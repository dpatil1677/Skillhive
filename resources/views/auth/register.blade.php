@extends('layouts.guest')

@section('title', 'Join SkillHive — Create Account')
@section('meta_description', 'Create your free SkillHive account. Hire top student freelancers or start earning with your skills today.')

@push('styles')
<style>
.auth-page { min-height: calc(100vh - 80px); display: flex; align-items: center; background: var(--color-bg-alt); padding: 2rem 0; }
.auth-card  { background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); padding: 2.5rem 2rem; width: 100%; max-width: 480px; }
.auth-card h1 { font-size: var(--fs-xl); font-weight: var(--fw-bold); margin-bottom: .25rem; }
.auth-subtitle { color: var(--color-text-secondary); font-size: var(--fs-sm); margin-bottom: 1.75rem; }
.auth-divider  { text-align: center; color: var(--color-muted); font-size: var(--fs-xs); margin: 1.25rem 0; position: relative; }
.auth-divider::before, .auth-divider::after {
    content: ''; position: absolute; top: 50%; height: 1px;
    background: var(--color-border); width: calc(50% - 72px);
}
.auth-divider::before { left: 0; } .auth-divider::after { right: 0; }
.auth-footer { text-align: center; font-size: var(--fs-sm); color: var(--color-text-secondary); margin-top: 1.75rem; }
.account-type-selector { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; margin-bottom: 1.5rem; }
.account-type-option {
    display: flex; flex-direction: column; align-items: center; gap: .375rem;
    padding: .875rem .5rem; border: 1.5px solid var(--color-border);
    border-radius: var(--radius-md); cursor: pointer; transition: all .15s;
    font-size: var(--fs-sm); font-weight: var(--fw-medium); color: var(--color-text-secondary);
    text-align: center;
}
.account-type-option i { font-size: 1.5rem; }
.account-type-option:hover { border-color: var(--color-primary); color: var(--color-primary); }
.account-type-option.selected { border-color: var(--color-primary); background: var(--color-primary-subtle); color: var(--color-primary); }
.btn-social {
    display: flex; align-items: center; justify-content: center; gap: .75rem;
    width: 100%; padding: .6rem 1rem; border: 1.5px solid var(--color-border);
    border-radius: var(--radius-md); background: #fff; font-size: var(--fs-sm);
    font-weight: var(--fw-medium); cursor: pointer; transition: border-color .15s, box-shadow .15s; color: var(--color-text);
}
.btn-social:hover { border-color: var(--color-primary); box-shadow: 0 0 0 3px var(--color-primary-subtle); }
.pw-toggle {
    position: absolute; right: 0; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: var(--color-muted); padding: .5rem .75rem; cursor: pointer;
}
.pw-toggle:hover { color: var(--color-primary); }
.field-error { color: #dc2626; font-size: var(--fs-xs); margin-top: .25rem; }
.field-ok    { color: #16a34a; font-size: var(--fs-xs); margin-top: .25rem; }

/* Password strength bar */
.pw-strength-bar { height: 4px; border-radius: 2px; margin-top: 6px; transition: width .3s, background .3s; }
.pw-strength-label { font-size: 11px; margin-top: 3px; }
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

                <h1>Create your account</h1>
                <p class="auth-subtitle">Join SkillHive and start today</p>

                @php
                    $selectedAccountType = old('account_type', request('type', 'buyer'));
                @endphp

                {{-- Account Type Selector --}}
                <div class="account-type-selector" id="accountTypeSelector">
                    <div class="account-type-option {{ $selectedAccountType === 'buyer' ? 'selected' : '' }}" data-type="buyer" onclick="selectType('buyer')">
                        <i class="bi bi-person-circle"></i>
                        <span>I want to hire</span>
                        <small style="font-size:10px;opacity:.7;">Client / Buyer</small>
                    </div>
                    <div class="account-type-option {{ $selectedAccountType === 'seller' ? 'selected' : '' }}" data-type="seller" onclick="selectType('seller')">
                        <i class="bi bi-laptop"></i>
                        <span>I want to work</span>
                        <small style="font-size:10px;opacity:.7;">Freelancer</small>
                    </div>
                </div>

                {{-- Social --}}
                <div class="d-grid mb-2">
                    <button type="button" class="btn-social" onclick="showToast('Google sign-up is coming soon! Please register with your email below.','info')">
                        <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        <span>Continue with Google</span>
                        <span class="badge-sh badge-sh-secondary ms-2" style="font-size:10px;padding:2px 6px;">Coming Soon</span>
                    </button>
                </div>

                <div class="auth-divider">or register with email</div>

                {{-- Register Form --}}
                <form action="{{ route('register.process') }}" method="POST" id="registerForm" novalidate>
                    @csrf
                    <input type="hidden" name="account_type" id="accountType" value="{{ $selectedAccountType }}">

                    {{-- Name --}}
                    <div class="mb-4">
                        <label class="form-label-sh" for="regName">Full Name <span class="text-danger">*</span></label>
                        <input type="text"
                               id="regName" name="name"
                               class="form-control-sh w-100 {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name') }}"
                               placeholder="e.g. Dhruv Patil"
                               required autocomplete="name">
                        @error('name')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="form-label-sh" for="regEmail">
                            Email Address <span class="text-danger">*</span>
                            <span class="text-muted fw-normal" style="font-size:var(--fs-xs);">(College or personal email)</span>
                        </label>
                        <input type="email"
                               id="regEmail" name="email"
                               class="form-control-sh w-100 {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email') }}"
                               placeholder="e.g. you@gmail.com or you@college.edu"
                               required autocomplete="email">
                        @error('email')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                        <div id="emailFeedback"></div>
                    </div>

                    {{-- College (required for sellers, optional for buyers) --}}
                    <div class="mb-4">
                        <label class="form-label-sh" for="regCollege">
                            College / University
                            <span id="collegeOptional" style="color:var(--color-muted);font-weight:400;">
                                {{ $selectedAccountType === 'seller' ? '*' : ' (optional)' }}
                            </span>
                        </label>
                        <input type="text"
                               id="regCollege" name="college"
                               class="form-control-sh w-100 {{ $errors->has('college') ? 'is-invalid' : '' }}"
                               value="{{ old('college') }}"
                               placeholder="e.g. MIT Pune, IIT Bombay"
                               autocomplete="organization">
                        @error('college')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Course --}}
                    <div class="mb-4" id="courseField">
                        <label class="form-label-sh" for="regCourse">Course / Specialisation <span class="text-muted fw-normal" style="font-size:var(--fs-xs);">(optional)</span></label>
                        <input type="text"
                               id="regCourse" name="course"
                               class="form-control-sh w-100"
                               value="{{ old('course') }}"
                               placeholder="e.g. B.Tech CSE, BDes Communication"
                               autocomplete="off">
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label-sh mb-0" for="regPassword">Password <span class="text-danger">*</span></label>
                            <span class="text-muted" style="font-size:var(--fs-xs);">Min. 8 characters</span>
                        </div>
                        <div style="position:relative;">
                            <input type="password"
                                   id="regPassword" name="password"
                                   class="form-control-sh w-100 {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="Create a strong password"
                                   required autocomplete="new-password"
                                   style="padding-right:2.75rem;">
                            <button type="button" class="pw-toggle" onclick="togglePw('regPassword',this)" title="Show/hide password"><i class="bi bi-eye"></i></button>
                        </div>
                        {{-- Strength bar --}}
                        <div style="background:var(--color-border);border-radius:2px;margin-top:6px;">
                            <div id="pwStrengthBar" class="pw-strength-bar" style="width:0%;background:#dc2626;"></div>
                        </div>
                        <div id="pwStrengthLabel" class="pw-strength-label text-muted"></div>
                        @error('password')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label class="form-label-sh" for="regConfirm">Confirm Password <span class="text-danger">*</span></label>
                        <div style="position:relative;">
                            <input type="password"
                                   id="regConfirm" name="password_confirmation"
                                   class="form-control-sh w-100"
                                   placeholder="Repeat your password"
                                   required autocomplete="new-password"
                                   style="padding-right:2.75rem;">
                            <button type="button" class="pw-toggle" onclick="togglePw('regConfirm',this)" title="Show/hide password"><i class="bi bi-eye"></i></button>
                        </div>
                        <div id="confirmFeedback"></div>
                    </div>

                    {{-- Terms --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-start gap-2">
                            <input type="checkbox" class="form-check-input mt-1 {{ $errors->has('terms') ? 'is-invalid' : '' }}" id="agreeTerms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required style="width:16px;height:16px;flex-shrink:0;">
                            <label for="agreeTerms" style="font-size:var(--fs-sm);cursor:pointer;color:var(--color-text-secondary);">
                                I agree to SkillHive's <a href="{{ route('home') }}#terms">Terms of Service</a> and <a href="{{ route('home') }}#privacy">Privacy Policy</a> <span class="text-danger">*</span>
                            </label>
                        </div>
                        @error('terms')
                            <div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn-sh btn-sh-primary btn-sh-lg" id="registerBtn">
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="auth-footer">
                    Already have an account? <a href="{{ route('login') }}" style="font-weight:var(--fw-medium);">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ── Account type selector ── */
function selectType(type) {
    document.getElementById('accountType').value = type;
    document.querySelectorAll('.account-type-option').forEach(el => {
        el.classList.toggle('selected', el.dataset.type === type);
    });
    // Mark college as required for sellers
    const opt  = document.getElementById('collegeOptional');
    const coll = document.getElementById('regCollege');
    if (type === 'seller') {
        coll.required = true;
        opt.textContent = ' *';
        opt.style.color = '#dc2626';
    } else {
        coll.required = false;
        opt.textContent = ' (optional)';
        opt.style.color = 'var(--color-muted)';
    }
}

// Initialise based on current selection
selectType("{{ $selectedAccountType }}");

/* ── Toggle password visibility ── */
function togglePw(fieldId, btn) {
    const f = document.getElementById(fieldId);
    const i = btn.querySelector('i');
    f.type  = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}

/* ── Real-time password strength ── */
document.getElementById('regPassword')?.addEventListener('input', function () {
    const v = this.value;
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const bar   = document.getElementById('pwStrengthBar');
    const label = document.getElementById('pwStrengthLabel');
    const map   = [
        { w: '25%', c: '#dc2626', l: 'Weak (add numbers/symbols)' },
        { w: '50%', c: '#f59e0b', l: 'Fair' },
        { w: '75%', c: '#3b82f6', l: 'Good' },
        { w: '100%',c: '#16a34a', l: 'Strong ✓' },
    ];
    const s = v.length ? map[Math.min(Math.max(score - 1, 0), 3)] : { w: '0%', c: '#dc2626', l: '' };
    bar.style.width      = s.w;
    bar.style.background = s.c;
    label.textContent    = s.l;
    label.style.color    = s.c;

    // Also recheck confirm
    checkConfirm();
});

/* ── Real-time confirm match ── */
function checkConfirm() {
    const pw  = document.getElementById('regPassword').value;
    const cf  = document.getElementById('regConfirm').value;
    const fb  = document.getElementById('confirmFeedback');
    if (!cf) { fb.innerHTML = ''; return; }
    if (pw === cf) {
        fb.innerHTML = '<div class="field-ok"><i class="bi bi-check-circle me-1"></i>Passwords match</div>';
    } else {
        fb.innerHTML = '<div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>Passwords do not match</div>';
    }
}
document.getElementById('regConfirm')?.addEventListener('input', checkConfirm);

/* ── Real-time email uniqueness feedback ── */
let emailTimer;
document.getElementById('regEmail')?.addEventListener('input', function () {
    clearTimeout(emailTimer);
    const fb = document.getElementById('emailFeedback');
    const val = this.value.trim().toLowerCase();
    if (!val.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) { fb.innerHTML = ''; return; }

    emailTimer = setTimeout(async () => {
        try {
            const r = await fetch(`/api/check-email?email=${encodeURIComponent(val)}`);
            const d = await r.json();
            fb.innerHTML = d.exists
                ? '<div class="field-error"><i class="bi bi-exclamation-circle me-1"></i>This email is already registered. <a href="{{ route("login") }}">Sign in</a></div>'
                : '<div class="field-ok"><i class="bi bi-check-circle me-1"></i>Email is available</div>';
        } catch (_) { fb.innerHTML = ''; }
    }, 400);
});

/* ── Form submit validation ── */
document.getElementById('registerForm')?.addEventListener('submit', function (e) {
    const name  = document.getElementById('regName');
    const email = document.getElementById('regEmail');
    const pw    = document.getElementById('regPassword');
    const cf    = document.getElementById('regConfirm');
    const terms = document.getElementById('agreeTerms');
    const coll  = document.getElementById('regCollege');
    const type  = document.getElementById('accountType').value;
    let ok = true;

    // Normalize values
    name.value  = name.value.trim();
    email.value = email.value.trim().toLowerCase();

    if (name.value.length < 2) {
        showErr(name, 'Please enter your full name (minimum 2 characters).');
        ok = false;
    }
    if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showErr(email, 'Please enter a valid email address.');
        ok = false;
    }
    if (type === 'seller' && coll.value.trim().length === 0) {
        showErr(coll, 'Please provide your college or university name as a freelancer.');
        ok = false;
    }
    if (pw.value.length < 8) {
        showErr(pw, 'Password must be at least 8 characters.');
        ok = false;
    }
    if (pw.value !== cf.value) {
        showErr(cf, 'Passwords do not match.');
        ok = false;
    }
    if (!terms.checked) {
        showToast('Please agree to the Terms of Service and Privacy Policy to continue.', 'warning');
        ok = false;
    }

    if (!ok) {
        e.preventDefault();
        const btn = document.getElementById('registerBtn');
        btn.textContent = 'Create Account';
        btn.disabled = false;
        return;
    }

    const btn = document.getElementById('registerBtn');
    btn.textContent = 'Creating account…';
    btn.disabled = true;
    this.submit();
});

function showErr(el, msg) {
    el.classList.add('is-invalid');
    const wrap = el.closest('.mb-4');
    wrap?.querySelectorAll('.js-err').forEach(e => e.remove());
    const d = document.createElement('div');
    d.className = 'field-error js-err';
    d.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>${msg}`;
    wrap?.appendChild(d);
}

document.querySelectorAll('#registerForm input').forEach(input => {
    input.addEventListener('input', () => {
        input.classList.remove('is-invalid');
        input.closest('.mb-4')?.querySelectorAll('.js-err').forEach(e => e.remove());
    });
});
</script>
@endpush
