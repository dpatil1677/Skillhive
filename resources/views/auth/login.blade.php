@extends('layouts.guest')

@section('title', 'Sign In — SkillHive')
@section('meta_description', 'Sign in to your SkillHive account to hire freelancers or manage your services.')

@push('styles')
<style>
.auth-page { min-height: calc(100vh - 80px); display: flex; align-items: center; background: var(--color-bg-alt); padding: 2rem 0; }
.auth-card  { background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); padding: 2.5rem 2rem; width: 100%; max-width: 480px; }
.auth-card h1 { font-size: var(--fs-xl); font-weight: var(--fw-bold); margin-bottom: .25rem; }
.auth-subtitle { color: var(--color-text-secondary); font-size: var(--fs-sm); margin-bottom: 1.75rem; }
.auth-divider  { text-align: center; color: var(--color-muted); font-size: var(--fs-xs); margin: 1.25rem 0; position: relative; }
.auth-divider::before, .auth-divider::after {
    content: ''; position: absolute; top: 50%; height: 1px;
    background: var(--color-border); width: calc(50% - 64px);
}
.auth-divider::before { left: 0; } .auth-divider::after { right: 0; }
.auth-footer { text-align: center; font-size: var(--fs-sm); color: var(--color-text-secondary); margin-top: 1.75rem; }
.btn-social {
    display: flex; align-items: center; justify-content: center; gap: .75rem;
    width: 100%; padding: .6rem 1rem; border: 1.5px solid var(--color-border);
    border-radius: var(--radius-md); background: #fff; font-size: var(--fs-sm);
    font-weight: var(--fw-medium); cursor: pointer; transition: border-color .15s, box-shadow .15s;
    color: var(--color-text);
}
.btn-social:hover { border-color: var(--color-primary); box-shadow: 0 0 0 3px var(--color-primary-subtle); }
.pw-toggle {
    position: absolute; right: 0; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: var(--color-muted); padding: .5rem .75rem;
    cursor: pointer; line-height: 1;
}
.pw-toggle:hover { color: var(--color-primary); }
.field-error { color: #dc2626; font-size: var(--fs-xs); margin-top: .25rem; }
.field-ok    { color: #16a34a; font-size: var(--fs-xs); margin-top: .25rem; }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="auth-card">

                {{-- Logo --}}
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" style="text-decoration:none;color:var(--color-primary);font-size:var(--fs-xl);font-weight:var(--fw-bold);">
                        <i class="bi bi-hexagon-fill" style="font-size:1.4rem;margin-right:2px;"></i> SkillHive
                    </a>
                </div>

                <h1>Welcome back</h1>
                <p class="auth-subtitle">Sign in to your SkillHive account</p>

                {{-- Server-side error alert --}}
                @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="alert alert-danger border-0 mb-3" style="font-size:var(--fs-sm);background:#fef2f2;color:#dc2626;border-radius:var(--radius-md);padding:.75rem 1rem;">
                        <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                {{-- Demo Account Buttons --}}
                <div class="card-sh p-3 mb-4" style="background:var(--color-primary-subtle);border:1px solid var(--color-primary-light);border-radius:var(--radius-md);">
                    <div class="d-flex align-items-center gap-2 mb-2" style="font-size:var(--fs-xs);font-weight:var(--fw-semibold);color:var(--color-primary);">
                        <i class="bi bi-person-badge-fill"></i> DEMO ACCOUNTS — ONE-CLICK LOGIN
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login.demo', ['type'=>'client','redirect'=>$redirectUrl??'','service'=>$serviceSlug??'','package'=>$packageKey??'standard']) }}" class="btn-sh btn-sh-sm btn-sh-primary">
                            <i class="bi bi-person-circle"></i> Demo User — Dhruv Patil
                        </a>
                        <a href="{{ route('login.demo', ['type'=>'seller','redirect'=>$redirectUrl??'','service'=>$serviceSlug??'','package'=>$packageKey??'standard']) }}" class="btn-sh btn-sh-sm btn-sh-outline">
                            <i class="bi bi-laptop"></i> Demo Freelancer — Mitali Patel
                        </a>
                        <a href="{{ route('login.demo', ['type'=>'admin']) }}" class="btn-sh btn-sh-sm btn-sh-secondary">
                            <i class="bi bi-shield-lock-fill"></i> Demo Admin
                        </a>
                    </div>
                </div>

                {{-- Social --}}
                <div class="d-grid mb-2">
                    <button type="button" class="btn-social" onclick="showToast('Google login coming soon!','info')">
                        <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Continue with Google
                    </button>
                </div>

                <div class="auth-divider">or sign in with email</div>

                {{-- Login Form --}}
                <form action="{{ route('login.process') }}" method="POST" id="loginForm" novalidate>
                    @csrf
                    <input type="hidden" name="redirect_url"  value="{{ $redirectUrl ?? '' }}">
                    <input type="hidden" name="service_slug"  value="{{ $serviceSlug ?? '' }}">
                    <input type="hidden" name="package_key"   value="{{ $packageKey ?? 'standard' }}">

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="form-label-sh" for="loginEmail">Email address</label>
                        <input type="email"
                               id="loginEmail"
                               name="email"
                               class="form-control-sh w-100 {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email') }}"
                               placeholder="you@college.edu"
                               required
                               autocomplete="email">
                        @error('email')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label-sh mb-0" for="loginPassword">Password</label>
                            <a href="{{ route('password.request') }}" style="font-size:var(--fs-sm);">Forgot password?</a>
                        </div>
                        <div style="position:relative;">
                            <input type="password"
                                   id="loginPassword"
                                   name="password"
                                   class="form-control-sh w-100 {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="Your password"
                                   required
                                   autocomplete="current-password"
                                   style="padding-right:2.75rem;">
                            <button type="button" class="pw-toggle" onclick="togglePw('loginPassword',this)" title="Show/hide password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div class="d-flex align-items-center gap-2 mb-5">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember" style="width:16px;height:16px;">
                        <label for="rememberMe" style="font-size:var(--fs-sm);cursor:pointer;">Remember me for 30 days</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn-sh btn-sh-primary btn-sh-lg" id="loginBtn">
                            Sign In
                        </button>
                    </div>
                </form>

                <div class="auth-footer">
                    Don't have an account? <a href="{{ route('register') }}" style="font-weight:var(--fw-medium);">Join SkillHive</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(fieldId, btn) {
    const f = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (f.type === 'password') {
        f.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        f.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Real-time validation feedback
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const email = document.getElementById('loginEmail');
    const pw    = document.getElementById('loginPassword');
    let ok = true;

    if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showFieldError(email, 'Please enter a valid email address.');
        ok = false;
    } else clearFieldError(email);

    if (pw.value.length < 6) {
        showFieldError(pw, 'Password must be at least 6 characters.');
        ok = false;
    } else clearFieldError(pw);

    if (!ok) e.preventDefault();
    else document.getElementById('loginBtn').textContent = 'Signing in…';
});

function showFieldError(el, msg) {
    clearFieldError(el);
    el.classList.add('is-invalid');
    const d = document.createElement('div');
    d.className = 'field-error js-err';
    d.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + msg;
    el.closest('.mb-4, .mb-3')?.appendChild(d);
}
function clearFieldError(el) {
    el.classList.remove('is-invalid');
    el.closest('.mb-4, .mb-3')?.querySelectorAll('.js-err').forEach(e => e.remove());
}

document.querySelectorAll('#loginForm input').forEach(input => {
    input.addEventListener('input', () => clearFieldError(input));
});
</script>
@endpush
