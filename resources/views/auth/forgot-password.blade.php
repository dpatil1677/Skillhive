@extends('layouts.guest')

@section('title', 'Forgot Password — SkillHive')
@section('meta_description', 'Reset your SkillHive account password.')

@push('styles')
<style>
.auth-page { min-height: calc(100vh - 80px); display: flex; align-items: center; background: var(--color-bg-alt); padding: 2rem 0; }
.auth-card  { background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); padding: 2.5rem 2rem; width: 100%; max-width: 440px; }
.auth-card h1 { font-size: var(--fs-xl); font-weight: var(--fw-bold); margin-bottom: .25rem; }
.auth-subtitle { color: var(--color-text-secondary); font-size: var(--fs-sm); margin-bottom: 1.75rem; }
.auth-footer { text-align: center; font-size: var(--fs-sm); color: var(--color-text-secondary); margin-top: 1.75rem; }
.field-error { color: #dc2626; font-size: var(--fs-xs); margin-top: .25rem; }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="auth-card">

                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" style="text-decoration:none;color:var(--color-primary);font-size:var(--fs-xl);font-weight:var(--fw-bold);">
                        <i class="bi bi-hexagon-fill" style="font-size:1.4rem;margin-right:2px;"></i> SkillHive
                    </a>
                </div>

                @if(session('status'))
                    {{-- Success state --}}
                    <div class="text-center mb-4">
                        <div style="width:64px;height:64px;background:var(--color-primary-subtle);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="bi bi-envelope-check-fill text-primary" style="font-size:1.75rem;"></i>
                        </div>
                        <h1 style="font-size:var(--fs-xl);">Check your email</h1>
                        <p class="auth-subtitle mb-3">We sent a password reset link to <strong>{{ old('email') }}</strong></p>
                        <div style="background:var(--color-primary-subtle);border:1px solid var(--color-primary-light);border-radius:var(--radius-md);padding:.875rem 1rem;font-size:var(--fs-sm);color:var(--color-primary);text-align:left;">
                            <i class="bi bi-shield-check me-1"></i>
                            <strong>Tip:</strong> If you don't see the email within a minute, check your <strong>Spam</strong> or <strong>Junk</strong> folder. The link expires in <strong>60 minutes</strong>.
                        </div>
                    </div>

                    <a href="{{ route('login') }}" class="btn-sh btn-sh-primary btn-sh-lg d-block text-center">
                        <i class="bi bi-arrow-left me-1"></i> Back to Sign In
                    </a>

                @else
                    <h1>Forgot Password</h1>
                    <p class="auth-subtitle">Enter your registered email and we'll send you a reset link.</p>

                    @if($errors->has('email'))
                        <div style="background:#fef2f2;color:#dc2626;border-radius:var(--radius-md);padding:.75rem 1rem;font-size:var(--fs-sm);margin-bottom:1rem;">
                            <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first('email') }}
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST" id="forgotForm" novalidate>
                        @csrf

                        <div class="mb-4">
                            <label class="form-label-sh" for="resetEmail">Email address</label>
                            <input type="email"
                                   id="resetEmail" name="email"
                                   class="form-control-sh w-100 {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                   value="{{ old('email') }}"
                                   placeholder="you@college.edu"
                                   required autocomplete="email">
                            @error('email')
                                <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn-sh btn-sh-primary btn-sh-lg" id="sendBtn">
                                <i class="bi bi-envelope me-1"></i> Send Reset Link
                            </button>
                        </div>
                    </form>

                    <div class="auth-footer">
                        Remember your password?
                        <a href="{{ route('login') }}" style="font-weight:var(--fw-medium);">Sign In</a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('forgotForm')?.addEventListener('submit', function(e) {
    const email = document.getElementById('resetEmail');
    if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        e.preventDefault();
        return;
    }
    document.getElementById('sendBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending…';
    document.getElementById('sendBtn').disabled = true;
});
</script>
@endpush
