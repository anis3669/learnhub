<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePayment;
use App\Models\Enrollment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CoursePaymentController extends Controller
{
    protected string $khaltiSecretKey;

    protected string $khaltiBaseUrl;

    protected string $khaltiWebsiteUrl;

    public function __construct()
    {
        $this->khaltiSecretKey = (string) config('services.khalti.secret');
        $this->khaltiBaseUrl = rtrim((string) config('services.khalti.base_url'), '/');
        $this->khaltiWebsiteUrl = (string) config('services.khalti.website_url');
    }

    public function initiate(Course $course)
    {
        $user = Auth::user();

        if (! $course->requiresPayment()) {
            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['progress_percent' => 0]
            );

            return redirect()->route('student.course.show', $course)
                ->with('success', "Enrolled in \"{$course->title}\" successfully!");
        }

        if (! $user->phone) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please add your phone number in your profile before making a payment.');
        }

        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.course.show', $course)
                ->with('success', 'You are already enrolled in this course.');
        }

        $existingPayment = CoursePayment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($existingPayment) {
            $data = $this->lookup($existingPayment->pidx);

            if ($data && ($data['status'] ?? null) === 'Completed') {
                return $this->finalizePayment($existingPayment, $data);
            }

            $existingPayment->delete();
        }

        $amount = $this->toPaisa($course->price);

        if ($amount < (int) config('services.khalti.min_amount_paisa', 1000)) {
            return redirect()->route('student.courses')
                ->with('error', 'Minimum payment amount is Rs. 10.');
        }

        if (! filter_var($this->khaltiWebsiteUrl, FILTER_VALIDATE_URL)) {
            Log::error('Khalti website_url is not a valid URL', [
                'website_url' => $this->khaltiWebsiteUrl,
            ]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment gateway is misconfigured. Please contact support.');
        }

        if (! config('services.khalti.sandbox') && $this->isLocalhost($this->khaltiWebsiteUrl)) {
            Log::warning('Khalti website_url is localhost in production mode', [
                'website_url' => $this->khaltiWebsiteUrl,
            ]);
        }

        $purchaseOrderId = 'course_'.$course->id.'_user_'.$user->id.'_'.Str::random(8);

        $payload = [
            'return_url' => route('student.payment.callback'),
            'website_url' => $this->khaltiWebsiteUrl,
            'amount' => $amount,
            'purchase_order_id' => $purchaseOrderId,
            'purchase_order_name' => $course->title,
            'customer_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'merchant_extra' => json_encode([
                'course_id' => $course->id,
                'user_id' => $user->id,
            ]),
            'merchant_username' => config('app.name', 'LearnHub'),
        ];

        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Key '.$this->khaltiSecretKey,
                'Content-Type' => 'application/json',
            ])->post($this->khaltiBaseUrl.'/epayment/initiate/', $payload);

            if ($response->failed()) {
                $body = $response->json() ?: ['raw' => $response->body()];

                Log::error('Khalti payment initiation failed', [
                    'http_status' => $response->status(),
                    'response' => $response->body(),
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                ]);

                $detail = is_array($body) ? (data_get($body, 'detail') ?? data_get($body, 'error') ?? '') : '';
                $message = 'Failed to initiate payment.';
                if ($detail) {
                    $message .= ' Gateway says: '.$detail;
                    if (stripos((string) $detail, 'invalid token') !== false) {
                        $message .= ' Check that KHALTI_SECRET_KEY is your real merchant key.';
                    }
                }

                return redirect()->route('student.courses')
                    ->with('error', $message);
            }

            $data = $response->json();

            if (empty($data['pidx']) || empty($data['payment_url'])) {
                Log::error('Khalti initiate returned an unexpected payload', [
                    'response' => $response->body(),
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ]);

                return redirect()->route('student.courses')
                    ->with('error', 'Invalid response from payment gateway. Please try again.');
            }

            CoursePayment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $course->price,
                'payment_gateway' => 'khalti',
                'purchase_order_id' => $purchaseOrderId,
                'pidx' => $data['pidx'],
                'status' => 'pending',
            ]);

            Log::info('Khalti payment initiated', [
                'pidx' => $data['pidx'],
                'purchase_order_id' => $purchaseOrderId,
                'course_id' => $course->id,
                'user_id' => $user->id,
                'course_price_npr' => $course->price,
                'amount_sent_paisa' => $amount,
            ]);

            return redirect()->away($data['payment_url']);
        } catch (Exception $e) {
            Log::error('Khalti payment initiation exception', [
                'message' => $e->getMessage(),
                'course_id' => $course->id,
                'user_id' => $user->id,
            ]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment gateway error. Please try again later.');
        }
    }

    public function callback(Request $request)
    {
        $pidx = $request->query('pidx') ?? $request->input('pidx');

        Log::info('Khalti callback received', [
            'pidx' => $pidx,
            'query_params' => $request->query(),
        ]);

        if (! $pidx) {
            Log::warning('Khalti callback received without pidx');

            return redirect()->route('student.courses')
                ->with('error', 'Invalid payment response received.');
        }

        $payment = CoursePayment::where('pidx', $pidx)->first();

        if (! $payment) {
            Log::warning('Khalti callback referenced an unknown pidx - rejecting forged callback', ['pidx' => $pidx]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment record not found.');
        }

        $callbackPurchaseOrderId = $request->query('purchase_order_id');
        if ($callbackPurchaseOrderId && $callbackPurchaseOrderId !== $payment->purchase_order_id) {
            Log::warning('Khalti callback purchase_order_id mismatch', [
                'pidx' => $pidx,
                'expected' => $payment->purchase_order_id,
                'received' => $callbackPurchaseOrderId,
            ]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment record not found.');
        }

        return $this->verifyPayment($payment);
    }

    public function verify(Request $request)
    {
        $pidx = $request->query('pidx');

        if (! $pidx) {
            return redirect()->route('student.courses')
                ->with('error', 'No payment reference provided.');
        }

        $payment = CoursePayment::where('pidx', $pidx)->first();

        if (! $payment) {
            return redirect()->route('student.courses')
                ->with('error', 'Payment record not found.');
        }

        return $this->verifyPayment($payment);
    }

    protected function verifyPayment(CoursePayment $payment)
    {
        $data = $this->lookup($payment->pidx);

        if ($data === null) {
            return redirect()->route('student.courses')
                ->with('error', 'Payment verification failed. Please contact support.');
        }

        if (! isset($data['status'])) {
            $payment->update(['status' => 'failed']);

            Log::error('Khalti lookup response missing status field', [
                'pidx' => $payment->pidx,
                'course_id' => $payment->course_id,
                'user_id' => $payment->user_id,
            ]);

            return redirect()->route('student.courses')
                ->with('error', 'Invalid payment response.');
        }

        $status = strtolower($data['status']);

        if ($status === 'completed') {
            $returnedAmount = (int) ($data['total_amount'] ?? 0);
            $expectedAmount = $this->toPaisa($payment->amount);

            Log::info('Khalti payment amount verification', [
                'pidx' => $payment->pidx,
                'course_price_npr' => (float) $payment->amount,
                'expected_amount_paisa' => $expectedAmount,
                'returned_amount_paisa' => $returnedAmount,
                'fee_paisa' => $data['fee'] ?? null,
                'refunded' => $data['refunded'] ?? null,
            ]);

            if ($returnedAmount !== $expectedAmount) {
                $payment->update(['status' => 'failed']);

                Log::error('Khalti payment amount mismatch - payment rejected', [
                    'pidx' => $payment->pidx,
                    'expected_amount_paisa' => $expectedAmount,
                    'returned_amount_paisa' => $returnedAmount,
                    'course_id' => $payment->course_id,
                    'user_id' => $payment->user_id,
                ]);

                return redirect()->route('student.courses')
                    ->with('error', 'Payment amount mismatch. The transaction was rejected for security reasons. Please contact support.');
            }

            if (! empty($data['refunded'])) {
                $payment->update([
                    'status' => 'refunded',
                    'transaction_id' => $data['transaction_id'] ?? $payment->transaction_id,
                ]);

                Log::info('Khalti payment refunded after lookup', [
                    'pidx' => $payment->pidx,
                    'course_id' => $payment->course_id,
                    'user_id' => $payment->user_id,
                ]);

                return redirect()->route('student.courses')
                    ->with('error', 'This payment was refunded.');
            }

            return $this->finalizePayment($payment, $data);
        }

        if ($status === 'user canceled' || $status === 'expired') {
            $payment->update(['status' => 'cancelled']);

            Log::info('Khalti payment cancelled/expired', [
                'pidx' => $payment->pidx,
                'status' => $data['status'],
                'course_id' => $payment->course_id,
                'user_id' => $payment->user_id,
            ]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment was cancelled or expired. Please try again.');
        }

        if ($status === 'refunded' || $status === 'partially refunded') {
            $payment->update(['status' => 'refunded']);

            Log::info('Khalti payment refunded', [
                'pidx' => $payment->pidx,
                'status' => $data['status'],
                'course_id' => $payment->course_id,
                'user_id' => $payment->user_id,
            ]);

            return redirect()->route('student.courses')
                ->with('error', 'This payment was refunded.');
        }

        $payment->update(['status' => $status]);

        if ($status === 'failed') {
            Log::warning('Khalti payment failed', [
                'pidx' => $payment->pidx,
                'course_id' => $payment->course_id,
                'user_id' => $payment->user_id,
            ]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment failed. Please try again.');
        }

        return redirect()->route('student.courses')
            ->with('error', 'Payment is still '.$data['status'].'. Please try again shortly.');
    }

    protected function lookup(string $pidx): ?array
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Key '.$this->khaltiSecretKey,
                'Content-Type' => 'application/json',
            ])->post($this->khaltiBaseUrl.'/epayment/lookup/', [
                'pidx' => $pidx,
            ]);

            if ($response->failed()) {
                Log::error('Khalti payment lookup failed', [
                    'pidx' => $pidx,
                    'http_status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            Log::info('Khalti payment lookup response', [
                'pidx' => $pidx,
                'status' => $data['status'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
                'returned_amount_paisa' => $data['total_amount'] ?? null,
                'fee_paisa' => $data['fee'] ?? null,
                'refunded' => $data['refunded'] ?? null,
            ]);

            return $data;
        } catch (Exception $e) {
            Log::error('Khalti payment lookup exception', [
                'pidx' => $pidx,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function finalizePayment(CoursePayment $payment, array $data)
    {
        return DB::transaction(function () use ($payment, $data) {
            $payment = $payment->lockForUpdate()->firstOrFail();

            if ($payment->status !== 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $data['transaction_id'] ?? $payment->transaction_id,
                    'paid_at' => now(),
                ]);

                Log::info('Khalti payment completed', [
                    'pidx' => $payment->pidx,
                    'transaction_id' => $payment->transaction_id,
                    'course_id' => $payment->course_id,
                    'user_id' => $payment->user_id,
                ]);
            }

            $enrollment = Enrollment::firstOrCreate(
                ['user_id' => $payment->user_id, 'course_id' => $payment->course_id],
                ['progress_percent' => 0]
            );

            Log::info('Enrollment created via verified payment', [
                'enrollment_id' => $enrollment->id,
                'course_id' => $payment->course_id,
                'user_id' => $payment->user_id,
            ]);

            return redirect()->route('student.course.show', $payment->course_id)
                ->with('success', 'Payment successful! You are now enrolled in the course.');
        });
    }

    private function toPaisa(float|int $npr): int
    {
        return (int) round((float) $npr * 100);
    }

    private function isLocalhost(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        return in_array($host, ['localhost', '127.0.0.1', '0.0.0.0'], true);
    }
}
