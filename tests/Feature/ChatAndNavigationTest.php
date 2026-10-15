<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ChatAndNavigationTest extends TestCase
{
    use DatabaseMigrations;
    protected User $admin;
    protected User $seller;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin  = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'email' => 'admin_test@skillhive.edu.in']);
        $this->seller = User::where('role', 'seller')->first() ?? User::factory()->create(['role' => 'seller', 'email' => 'seller_test@skillhive.edu.in']);
        $this->client = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user', 'email' => 'client_test@skillhive.edu.in']);
    }

    /**
     * Test navigation: Guests redirected to login from chat.
     */
    public function test_guests_cannot_access_chat(): void
    {
        $response = $this->get('/chat');
        $response->assertRedirect('/login');
    }

    /**
     * Test navigation: Authenticated client can access /chat.
     */
    public function test_authenticated_user_can_access_chat(): void
    {
        $response = $this->actingAs($this->client)->get('/chat');
        $response->assertStatus(200);
        $response->assertSee('Messages');
    }

    /**
     * Test navigation: Landing page does not forcibly redirect authenticated users away.
     */
    public function test_home_page_accessible_to_authenticated_users(): void
    {
        $response = $this->actingAs($this->client)->get('/');
        $response->assertStatus(200);

        $responseAdmin = $this->actingAs($this->admin)->get('/');
        $responseAdmin->assertStatus(200);
    }

    /**
     * Test chat: Client can send message to freelancer via AJAX.
     */
    public function test_client_can_send_message_to_freelancer(): void
    {
        $text = 'Test message from client at ' . now()->toDateTimeString();

        $response = $this->actingAs($this->client)->postJson('/chat/send', [
            'receiver_id' => $this->seller->id,
            'message'     => $text,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => [
                'sender' => 'me',
                'text'   => $text,
            ],
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id'   => $this->client->id,
            'receiver_id' => $this->seller->id,
            'message'     => $text,
        ]);
    }

    /**
     * Test chat: Cannot message oneself.
     */
    public function test_user_cannot_message_themselves(): void
    {
        $response = $this->actingAs($this->client)->postJson('/chat/send', [
            'receiver_id' => $this->client->id,
            'message'     => 'Self message',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test chat: Freelancer receives new message automatically via polling.
     */
    public function test_freelancer_polls_and_receives_new_message(): void
    {
        // 1. Create a fresh message from client to freelancer
        $msg = Message::create([
            'sender_id'   => $this->client->id,
            'receiver_id' => $this->seller->id,
            'message'     => 'Real-time test ping',
            'is_read'     => false,
        ]);

        // 2. Poll as freelancer with since = msg->id - 1
        $response = $this->actingAs($this->seller)
            ->getJson('/chat/poll?with=' . $this->client->id . '&since=' . ($msg->id - 1));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'messages',
            'last_msg_id',
            'conversations',
            'session_user_id',
        ]);

        $json = $response->json();
        $this->assertTrue(collect($json['messages'])->contains('id', $msg->id));

        // 3. Verify message is marked as read after being polled by recipient
        $this->assertTrue((bool) $msg->fresh()->is_read);
    }

    /**
     * Test chat: Asynchronously fetch full conversation history without full page reload.
     */
    public function test_fetch_messages_history_endpoint(): void
    {
        $response = $this->actingAs($this->client)
            ->getJson('/chat/messages?with=' . $this->seller->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'partner' => ['id', 'name', 'avatar', 'role'],
            'messages',
            'last_msg_id',
        ]);
    }

    /**
     * Test admin permissions: Non-admin cannot access admin panel.
     */
    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->client)->get('/admin/dashboard');
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test admin permissions: Admin can access admin panel.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Test seller permissions: Client cannot access seller dashboard.
     */
    public function test_client_cannot_access_seller_dashboard(): void
    {
        $response = $this->actingAs($this->client)->get('/seller/dashboard');
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test seller permissions: Seller can access seller dashboard.
     */
    public function test_seller_can_access_seller_dashboard(): void
    {
        $response = $this->actingAs($this->seller)->get('/seller/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Test cross-tab session restoration: Admin in impersonation mode is not kicked out of /admin/*
     */
    public function test_admin_impersonation_allows_admin_route_restoration(): void
    {
        // Simulate session having admin_impersonator_id while logged in as client
        $response = $this->actingAs($this->client)
            ->withSession(['admin_impersonator_id' => $this->admin->id])
            ->get('/admin/dashboard');

        // Should NOT be redirected to /dashboard; should restore admin and return 200
        $response->assertStatus(200);
    }
}
