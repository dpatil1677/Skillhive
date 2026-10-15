@extends('layouts.guest')

@section('title', 'Reset Password — SkillHive')
@section('meta_description', 'Set a new password for your SkillHive account.')

@push('styles')
<style>
.auth-page { min-height: calc(100vh - 80px); display: flex; align-items: center; background: var(--color-bg-alt); padding: 2rem 0; }
.auth-card  { background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); padding: 2.5rem 2rem; width: 100%; max-width: 440px; }
.auth-card h1 { font-size: var(--fs-xl); font-weight: var(--fw-bold); margin-bottom: .25rem; }
.auth-subtitle { color: var(--color-text-secondary); font-size: var(--fs-sm); margin-bottom: 1.75rem; }
.auth-footer { text-align: center; font-size: var(--fs-sm); color: var(--color-text-secondary); margin-top: 1.75rem; }
.field-error { color: #dc2626; font-size: var(--fs-xs); margin-top: .25rem; }
.field-ok    { color: #16a34a; font-size: var(--fs-xs); margin-top: .25rem; }
.pw-toggle {
    position: absolute; right: 0; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: var(--color-muted); padding: .5rem .75rem; cursor: pointer;
}
.pw-toggle:hover { color: var(--color-primary); }
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

                <h1>Set New Password</h1>
                <p class="auth-subtitle">Choose a strong password for your account.</p>

                @if($errors->any())
                    <div class="alert alert-danger border-0 mb-4" style="font-size:var(--fs-sm);background:#fef2f2;color:#dc2626;border-radius:var(--radius-md);padding:.75rem 1rem;">
                        <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST" id="resetForm" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="form-label-sh" for="resetEmail">Email address</label>
                        <input type="email"
                               id="resetEmail" name="email"
                               class="form-control-sh w-100 {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email', $email) }}"
                               placeholder="you@college.edu"
                               required autocomplete="email">
                        @error('email')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="mb-4">
                        <label class="form-label-sh" for="newPassword">New Password</label>
                        <div style="position:relative;">
                            <input type="password"
                                   id="newPassword" name="password"
                                   class="form-control-sh w-100 {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="Min. 8 characters"
                                   required autocomplete="new-password"
                                   style="padding-right:2.75rem;">
                            <button type="button" class="pw-toggle" onclick="togglePw('newPassword',this)"><i class="bi bi-eye"></i></button>
                        </div>
                        {{-- Strength bar --}}
                        <div style="background:var(--color-border);border-radius:2px;margin-top:6px;">
                            <div id="pwBar" style="height:4px;border-radius:2px;width:0%;background:#dc2626;transition:width .3s,background .3s;"></div>
                        </div>
                        <div id="pwLabel" style="font-size:11px;margin-top:3px;"></div>
                        @error('password')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-5">
                        <label class="form-label-sh" for="confirmPassword">Confirm New Password</label>
                        <div style="position:relative;">
                            <input type="password"
                                   id="confirmPassword" name="password_confirmation"
                                   class="form-control-sh w-100"
                                   placeholder="Repeat new password"
                                   required autocomplete="new-password"
                                   style="padding-right:2.75rem;">
                            <button type="button" class="pw-toggle" onclick="togglePw('confirmPassword',this)"><i class="bi bi-eye"></i></button>
                        </div>
                        <div id="confirmFb"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn-sh btn-sh-primary btn-sh-lg" id="resetBtn">
                            <i class="bi bi-lock-fill me-1"></i> Reset Password
                        </button>
                    </div>
                </form>

                <div class="auth-footer">
                    <a href="{{ route('login') }}" style="font-weight:var(--fw-medium);">
                        <i class="bi bi-arrow-left me-1"></i> Back to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(id, btn) {
    const f = document.getElementById(id);
    const i = btn.querySelector('i');
    f.type  = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}

document.getElementById('newPassword')?.addEventListener('input', function () {
    const v = this.value;
    let s = 0;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    const m = [
        { w:'25%', c:'#dc2626', l:'Weak' },
        { w:'50%', c:'#f59e0b', l:'Fair' },
        { w:'75%', c:'#3b82f6', l:'Good' },
        { w:'100%',c:'#16a34a', l:'Strong ✓' },
    ];
    const r = v.length ? m[Math.min(s-1,3)] : { w:'0%', c:'#dc2626', l:'' };
    document.getElementById('pwBar').style.cssText  = `height:4px;border-radius:2px;width:${r.w};background:${r.c};transition:width .3s,background .3s;`;
    document.getElementById('pwLabel').textContent  = r.l;
    document.getElementById('pwLabel').style.color  = r.c;
    checkMatch();
});

document.getElementById('confirmPassword')?.addEventListener('input', checkMatch);

function checkMatch() {
    const pw = document.getElementById('newPassword').value;
    const cf = document.getElementById('confirmPassword').value;
    const fb = document.getElementById('confirmFb');
    if (!cf) { fb.innerHTML = ''; return; }
    fb.innerHTML = pw === cf
        ? '<div class="field-ok"><i class="bi bi-check-circle me-1"></i>Passwords match</div>'
        : '<div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>Passwords do not match</div>';
}

document.getElementById('resetForm')?.addEventListener('submit', function(e) {
    const pw = document.getElementById('newPassword');
    const cf = document.getElementById('confirmPassword');
    if (pw.value.length < 8) { e.preventDefault(); return; }
    if (pw.value !== cf.value) { e.preventDefault(); return; }
    document.getElementById('resetBtn').textContent = 'Resetting…';
});
</script>
@endpush
