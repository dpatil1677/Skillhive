<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewFeaturesTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_freelancer_payout_withdrawal_request_flow()
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'college' => 'IIT Bombay',
        ]);

        $client = User::factory()->create();

        $category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'icon' => 'bi-code',
            'description' => 'Web projects',
            'status' => 'active',
        ]);

        $service = Service::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Laravel Web App',
            'slug' => 'laravel-web-app',
            'description' => 'High quality Laravel development',
            'price' => 2000,
            'status' => 'active',
        ]);

        // Completed order so seller has ₹2000 earnings
        Order::create([
            'order_number' => 'SH-9901',
            'client_id' => $client->id,
            'freelancer_id' => $seller->id,
            'service_id' => $service->id,
            'package' => 'Standard',
            'price' => 2000,
            'platform_fee' => 100,
            'total_amount' => 2100,
            'status' => 'completed',
        ]);

        // 1. Visit Payouts Page
        $response = $this->actingAs($seller)->get(route('seller.payouts'));
        $response->assertStatus(200);
        $response->assertSee('₹2,000.00');

        // 2. Submit UPI Withdrawal Request
        $withdrawResponse = $this->actingAs($seller)->post(route('seller.payouts.request'), [
            'amount' => 1500,
            'method' => 'upi',
            'upi_id' => 'seller@okaxis',
        ]);

        $withdrawResponse->assertRedirect(route('seller.payouts'));
        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $seller->id,
            'amount' => 1500,
            'method' => 'upi',
            'status' => 'pending',
        ]);

        // 3. Reject over-withdrawal (remaining is 500, trying to withdraw 600)
        $rejectResponse = $this->actingAs($seller)->post(route('seller.payouts.request'), [
            'amount' => 600,
            'method' => 'upi',
            'upi_id' => 'seller@okaxis',
        ]);
        $rejectResponse->assertSessionHas('toast_error');
    }

    public function test_buyer_can_upgrade_to_freelancer_in_app()
    {
        $buyer = User::factory()->create([
            'role' => 'user',
            'college' => null,
        ]);

        $response = $this->actingAs($buyer)->post(route('profile.become-seller'), [
            'college' => 'BITS Pilani',
            'course' => 'B.E. Computer Science',
            'skills' => 'Flutter, Dart, Firebase',
            'bio' => 'Experienced app developer.',
        ]);

        $response->assertRedirect(route('seller.dashboard'));
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'role' => 'seller',
            'college' => 'BITS Pilani',
        ]);
    }

    public function test_user_can_upload_and_remove_custom_avatar()
    {
        $user = User::factory()->create([
            'profile_image' => null,
        ]);

        $file = UploadedFile::fake()->image('avatar.jpg', 300, 300);

        // Upload custom photo
        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'college' => $user->college,
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNotNull($user->profile_image);
        $this->assertStringContainsString('/storage/avatars/', $user->profile_image);

        // Remove custom photo
        $removeResponse = $this->actingAs($user)->post(route('profile.avatar.remove'));
        $removeResponse->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNull($user->profile_image);
    }

    public function test_buyer_can_cancel_order_with_reason()
    {
        $client = User::factory()->create();
        $freelancer = User::factory()->create(['role' => 'seller']);

        $category = Category::create([
            'name' => 'UI Design',
            'slug' => 'ui-design',
            'icon' => 'bi-palette',
            'status' => 'active',
        ]);

        $service = Service::create([
            'user_id' => $freelancer->id,
            'category_id' => $category->id,
            'title' => 'Mobile App UI',
            'slug' => 'mobile-app-ui',
            'description' => 'Figma UI',
            'price' => 1200,
            'status' => 'active',
        ]);

        $order = Order::create([
            'order_number' => 'SH-5512',
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'service_id' => $service->id,
            'package' => 'Basic',
            'price' => 1200,
            'platform_fee' => 60,
            'total_amount' => 1260,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($client)->post(route('orders.cancel', $order->order_number), [
            'cancellation_reason' => 'Duplicate order placed by mistake',
        ]);

        $response->assertSessionHas('toast_success');
        $order->refresh();
        $this->assertEquals('Cancelled', $order->status);
        $this->assertEquals('Duplicate order placed by mistake', $order->cancellation_reason);
        $this->assertEquals('client', $order->cancelled_by);
    }

    public function test_printable_tax_invoice_can_be_viewed()
    {
        $client = User::factory()->create();
        $freelancer = User::factory()->create(['role' => 'seller']);

        $category = Category::create([
            'name' => 'Writing',
            'slug' => 'writing',
            'icon' => 'bi-pen',
            'status' => 'active',
        ]);

        $service = Service::create([
            'user_id' => $freelancer->id,
            'category_id' => $category->id,
            'title' => 'Blog Writing',
            'slug' => 'blog-writing',
            'description' => 'SEO blog posts',
            'price' => 800,
            'status' => 'active',
        ]);

        $order = Order::create([
            'order_number' => 'SH-8821',
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'service_id' => $service->id,
            'package' => 'Standard',
            'price' => 800,
            'platform_fee' => 40,
            'total_amount' => 840,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($client)->get(route('orders.invoice', $order->order_number));
        $response->assertStatus(200);
        $response->assertSee('TAX INVOICE');
        $response->assertSee('SH-8821');
        $response->assertSee('₹840.00');
    }

    public function test_live_video_meeting_can_be_initiated()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create(['role' => 'seller']);

        $response = $this->actingAs($user1)->postJson(route('chat.meeting.start'), [
            'receiver_id' => $user2->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $json = $response->json();
        $this->assertStringContainsString('https://meet.jit.si/SkillHive-Meet-', $json['room_url']);
    }

    public function test_forgot_password_sends_email_with_reset_link()
    {
        \Illuminate\Support\Facades\Notification::fake();

        $user = User::factory()->create([
            'email' => 'student@college.edu',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => '  Student@College.EDU  ',
        ]);

        $response->assertSessionHas('status');

        \Illuminate\Support\Facades\Notification::assertSentTo(
            $user,
            \App\Notifications\ResetPasswordNotification::class,
            function ($notification) use ($user) {
                $mailData = $notification->toMail($user);
                return $mailData->subject === 'SkillHive — Reset Your Password';
            }
        );
    }

    public function test_order_completion_triggers_invoice_email_and_notification()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $client = User::factory()->create(['email' => 'client@test.com']);
        $freelancer = User::factory()->create(['role' => 'seller']);

        $category = Category::create([
            'name' => 'Video Editing',
            'slug' => 'video-editing',
            'icon' => 'bi-camera-reels',
            'status' => 'active',
        ]);

        $service = Service::create([
            'user_id' => $freelancer->id,
            'category_id' => $category->id,
            'title' => 'Reel Video Editing',
            'slug' => 'reel-video-editing',
            'description' => 'Viral short form videos',
            'price' => 1500,
            'status' => 'active',
        ]);

        $order = Order::create([
            'order_number' => 'SH-7711',
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'service_id' => $service->id,
            'package' => 'Standard',
            'price' => 1500,
            'platform_fee' => 75,
            'total_amount' => 1575,
            'status' => 'submitted',
        ]);

        // Client approves deliverables and completes order
        $response = $this->actingAs($client)->post(route('orders.update-status', $order->order_number), [
            'status' => 'Completed',
        ]);

        $response->assertSessionHas('toast_success');

        // Assert tax invoice email was dispatched to client
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\OrderInvoiceMail::class, function ($mail) use ($client, $order) {
            return $mail->hasTo($client->email) && $mail->order->id === $order->id;
        });

        // Assert in-app tax invoice notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $client->id,
            'title'   => "Tax Invoice Issued (#{$order->order_number})",
        ]);
    }
}
