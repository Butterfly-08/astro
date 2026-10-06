<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test home page loads successfully.
     */
    public function test_home_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('AstroVani');
        $response->assertSee('Guidance for Your Journey');
        $response->assertSee('id="website-language"', false);
        $response->assertSee('value="hi"', false);
        $response->assertSee('<span class="notranslate" translate="no">AstroVani</span>', false);
        $response->assertSee('id="page-loader"', false);
        $response->assertSee('css/page-loader.css');
        $response->assertSee('js/page-loader.js');
    }

    /**
     * Test customer registration page is accessible.
     */
    public function test_customer_registration_page_is_accessible(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Join AstroVani');
        $response->assertSee('id="page-loader"', false);
    }

    /**
     * Test customer can register successfully with full required details.
     */
    public function test_customer_can_register_successfully(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Aditi',
            'last_name' => 'Patel',
            'email' => 'aditi@example.com',
            'phone' => '+91 9876500000',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'date_of_birth' => '1998-05-20',
            'gender' => 'female',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'country' => 'India',
            'terms' => '1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs(User::where('email', 'aditi@example.com')->first(), 'web');
        $this->assertDatabaseHas('users', [
            'email' => 'aditi@example.com',
            'first_name' => 'Aditi',
            'last_name' => 'Patel',
            'status' => 'active',
        ]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('20 May, 1998');
    }

    /**
     * Test registration validation fails with short password.
     */
    public function test_customer_registration_requires_minimum_password_length(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Aditi',
            'email' => 'shortpass@example.com',
            'password' => '123',
            'password_confirmation' => '123',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest('web');
    }

    /**
     * Test registration fails when email is already registered.
     */
    public function test_customer_registration_fails_with_duplicate_email(): void
    {
        User::create([
            'first_name' => 'Existing',
            'email' => 'existing@example.com',
            'password' => Hash::make('Password@123'),
            'status' => 'active',
        ]);

        $response = $this->post('/register', [
            'first_name' => 'New',
            'email' => 'existing@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test customer can log in and access dashboard.
     */
    public function test_customer_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'first_name' => 'Karan',
            'last_name' => 'Mehta',
            'email' => 'karan@example.com',
            'password' => Hash::make('Password@123'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'karan@example.com',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user, 'web');

        // Customer dashboard view check
        $dashboardResponse = $this->actingAs($user, 'web')->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Namaste, Karan Mehta!');
    }

    /**
     * Test customer login fails with invalid password.
     */
    public function test_customer_login_fails_with_invalid_credentials(): void
    {
        User::create([
            'first_name' => 'Karan',
            'email' => 'karan@example.com',
            'password' => Hash::make('Password@123'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'karan@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('web');
    }

    /**
     * Test customer logout.
     */
    public function test_customer_can_logout(): void
    {
        $user = User::create([
            'first_name' => 'Karan',
            'email' => 'karan@example.com',
            'password' => Hash::make('Password@123'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user, 'web')->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest('web');
    }

    /**
     * Test admin login page is accessible at /admin/login.
     */
    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Admin Control Portal');
    }

    /**
     * Test admin can log in and access /admin/dashboard.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = Admin::create([
            'name' => 'Master Administrator',
            'email' => 'admin@astrovani.test',
            'password' => Hash::make('Password@123'),
            'status' => 'active',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@astrovani.test',
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin, 'admin');

        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Master Administration Dashboard');
        $dashboardResponse->assertSee('id="page-loader"', false);
    }

    /**
     * Test unauthenticated user or guest cannot access admin dashboard.
     */
    public function test_unauthenticated_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test regular customer CANNOT access admin dashboard.
     */
    public function test_authenticated_customer_cannot_access_admin_dashboard(): void
    {
        $user = User::create([
            'first_name' => 'Regular',
            'email' => 'regular@example.com',
            'password' => Hash::make('Password@123'),
            'status' => 'active',
        ]);

        // Access as web guard user
        $response = $this->actingAs($user, 'web')->get('/admin/dashboard');
        // AdminMiddleware rejects because auth('admin')->check() is false
        $response->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }

    /**
     * Test admin logout.
     */
    public function test_admin_can_logout(): void
    {
        $admin = Admin::create([
            'name' => 'Master Administrator',
            'email' => 'admin@astrovani.test',
            'password' => Hash::make('Password@123'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }
}
