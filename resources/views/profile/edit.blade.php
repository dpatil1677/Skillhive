@extends('layouts.guest')

@section('title', 'Profile Settings — SkillHive')

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Profile Settings'],
    ]])

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Role Upgrade / Switch Mode Card --}}
            @if(($user['role'] ?? '') === 'user')
                <div class="card-sh p-4 mb-4" style="background: linear-gradient(135deg, var(--color-primary-subtle) 0%, #fff 100%); border: 2px dashed var(--color-primary);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: var(--color-primary); color: #fff; font-size: 1.5rem;">
                                <i class="bi bi-rocket-takeoff-fill"></i>
                            </div>
                            <div>
                                <h4 class="mb-1" style="font-size: var(--fs-md); font-weight: var(--fw-bold);">Want to offer your student services?</h4>
                                <p class="text-secondary mb-0" style="font-size: var(--fs-xs);">
                                    Upgrade your account to a Freelancer profile in 1 click to list services and earn income.
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-sh btn-sh-primary btn-sh-sm" data-bs-toggle="modal" data-bs-target="#becomeSellerModal">
                            <i class="bi bi-stars me-1"></i> Activate Freelancer Profile
                        </button>
                    </div>
                </div>
            @elseif(($user['role'] ?? '') === 'seller')
                <div class="card-sh p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2 bg-alt">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-sh badge-sh-primary"><i class="bi bi-patch-check-fill me-1"></i> Verified Freelancer</span>
                        <span class="text-secondary" style="font-size: var(--fs-xs);">You have full access to the Freelancer Suite.</span>
                    </div>
                    <form action="{{ route('profile.switch-role') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="role" value="user">
                        <button type="submit" class="btn-sh btn-sh-outline btn-sh-xs">
                            <i class="bi bi-arrow-repeat me-1"></i> Switch to Buyer View
                        </button>
                    </form>
                </div>
            @endif

            <div class="card-sh p-4 p-md-5">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Avatar Section with Upload & Preview --}}
                    <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom-sh flex-wrap">
                        <div class="position-relative">
                            <img id="avatarPreview" src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary); box-shadow: 0 4px 12px rgba(109,40,217,0.15);">
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-1" style="font-size: var(--fs-xl); font-weight: var(--fw-bold);">Profile Settings</h2>
                            <p class="text-secondary mb-2" style="font-size: var(--fs-xs);">Upload a custom photo or keep your verified student avatar.</p>
                            
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <label for="avatarInput" class="btn-sh btn-sh-outline btn-sh-xs cursor-pointer mb-0">
                                    <i class="bi bi-camera me-1"></i> Change Photo
                                </label>
                                <input type="file" id="avatarInput" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp" class="d-none" onchange="previewUserAvatar(event)">
                                
                                @if(!empty($user['profile_image']))
                                    <button type="button" class="btn-sh btn-sh-outline btn-sh-xs text-danger" onclick="document.getElementById('removeAvatarForm').submit();">
                                        <i class="bi bi-trash me-1"></i> Remove
                                    </button>
                                @endif
                                <span class="text-muted" style="font-size: 11px;">JPG, PNG or WEBP (Max 5MB)</span>
                            </div>
                            @error('avatar')
                                <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="profName">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-sh w-100 @error('name') is-invalid @enderror" id="profName" name="name" value="{{ old('name', $user['name']) }}" required>
                            @error('name')
                                <div class="invalid-feedback-sh">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-sh" for="profEmail">Email Address</label>
                            <input type="email" class="form-control-sh w-100 bg-alt" id="profEmail" value="{{ $user['email'] }}" disabled readonly>
                            <span class="text-muted" style="font-size: 11px;">Primary email address cannot be changed.</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="profCollege">College / University @if(($user['role'] ?? '') === 'seller')<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control-sh w-100 @error('college') is-invalid @enderror" id="profCollege" name="college" value="{{ old('college', $user['college']) }}" placeholder="e.g. MIT Pune" @if(($user['role'] ?? '') === 'seller') required @endif>
                            @error('college')
                                <div class="invalid-feedback-sh">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-sh" for="profCourse">Degree / Course</label>
                            <input type="text" class="form-control-sh w-100" id="profCourse" name="course" value="{{ old('course', $user['course'] ?? '') }}" placeholder="e.g. B.Tech Computer Science">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-sh" for="profBio">About Bio</label>
                        <textarea class="form-control-sh w-100" id="profBio" name="bio" rows="4" placeholder="Briefly describe your experience and interests...">{{ old('bio', $user['bio'] ?? '') }}</textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label-sh" for="profSkills">Skills (comma-separated)</label>
                        <input type="text" class="form-control-sh w-100" id="profSkills" name="skills" value="{{ is_array($user['skills']) ? implode(', ', $user['skills']) : $user['skills'] }}" placeholder="Laravel, PHP, JavaScript, Figma">
                        <span class="text-muted" style="font-size: 11px;">Separate skills with commas.</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('dashboard') }}" class="btn-sh btn-sh-outline">Cancel</a>
                        <button type="submit" class="btn-sh btn-sh-primary btn-sh-lg">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>

                {{-- Hidden Form to Remove Avatar --}}
                <form id="removeAvatarForm" action="{{ route('profile.avatar.remove') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Become Seller Modal --}}
@if(($user['role'] ?? '') === 'user')
<div class="modal fade" id="becomeSellerModal" tabindex="-1" aria-labelledby="becomeSellerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="becomeSellerModalLabel">
                    <i class="bi bi-stars text-primary me-1"></i> Activate Freelancer Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.become-seller') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">
                        Enter your college details to start offering student services, publishing gigs, and receiving orders on SkillHive.
                    </p>

                    <div class="mb-3">
                        <label class="form-label-sh" for="modalCollege">College / University <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="modalCollege" name="college" value="{{ old('college', $user['college']) }}" placeholder="e.g. IIT Bombay / MIT Pune" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="modalCourse">Degree / Course</label>
                        <input type="text" class="form-control-sh w-100" id="modalCourse" name="course" value="{{ old('course', $user['course']) }}" placeholder="e.g. B.Tech Computer Engineering">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="modalSkills">Skills You Offer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="modalSkills" name="skills" value="{{ is_array($user['skills']) ? implode(', ', $user['skills']) : $user['skills'] }}" placeholder="Web Development, UI/UX, Video Editing" required>
                        <span class="text-muted" style="font-size: 11px;">Separate skills with commas.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="modalBio">Short Bio</label>
                        <textarea class="form-control-sh w-100" id="modalBio" name="bio" rows="3" placeholder="Tell buyers what you can build or design...">{{ old('bio', $user['bio']) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">
                        <i class="bi bi-check-circle me-1"></i> Activate &amp; Go to Seller Dashboard
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function previewUserAvatar(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection
