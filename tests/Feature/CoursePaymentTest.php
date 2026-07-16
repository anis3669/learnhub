<?php

use App\Models\Course;
use App\Models\CoursePayment;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'student']);
    Role::firstOrCreate(['name' => 'teacher']);
    Role::firstOrCreate(['name' => 'admin']);

    $this->student = User::factory()->create([
        'phone' => '9800000001',
        'email_verified_at' => now(),
    ]);
    $this->student->assignRole('student');
});

it('enrolls a free course immediately without payment', function () {
    $course = Course::factory()->create(['category' => 'Programming', 'price' => 0]);

    $this->actingAs($this->student)
        ->post(route('student.enroll', $course))
        ->assertRedirect(route('student.course.show', $course));

    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeTrue();
    expect(CoursePayment::count())->toBe(0);
});

it('initiates khalti payment for a premium course and redirects to payment_url', function () {
    Http::fake([
        '*/epayment/initiate/*' => Http::response([
            'pidx' => 'TEST_PIDX_123',
            'payment_url' => 'https://pay.khalti.com/TEST_PIDX_123',
        ], 200),
    ]);

    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);

    $this->actingAs($this->student)
        ->post(route('student.course.payment.initiate', $course))
        ->assertRedirect('https://pay.khalti.com/TEST_PIDX_123');

    // Enrollment must NOT exist yet - only after verification.
    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeFalse();
    expect(CoursePayment::where('pidx', 'TEST_PIDX_123')->where('status', 'pending')->exists())->toBeTrue();
});

it('creates enrollment only after successful server-side verification', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    $payment = CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00,
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_1',
        'pidx' => 'TEST_PIDX_123',
        'status' => 'pending',
    ]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'TEST_PIDX_123',
            'status' => 'Completed',
            'transaction_id' => 'TXN_999',
            'total_amount' => 10000,
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'TEST_PIDX_123']))
        ->assertRedirect(route('student.course.show', $course));

    expect($payment->refresh()->status)->toBe('completed');
    expect($payment->transaction_id)->toBe('TXN_999');
    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeTrue();
});

it('does not create duplicate enrollments on callback refresh', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    $payment = CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00,
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_2',
        'pidx' => 'TEST_PIDX_456',
        'status' => 'completed',
        'transaction_id' => 'TXN_OLD',
        'paid_at' => now(),
    ]);
    Enrollment::create(['user_id' => $this->student->id, 'course_id' => $course->id, 'progress_percent' => 0]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'TEST_PIDX_456',
            'status' => 'Completed',
            'transaction_id' => 'TXN_NEW',
            'total_amount' => 10000,
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'TEST_PIDX_456']));

    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->count())->toBe(1);
    // Keeps original transaction id (idempotent update skipped).
    expect($payment->refresh()->transaction_id)->toBe('TXN_OLD');
});

it('handles cancelled payment without enrolling', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00,
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_3',
        'pidx' => 'TEST_PIDX_789',
        'status' => 'pending',
    ]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'TEST_PIDX_789',
            'status' => 'User canceled',
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'TEST_PIDX_789']))
        ->assertRedirect(route('student.courses'));

    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeFalse();
});

it('rejects forged callback with unknown pidx', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'FORGED_PIDX']))
        ->assertRedirect(route('student.courses'));

    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeFalse();
});

it('blocks direct premium course access without completed payment', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    // Force an enrollment row without a completed payment.
    Enrollment::create(['user_id' => $this->student->id, 'course_id' => $course->id, 'progress_percent' => 0]);

    $this->actingAs($this->student)
        ->get(route('student.course.show', $course))
        ->assertRedirect(route('student.courses'));

    // With a completed payment, access is granted.
    CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00,
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_4',
        'pidx' => 'TEST_PIDX_ACCESS',
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.course.show', $course))
        ->assertOk();
});

it('converts 9.99 NPR to 999 paisa when initiating', function () {
    Http::fake([
        '*/epayment/initiate/*' => Http::response([
            'pidx' => 'PIDX_999',
            'payment_url' => 'https://pay.khalti.com/PIDX_999',
        ], 200),
    ]);

    $course = Course::factory()->create(['category' => 'Premium', 'price' => 9.99]);

    $this->actingAs($this->student)
        ->post(route('student.course.payment.initiate', $course))
        ->assertRedirect('https://pay.khalti.com/PIDX_999');

    Http::assertSent(function ($request) {
        return $request->url() === config('services.khalti.base_url').'/epayment/initiate/'
            && $request['amount'] === 999; // 9.99 * 100
    });

    // DB stores the amount in NPR (decimal), not paisa.
    $payment = CoursePayment::where('pidx', 'PIDX_999')->first();
    expect((float) $payment->amount)->toBe(9.99);
});

it('converts 100 NPR to 10000 paisa when initiating', function () {
    Http::fake([
        '*/epayment/initiate/*' => Http::response([
            'pidx' => 'PIDX_10000',
            'payment_url' => 'https://pay.khalti.com/PIDX_10000',
        ], 200),
    ]);

    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);

    $this->actingAs($this->student)
        ->post(route('student.course.payment.initiate', $course))
        ->assertRedirect('https://pay.khalti.com/PIDX_10000');

    Http::assertSent(function ($request) {
        return $request['amount'] === 10000; // 100 * 100
    });
});

