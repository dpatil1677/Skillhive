<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use DatabaseMigrations;

    public function test_buyer_registration_succeeds_and_normalizes_email()
    {
        $response = $this->post(route('register.process'), [
            'name'                  => '  Aarav Mehta  ',
            'email'                 => '  Aarav.Mehta@Example.COM  ',
            'account_type'          => 'buyer',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            'terms'                 => '1',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'name'  => 'Aarav Mehta',
            'email' => 'aarav.mehta@example.com',
            'role'  => 'user',
        ]);

        $this->assertAuthenticated();
    }

    public function test_seller_registration_requires_college()
    {
        $response = $this->from(route('register'))->post(route('register.process'), [
            'name'                  => 'Neha Sharma',
            'email'                 => 'neha@college.edu',
            'account_type'          => 'seller',
            'college'               => '', // Missing college
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            'terms'                 => '1',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors(['college']);
        $this->assertGuest();
    }

    public function test_seller_registration_succeeds_with_college()
    {
        $response = $this->post(route('register.process'), [
            'name'                  => 'Neha Sharma',
            'email'                 => 'neha@college.edu',
            'account_type'          => 'seller',
            'college'               => 'NID Ahmedabad',
            'course'                => 'BDes Animation',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            'terms'                 => '1',
        ]);

        $response->assertRedirect(route('seller.dashboard'));

        $this->assertDatabaseHas('users', [
            'name'    => 'Neha Sharma',
            'email'   => 'neha@college.edu',
            'role'    => 'seller',
            'college' => 'NID Ahmedabad',
        ]);

        $this->assertAuthenticated();
    }

    public function test_registration_requires_terms_acceptance()
    {
        $response = $this->from(route('register'))->post(route('register.process'), [
            'name'                  => 'Karan Patel',
            'email'                 => 'karan@gmail.com',
            'account_type'          => 'buyer',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            // 'terms' omitted
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors(['terms']);
        $this->assertGuest();
    }

    public function test_registration_rejects_duplicate_email_regardless_of_case()
    {
        User::factory()->create([
            'email' => 'existing@gmail.com',
        ]);

        $response = $this->from(route('register'))->post(route('register.process'), [
            'name'                  => 'Another User',
            'email'                 => 'Existing@Gmail.Com',
            'account_type'          => 'buyer',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            'terms'                 => '1',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_normalizes_email_and_authenticates()
    {
        User::factory()->create([
            'name'     => 'Rohan Joshi',
            'email'    => 'rohan@mitpune.edu.in',
            'password' => Hash::make('MySecurePass123!'),
            'role'     => 'user',
        ]);

        // Login with uppercase letters and spaces
        $response = $this->post(route('login.process'), [
            'email'    => '  Rohan@Mitpune.Edu.In  ',
            'password' => 'MySecurePass123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_rate_limiting_after_excessive_failed_attempts()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.process'), [
                'email'    => 'victim@gmail.com',
                'password' => 'WrongPassword',
            ]);
        }

        // 6th attempt should trigger rate limiting
        $response = $this->post(route('login.process'), [
            'email'    => 'victim@gmail.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $errors = session('errors')->get('email');
        $this->assertStringContainsString('Too many login attempts', $errors[0]);
    }

    public function test_buyer_can_update_profile_without_college()
    {
        $buyer = User::factory()->create([
            'name'    => 'Client User',
            'email'   => 'client@gmail.com',
            'role'    => 'user',
            'college' => null,
        ]);

        $response = $this->actingAs($buyer)->post(route('profile.update'), [
            'name'    => 'Updated Client Name',
            'college' => '',
            'bio'     => 'Looking for UI designers',
            'skills'  => 'Design, Web',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('users', [
            'id'   => $buyer->id,
            'name' => 'Updated Client Name',
        ]);
    }

    public function test_seller_profile_update_requires_college()
    {
        $seller = User::factory()->create([
            'name'    => 'Dev Freelancer',
            'email'   => 'dev@gmail.com',
            'role'    => 'seller',
            'college' => 'COEP Tech',
        ]);

        $response = $this->actingAs($seller)->from(route('profile.edit'))->post(route('profile.update'), [
            'name'    => 'Dev Freelancer',
            'college' => '', // Empty college for seller
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrors(['college']);
    }

    public function test_dashboard_renders_for_buyer_without_seller_links()
    {
        $buyer = User::factory()->create([
            'name' => 'Test Buyer',
            'email' => 'buyer@test.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($buyer)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Client / Buyer');
        $response->assertDontSee(route('seller.services.create'));
    }
}

