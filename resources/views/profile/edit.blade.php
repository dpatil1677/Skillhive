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
            <div class="card-sh p-4 p-md-5">
                <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom-sh">
                    <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary);">
                    <div>
                        <h2 class="mb-1" style="font-size: var(--fs-xl); font-weight: var(--fw-bold);">Profile Settings</h2>
                        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Update your personal student details and skills displayed across SkillHive.</p>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    
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
                            <label class="form-label-sh" for="profCollege">College / University <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-sh w-100 @error('college') is-invalid @enderror" id="profCollege" name="college" value="{{ old('college', $user['college']) }}" placeholder="e.g. MIT Pune" required>
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
            </div>
        </div>
    </div>
</div>

@endsection
