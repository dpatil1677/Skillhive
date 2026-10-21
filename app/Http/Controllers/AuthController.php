<?php

namespace App\Http\Controllers;

use App\Helpers\UserStore;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ================================================================
    // LOGIN
    // ================================================================

    public function login(Request $request)
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role === 'admin')  return redirect()->route('admin.dashboard');
            if ($role === 'seller') return redirect()->route('seller.dashboard');
            return redirect()->route('dashboard');
        }

        return view('auth.login', [
            'redirectUrl' => $request->query('redirect', ''),
            'serviceSlug' => $request->query('service', ''),
            'packageKey'  => $request->query('package', 'standard'),
        ]);
    }

    public function processLogin(Request $request)
    {
        $normalizedEmail = strtolower(trim((string) $request->input('email', '')));
        $request->merge(['email' => $normalizedEmail]);

        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'Please enter your email address.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
            'password.min'      => 'Password must be at least 6 characters.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($normalizedEmail) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => "Too many login attempts. Please try again in {$seconds} seconds."])
                ->with('toast_error', "Too many failed attempts. Please wait {$seconds}s.");
        }

        $credentials = [
            'email'    => $normalizedEmail,
            'password' => $request->input('password'),
        ];
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();
            UserStore::initForAuthUser();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('toast_success', 'Welcome back, ' . $user->name . '! (Admin)');
            }

            if ($user->role === 'seller') {
                return redirect()->route('seller.dashboard')
                    ->with('toast_success', 'Welcome back, ' . $user->name . '! 🎨');
            }

            // Post-login redirect to checkout
            if ($request->filled('redirect_url') && $request->input('redirect_url') === 'checkout' && $request->filled('service_slug')) {
                return redirect()->route('checkout', [
                    'serviceSlug' => $request->input('service_slug'),
                    'package'     => $request->input('package_key', 'standard'),
                ])->with('toast_success', 'Signed in! Complete your booking below.');
            }

            return redirect()->route('dashboard')
                ->with('toast_success', 'Welcome back, ' . $user->name . '!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'These credentials do not match our records. Please check your email and password.'])
            ->with('toast_error', 'Invalid email or password. Please try again.');
    }

    // ================================================================
    // DEMO LOGIN
    // ================================================================

    public function demoLogin(Request $request)
    {
        $type = $request->query('type', 'client');

        $emailMap = [
            'admin'  => 'admin@skillhive.edu.in',
            'seller' => 'mitali.p@srishti.edu.in',
            'client' => 'dhruv.patil@mitpune.edu.in',
        ];

        $email = $emailMap[$type] ?? $emailMap['client'];
        $user  = User::where('email', $email)->first();

        if (!$user) {
            $user = match ($type) {
                'admin'  => User::where('role', 'admin')->first(),
                'seller' => User::where('role', 'seller')->first(),
                default  => User::where('role', 'user')->first(),
            };
        }

        if (!$user) {
            return redirect()->route('login')
                ->with('toast_error', 'Demo user not found. Please run: php artisan migrate:fresh --seed');
        }

        Auth::login($user, false);
        $request->session()->regenerate();
        UserStore::initForAuthUser();

        $redirectUrl = $request->query('redirect');
        $serviceSlug = $request->query('service');
        $packageKey  = $request->query('package', 'standard');

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('toast_success', 'Signed in as SkillHive Admin!');
        }

        if ($user->role === 'seller') {
            return redirect()->route('seller.dashboard')
                ->with('toast_success', 'Welcome, ' . $user->name . '! Freelancer Dashboard 🎨');
        }

        if ($redirectUrl === 'checkout' && $serviceSlug) {
            return redirect()->route('checkout', ['serviceSlug' => $serviceSlug, 'package' => $packageKey])
                ->with('toast_success', 'Signed in as ' . $user->name . '. Complete your booking below.');
        }

        return redirect()->route('dashboard')
            ->with('toast_success', 'Signed in as ' . $user->name . ' (Demo ' . ucfirst($type) . ')');
    }

    // ================================================================
    // REGISTER
    // ================================================================

    public function register()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role === 'seller') return redirect()->route('seller.dashboard');
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function processRegister(Request $request)
    {
        $normalizedEmail = strtolower(trim((string) $request->input('email', '')));
        $normalizedName  = trim((string) $request->input('name', ''));

        $request->merge([
            'email' => $normalizedEmail,
            'name'  => $normalizedName,
        ]);

        $request->validate([
            'name'         => ['required', 'string', 'max:100', 'min:2'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'account_type' => ['nullable', 'in:buyer,seller'],
            'college'      => ['nullable', 'string', 'max:150', 'required_if:account_type,seller'],
            'course'       => ['nullable', 'string', 'max:100'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'terms'        => ['accepted'],
        ], [
            'name.required'       => 'Please enter your full name.',
            'name.min'            => 'Name must be at least 2 characters.',
            'email.required'      => 'Please enter your email address.',
            'email.email'         => 'Please enter a valid email address.',
            'email.unique'        => 'This email is already registered. Try signing in.',
            'college.required_if' => 'Please provide your college or university name as a student freelancer.',
            'password.required'   => 'Please choose a password.',
            'password.min'        => 'Password must be at least 8 characters.',
            'password.confirmed'  => 'The password confirmation does not match.',
            'terms.accepted'      => 'Please agree to the Terms of Service and Privacy Policy to continue.',
        ]);

        $accountType = $request->input('account_type', 'buyer');
        $role        = ($accountType === 'seller') ? 'seller' : 'user';

        $user = User::create([
            'name'     => $normalizedName,
            'email'    => $normalizedEmail,
            'password' => Hash::make($request->input('password')),
            'role'     => $role,
            'college'  => trim((string) $request->input('college', '')),
            'course'   => trim((string) $request->input('course', 'Student Member')),
            'bio'      => 'New SkillHive member.',
            'skills'   => ['Student', 'Freelancer'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        UserStore::initForAuthUser();

        if ($role === 'seller') {
            return redirect()->route('seller.dashboard')
                ->with('toast_success', 'Welcome to SkillHive, ' . $user->name . '! Start creating your first service. 🎨');
        }

        return redirect()->route('dashboard')
            ->with('toast_success', 'Welcome to SkillHive, ' . $user->name . '! Your account is ready.');
    }

    // ================================================================
    // LOGOUT
    // ================================================================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('toast_info', 'You have been signed out. See you soon!');
    }

    // ================================================================
    // FORGOT PASSWORD — sends a real reset email via Laravel Password broker
    // ================================================================

    public function forgotPassword()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $normalizedEmail = strtolower(trim((string) $request->input('email', '')));
        $request->merge(['email' => $normalizedEmail]);

        $request->validate(
            ['email' => ['required', 'email']],
            ['email.required' => 'Please enter your email address.',
             'email.email'    => 'Please enter a valid email address.']
        );

        $status = Password::sendResetLink(['email' => $normalizedEmail]);

        if ($status === Password::RESET_LINK_SENT) {
            $user = User::where('email', $normalizedEmail)->first();
            if ($user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title'   => 'Password Reset Link Dispatched',
                    'message' => 'A secure password reset link was dispatched to your email address.',
                    'icon'    => 'bi-key-fill',
                    'type'    => 'security',
                ]);
            }

            return back()->withInput()->with('status', __($status));
        }

        return back()
            ->withInput()
            ->withErrors(['email' => __($status)]);
    }

    // ================================================================
    // RESET PASSWORD — shows the form from the emailed link
    // ================================================================

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $normalizedEmail = strtolower(trim((string) $request->input('email', '')));
        $request->merge(['email' => $normalizedEmail]);

        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $status = Password::reset(
            [
                'email'                 => $normalizedEmail,
                'password'              => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token'                 => $request->input('token'),
            ],
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('toast_success', 'Password reset successfully! You can now sign in with your new password.');
        }

        return back()
            ->withInput()
            ->withErrors(['email' => __($status)]);
    }

    // ================================================================
    // EMAIL AVAILABILITY CHECK (AJAX — used by register form)
    // ================================================================

    public function checkEmailAvailability(Request $request)
    {
        $email  = strtolower(trim($request->query('email', '')));
        $exists = User::where('email', $email)->exists();
        return response()->json(['exists' => $exists]);
    }
}
