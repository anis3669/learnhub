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
        // Base URL resolved from config/services.php (sandbox vs production).
        $this->khaltiBaseUrl = rtrim((string) config('services.khalti.base_url'), '/');
        $this->khaltiWebsiteUrl = (string) config('services.khalti.website_url');
    }

    /**
     * Step 1 - Initiate a Khalti ePayment (server-to-server POST).
     * Only free courses enroll immediately; Premium courses require payment.
     */
    public function initiate(Course $course)
    {
        $user = Auth::user();

        // Free courses never reach payment - enroll immediately.
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

        // Already enrolled (e.g. via a previously completed payment) -> just go to the course.
        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.course.show', $course)
                ->with('success', 'You are already enrolled in this course.');
        }

        // Reuse a still-pending payment for the same user+course instead of creating duplicates.
        $existingPayment = CoursePayment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($existingPayment) {
            $data = $this->lookup($existingPayment->pidx);

            // If the old pending payment actually completed meanwhile, finalize and continue.
            if ($data && ($data['status'] ?? null) === 'Completed') {
                return $this->finalizePayment($existingPayment, $data);
            }

            // Otherwise re-initiate to refresh the payment_url (old link may have expired).
            $existingPayment->delete();
        }

        // Convert the course price (NPR, decimal) to paisa (integer) for Khalti.
        $coursePriceNpr = (float) $course->price;
        $amount = (int) round($coursePriceNpr * 100); // paisa
        $purchaseOrderId = 'course_'.$course->id.'_user_'.$user->id.'_'.Str::random(8);

        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Key '.$this->khaltiSecretKey,
                'Content-Type' => 'application/json',
            ])->post($this->khaltiBaseUrl.'/epayment/initiate/', [
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
            ]);

            if ($response->failed()) {
                $body = $response->json() ?: ['raw' => $response->body()];

                Log::error('Khalti payment initiation failed', [
                    'http_status' => $response->status(),
                    'response' => $response->body(),
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                ]);

                // Surface the underlying Khalti error (e.g. "Invalid token") so the
                // cause is diagnosable instead of a generic failure message.
                $detail = is_array($body) ? (data_get($body, 'detail') ?? data_get($body, 'error') ?? '') : '';
                $message = 'Failed to initiate payment.';
                if ($detail) {
                    $message .= ' Gateway says: '.$detail;
                    // A 401 "Invalid token" almost always means the configured
                    // KHALTI_SECRET_KEY is not a real sandbox merchant key
                    // (it must come from https://test-admin.khalti.com).
                    if (stripos((string) $detail, 'invalid token') !== false) {
                        $message .= ' Check that KHALTI_SECRET_KEY is your real sandbox merchant key from test-admin.khalti.com.';
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
                'course_id' => $course->id,
                'user_id' => $user->id,
                'course_price_npr' => $coursePriceNpr,
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

    /**
     * Step 2 - Khalti redirects here after the user pays / cancels / lets it expire.
     * The pidx is taken from the query string and verified server-side via the lookup API.
     */
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

        // Forged/unauthorized callback protection: the pidx must belong to a real payment record.
        $payment = CoursePayment::where('pidx', $pidx)->first();

        if (! $payment) {
            Log::warning('Khalti callback referenced an unknown pidx - rejecting forged callback', ['pidx' => $pidx]);

            return redirect()->route('student.courses')
                ->with('error', 'Payment record not found.');
        }

        // Always re-verify server-side; never trust the callback query params alone.
        return $this->verifyPayment($payment);
    }

    /**
     * Manual verification entry point (unused by the normal flow, but kept for completeness).
     */
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

    /**
     * Step 3 - Server-side verification using the Khalti lookup API (POST /epayment/lookup/).
     */
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
            // Verify the amount Khalti actually collected matches what we charged (paisa).
            $returnedAmount = (int) ($data['total_amount'] ?? 0);
            $expectedAmount = (int) round((float) $payment->amount * 100); // paisa

            Log::info('Khalti payment amount verification', [
                'pidx' => $payment->pidx,
                'course_price_npr' => (float) $payment->amount,
                'expected_amount_paisa' => $expectedAmount,
                'returned_amount_paisa' => $returnedAmount,
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

        if ($status === 'refunded') {
            $payment->update(['status' => 'refunded']);

            Log::info('Khalti payment refunded', [
                'pidx' => $payment->pidx,
                'course_id' => $payment->course_id,
                'user_id' => $payment->user_id,
            ]);

            return redirect()->route('student.courses')
                ->with('error', 'This payment was refunded.');
        }

        // pending / initiated / any other non-terminal status.
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

    /**
     * Call Khalti lookup API and return the decoded response, or null on failure.
     */
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

    /**
     * Mark payment completed (idempotent) and create the enrollment - only after verification.
     */
    protected function finalizePayment(CoursePayment $payment, array $data)
    {
        return DB::transaction(function () use ($payment, $data) {
            if ($payment->status !== 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'paid_at' => now(),
                ]);

                Log::info('Khalti payment completed', [
                    'pidx' => $payment->pidx,
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'course_id' => $payment->course_id,
                    'user_id' => $payment->user_id,
                ]);
            }

            // Idempotent enrollment - never duplicate.
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
}