it('rejects verification when returned amount does not match expected', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    $payment = CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00, // NPR -> expected 10000 paisa
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_mismatch',
        'pidx' => 'PIDX_MISMATCH',
        'status' => 'pending',
    ]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'PIDX_MISMATCH',
            'status' => 'Completed',
            'transaction_id' => 'TXN_MISMATCH',
            'total_amount' => 5000, // tampered / wrong amount
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'PIDX_MISMATCH']))
        ->assertRedirect(route('student.courses'));

    // Payment must be marked failed and NO enrollment created.
    expect($payment->refresh()->status)->toBe('failed');
    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeFalse();
});

it('completes verification with a matching returned amount', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 9.99]);
    $payment = CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 9.99, // expected 999 paisa
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_match',
        'pidx' => 'PIDX_MATCH',
        'status' => 'pending',
    ]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'PIDX_MATCH',
            'status' => 'Completed',
            'transaction_id' => 'TXN_MATCH',
            'total_amount' => 999, // exactly matches
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'PIDX_MATCH']))
        ->assertRedirect(route('student.course.show', $course));

    expect($payment->refresh()->status)->toBe('completed');
    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeTrue();
});

it('handles failed payment without enrolling', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00,
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_failed',
        'pidx' => 'TEST_PIDX_FAILED',
        'status' => 'pending',
    ]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'TEST_PIDX_FAILED',
            'status' => 'Failed',
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'TEST_PIDX_FAILED']))
        ->assertRedirect(route('student.courses'));

    expect(CoursePayment::where('pidx', 'TEST_PIDX_FAILED')->first()->status)->toBe('failed');
    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeFalse();
});

it('handles expired payment without enrolling', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00,
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_expired',
        'pidx' => 'TEST_PIDX_EXPIRED',
        'status' => 'pending',
    ]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'TEST_PIDX_EXPIRED',
            'status' => 'Expired',
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'TEST_PIDX_EXPIRED']))
        ->assertRedirect(route('student.courses'));

    expect(CoursePayment::where('pidx', 'TEST_PIDX_EXPIRED')->first()->status)->toBe('cancelled');
    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeFalse();
});

it('treats an unknown pidx as a forged callback', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'DOES_NOT_EXIST']))
        ->assertRedirect(route('student.courses'));

    expect(CoursePayment::count())->toBe(0);
    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->exists())->toBeFalse();
});

it('is idempotent on a duplicate callback for an already-completed payment', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    $payment = CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00,
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_dup',
        'pidx' => 'TEST_PIDX_DUP',
        'status' => 'completed',
        'transaction_id' => 'TXN_DUP',
        'paid_at' => now(),
    ]);
    Enrollment::create(['user_id' => $this->student->id, 'course_id' => $course->id, 'progress_percent' => 0]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'TEST_PIDX_DUP',
            'status' => 'Completed',
            'transaction_id' => 'TXN_DUP_NEW',
            'total_amount' => 10000,
        ], 200),
    ]);

    // Fire the callback twice to simulate Khalti retry / user refresh.
    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'TEST_PIDX_DUP']))
        ->assertRedirect(route('student.course.show', $course));
    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'TEST_PIDX_DUP']))
        ->assertRedirect(route('student.course.show', $course));

    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->count())->toBe(1);
    expect(CoursePayment::where('pidx', 'TEST_PIDX_DUP')->count())->toBe(1);
    // Idempotent update skipped: original transaction id preserved.
    expect($payment->refresh()->transaction_id)->toBe('TXN_DUP');
});

it('does not create a duplicate enrollment when one already exists', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);
    $payment = CoursePayment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'amount' => 100.00,
        'payment_gateway' => 'khalti',
        'purchase_order_id' => 'order_dup_enroll',
        'pidx' => 'TEST_PIDX_DUP_ENROLL',
        'status' => 'pending',
    ]);
    Enrollment::create(['user_id' => $this->student->id, 'course_id' => $course->id, 'progress_percent' => 0]);

    Http::fake([
        '*/epayment/lookup/*' => Http::response([
            'pidx' => 'TEST_PIDX_DUP_ENROLL',
            'status' => 'Completed',
            'transaction_id' => 'TXN_DUP_ENROLL',
            'total_amount' => 10000,
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->get(route('student.payment.callback', ['pidx' => 'TEST_PIDX_DUP_ENROLL']))
        ->assertRedirect(route('student.course.show', $course));

    expect(Enrollment::where('user_id', $this->student->id)->where('course_id', $course->id)->count())->toBe(1);
    expect($payment->refresh()->status)->toBe('completed');
});

it('does not create a duplicate pending payment when one already exists', function () {
    $course = Course::factory()->create(['category' => 'Premium', 'price' => 100.00]);

    Http::fake([
        '*/epayment/initiate/*' => Http::response([
            'pidx' => 'PIDX_FIRST',
            'payment_url' => 'https://pay.khalti.com/PIDX_FIRST',
        ], 200),
    ]);

    $this->actingAs($this->student)
        ->post(route('student.course.payment.initiate', $course))
        ->assertRedirect('https://pay.khalti.com/PIDX_FIRST');

    // Second attempt should reuse the pending payment (no new HTTP call), not create a duplicate.
    $this->actingAs($this->student)
        ->post(route('student.course.payment.initiate', $course))
        ->assertRedirect('https://pay.khalti.com/PIDX_FIRST');

    expect(CoursePayment::where('user_id', $this->student->id)
        ->where('course_id', $course->id)
        ->where('status', 'pending')->count())->toBe(1);
});
