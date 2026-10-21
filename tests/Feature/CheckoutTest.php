<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use DatabaseMigrations;

    protected User $buyer;
    protected User $seller;
    protected Service $javaService;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::firstOrCreate(['slug' => 'web-development'], [
            'name' => 'Web Development',
            'slug' => 'web-development',
            'icon' => 'bi-code-slash',
            'description' => 'Custom websites and web applications',
            'status' => 'active',
        ]);

        $this->seller = User::where('email', 'dpatil380@rku.ac.in')->first() 
            ?? User::factory()->create(['role' => 'seller', 'name' => 'Dhruv Patil']);

        $this->buyer = User::where('role', 'user')->first() 
            ?? User::factory()->create(['role' => 'user', 'name' => 'Test Buyer']);

        $this->javaService = Service::where('slug', 'java-project')->first()
            ?? Service::create([
                'user_id' => $this->seller->id,
                'category_id' => $this->category->id,
                'title' => 'JAVA PROJECT',
                'slug' => 'java-project',
                'description' => 'A comprehensive Java application with database backend.',
                'image' => 'https://placehold.co/900x540/e8e0f5/6D28D9?text=Java+Project',
                'price' => 499,
                'delivery_days' => 2,
                'revisions' => 1,
                'status' => 'active',
                'rating' => 5.0,
                'reviews_count' => 0,
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 499, 'delivery' => 2, 'revisions' => 1, 'features' => ['Basic deliverable', 'Source file included', '1 Revision']],
                    'standard' => ['name' => 'Standard', 'price' => 999, 'delivery' => 4, 'revisions' => 3, 'features' => ['Everything in Basic', 'Database setup', 'Admin dashboard', '3 Revisions']],
                    'premium' => ['name' => 'Premium', 'price' => 1999, 'delivery' => 7, 'revisions' => 5, 'features' => ['Full production app', 'Deployment help', 'Commercial license', '14 days support']],
                ],
            ]);
    }

    public function test_checkout_page_renders_cleanly_without_raw_json()
    {
        $response = $this->actingAs($this->buyer)->get(route('checkout', ['serviceSlug' => 'java-project', 'package' => 'standard']));

        $response->assertStatus(200);

        // Assert title, category and description appear
        $response->assertSee('JAVA PROJECT');
        $response->assertSee('Web Development');
        $response->assertSee('bi-code-slash');
        $response->assertSee(Str::limit(strip_tags($this->javaService->description), 100));

        // Assert NO raw JSON or serialized model strings appear in the rendered HTML
        $content = $response->getContent();
        $this->assertStringNotContainsString('{"id":1,"name":"Web Development"', $content);
        $this->assertStringNotContainsString('&quot;id&quot;:1,&quot;name&quot;:&quot;Web Development&quot;', $content);
        $this->assertStringNotContainsString('"slug":"web-development"', $content);
        $this->assertStringNotContainsString('&quot;slug&quot;:&quot;web-development&quot;', $content);
    }

    public function test_checkout_pricing_for_standard_package()
    {
        $response = $this->actingAs($this->buyer)->get(route('checkout', ['serviceSlug' => 'java-project', 'package' => 'standard']));

        $response->assertStatus(200);
        $response->assertSee('Subtotal (Standard)');
        $response->assertSee('₹999');
        $response->assertSee('₹50'); // Platform fee
        $response->assertSee('₹1,049'); // Total
        $response->assertSee('4 days after freelancer acceptance');
    }

    public function test_checkout_pricing_for_basic_package()
    {
        $response = $this->actingAs($this->buyer)->get(route('checkout', ['serviceSlug' => 'java-project', 'package' => 'basic']));

        $response->assertStatus(200);
        $response->assertSee('Subtotal (Basic)');
        $response->assertSee('₹499');
        $response->assertSee('₹50');
        $response->assertSee('₹549');
        $response->assertSee('2 days after freelancer acceptance');
    }

    public function test_checkout_pricing_for_premium_package()
    {
        $response = $this->actingAs($this->buyer)->get(route('checkout', ['serviceSlug' => 'java-project', 'package' => 'premium']));

        $response->assertStatus(200);
        $response->assertSee('Subtotal (Premium)');
        $response->assertSee('₹1,999');
        $response->assertSee('₹50');
        $response->assertSee('₹2,049');
        $response->assertSee('7 days after freelancer acceptance');
    }

    public function test_checkout_fallback_for_invalid_package_key()
    {
        // Should safely fallback to standard package
        $response = $this->actingAs($this->buyer)->get(route('checkout', ['serviceSlug' => 'java-project', 'package' => 'unsupported_tier']));

        $response->assertStatus(200);
        $response->assertSee('Subtotal (Standard)');
        $response->assertSee('₹999');
        $response->assertSee('₹1,049');
    }

    public function test_checkout_returns_404_for_invalid_service_slug()
    {
        $response = $this->actingAs($this->buyer)->get(route('checkout', ['serviceSlug' => 'non-existent-service-slug']));
        $response->assertStatus(404);
    }

    public function test_seller_cannot_purchase_own_service()
    {
        $response = $this->actingAs($this->seller)->get(route('checkout', ['serviceSlug' => 'java-project']));
        $response->assertRedirect(route('seller.dashboard'));
    }

    public function test_successful_order_processing_with_server_side_pricing()
    {
        $initialOrderCount = Order::count();

        $response = $this->actingAs($this->buyer)->post(route('checkout.process'), [
            'service_slug' => 'java-project',
            'package_key' => 'standard',
            'requirements' => 'Build a responsive Java Spring Boot and MySQL web application with modern REST APIs.',
        ]);

        $this->assertEquals($initialOrderCount + 1, Order::count());

        $order = Order::latest()->first();
        $this->assertEquals($this->buyer->id, $order->client_id);
        $this->assertEquals($this->javaService->user_id, $order->freelancer_id);
        $this->assertEquals($this->javaService->id, $order->service_id);
        $this->assertEquals('Standard', $order->package);
        $this->assertEquals(999, (int) $order->price);
        $this->assertEquals(50, (int) $order->platform_fee);
        $this->assertEquals(1049, (int) $order->total_amount);
        $this->assertEquals('Pending', $order->status);

        $response->assertRedirect(route('orders.show', ['orderNumber' => $order->order_number]));
    }
}
