{{-- Auth Modal Component --}}
{{-- Shown when guest tries a restricted action --}}
<div class="modal fade auth-modal-sh" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <i class="bi bi-lock"></i>
                <h5 id="authModalLabel">Sign in to continue</h5>
                <p>You need to be logged in to perform this action. Join SkillHive to access all features.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn-sh btn-sh-primary btn-sh-lg">Sign In</a>
                    <a href="{{ route('register') }}" class="btn-sh btn-sh-outline btn-sh-lg">Create Account</a>
                </div>
            </div>
        </div>
    </div>
</div>
