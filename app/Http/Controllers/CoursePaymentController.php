<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePayment;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class CoursePaymentController extends Controller
{
    protected string $khaltiSecretKey;
    protected string $khaltiBaseUrl;

    public function __construct()
    {
        $this->khaltiSecretKey = config('services.khalti.secret');
        $this->khaltiBaseUrl = config('services.khalti.sandbox', true)
            ? 'https://dev.khalti.com/api/v2'
            : 'https://a.khalti.com/api/v2';
    }

    public function initiate(Course $course)
    {
        $user = Auth::user();

        if (!$course->requiresPayment()) {
            return redirect()->route('student.course.show', $course)
                ->with('error', 'This course does not require payment.');
        }

        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)->first();
        if ($existingEnrollment) {
            return redirect()->route('student.course.show', $course)
                ->with('success', 'You are already enrolled in this course.');
        }

        $existingPayment = CoursePayment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['pending', 'completed'])
            ->first();

        if ($existingPayment && $existingPayment->status === 'completed') {
            return redirect()->route('student.course.show', $course)
                ->with('success', 'Payment already completed for this course.');
        }

        $purchaseOrderId = 'course_' . $course->id . '_user_' . $user->id . '_' . Str::random(8);
        $amount = (int)($course->price * 100);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->khaltiSecretKey,
                'Content-Type' => 'application/json',
            ])->post($this->khaltiBaseUrl . '/epayment/initiate/', [
                'return_url' => route('student.payment.callback'),
                'website_url' => config('app.url'),
                'amount' => $amount,
                'purchase_order_id' => $purchaseOrderId,
                'purchase_order_name' => $course->title,
                'customer_info' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                ],
            ]);

            if ($response->failed()) {
                Log::error('Khalti payment initiation failed', [
                    'response' => $response->body(),
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ]);
                return redirect()->route('student.courses')
                    ->with('error', 'Failed to initiate payment. Please try again.');
            }

            $data = $response->json();

            $payment = CoursePayment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $course->price,
                'payment_gateway' => 'khalti',
                'purchase_order_id' => $purchaseOrderId,
                'pidx' => $data['pidx'],
                'status' => 'pending',
            ]);

            if (!isset($data['payment_url'])) {
                throw new Exception('Invalid response from Khalti: missing payment_url');
            }

            return redirect()->away($data['payment_url']);

        } catch (Exception $e) {
            Log::error('Khalti payment initiation error', ['message' => $e->getMessage()]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment gateway error. Please try again later.');
        }
    }

    public function callback(Request $request)
    {
        $pidx = $request->query('pidx') ?? $request->input('pidx');

        if (!$pidx) {
            return redirect()->route('student.courses')
                ->with('error', 'Invalid payment response received.');
        }

        $payment = CoursePayment::where('pidx', $pidx)->first();

        if (!$payment) {
            return redirect()->route('student.courses')
                ->with('error', 'Payment record not found.');
        }

        if ($payment->status === 'completed') {
            return redirect()->route('student.course.show', $payment->course_id)
                ->with('success', 'Payment already processed. You are enrolled in the course.');
        }

        return $this->verifyPayment($pidx, $payment);
    }

    public function verify(Request $request)
    {
        $pidx = $request->query('pidx');

        if (!$pidx) {
            return redirect()->route('student.courses')
                ->with('error', 'No payment reference provided.');
        }

        $payment = CoursePayment::where('pidx', $pidx)->firstOrFail();

        return $this->verifyPayment($pidx, $payment);
    }

    protected function verifyPayment(string $pidx, CoursePayment $payment)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->khaltiSecretKey,
                'Content-Type' => 'application/json',
            ])->get($this->khaltiBaseUrl . '/epayment/lookup/', [
                'pidx' => $pidx,
            ]);

            if ($response->failed()) {
                Log::error('Khalti payment lookup failed', [
                    'pidx' => $pidx,
                    'response' => $response->body(),
                ]);
                $payment->update(['status' => 'failed']);

                return redirect()->route('student.courses')
                    ->with('error', 'Payment verification failed. Please contact support.');
            }

            $data = $response->json();

            if (!isset($data['status'])) {
                $payment->update(['status' => 'failed']);
                return redirect()->route('student.courses')
                    ->with('error', 'Invalid payment response.');
            }

            $status = strtolower($data['status']);

            if ($status === 'completed') {
                return DB::transaction(function () use ($payment, $data) {
                    if ($payment->status !== 'completed') {
                        $payment->update([
                            'status' => 'completed',
                            'transaction_id' => $data['transaction_id'] ?? null,
                        ]);

                        Enrollment::firstOrCreate(
                            ['user_id' => $payment->user_id, 'course_id' => $payment->course_id],
                            ['progress_percent' => 0]
                        );
                    }

                    return redirect()->route('student.course.show', $payment->course_id)
                        ->with('success', 'Payment successful! You are now enrolled in the course.');
                });
            }

            if ($status === 'user canceled' || $status === 'expired') {
                $payment->update(['status' => 'cancelled']);
                return redirect()->route('student.courses')
                    ->with('error', 'Payment was cancelled or expired. Please try again.');
            }

            $payment->update(['status' => $status]);

            if ($status === 'failed') {
                return redirect()->route('student.courses')
                    ->with('error', 'Payment failed. Please try again.');
            }

            return redirect()->route('student.courses')
                ->with('error', 'Payment status: ' . $data['status'] . '. Please try again.');

        } catch (Exception $e) {
            Log::error('Khalti payment verification error', ['message' => $e->getMessage()]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment verification error. Please contact support.');
        }
    }
}
