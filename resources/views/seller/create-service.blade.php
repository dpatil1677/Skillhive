@extends('layouts.guest')

@section('title', 'Create New Service — SkillHive')

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Seller Dashboard', 'url' => route('seller.dashboard')],
        ['label' => 'Create Service'],
    ]])

    <div class="mb-4">
        <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Create a New Service</h1>
        <p class="text-secondary" style="font-size: var(--fs-sm);">Offer your skills to clients on SkillHive in 4 easy steps.</p>
    </div>

    {{-- Multi-Step Wizard Progress Bar --}}
    <div class="card-sh p-3 mb-5">
        <div class="d-flex justify-content-between position-relative">
            <div class="wizard-step active" id="wStepIndicator1" onclick="goToStep(1)" style="cursor: pointer;">
                <span class="wizard-step-num">1</span>
                <span class="wizard-step-title">Information</span>
            </div>
            <div class="wizard-step" id="wStepIndicator2" onclick="goToStep(2)" style="cursor: pointer;">
                <span class="wizard-step-num">2</span>
                <span class="wizard-step-title">Packages</span>
            </div>
            <div class="wizard-step" id="wStepIndicator3" onclick="goToStep(3)" style="cursor: pointer;">
                <span class="wizard-step-num">3</span>
                <span class="wizard-step-title">Gallery</span>
            </div>
            <div class="wizard-step" id="wStepIndicator4" onclick="goToStep(4)" style="cursor: pointer;">
                <span class="wizard-step-num">4</span>
                <span class="wizard-step-title">Review & Publish</span>
            </div>
        </div>
    </div>

    <form action="{{ route('seller.services.store') }}" method="POST" id="createServiceForm" onsubmit="return handleFormSubmit(event)">
        @csrf

        {{-- STEP 1: Service Information --}}
        <div class="card-sh p-4 p-md-5 wizard-pane" id="wizardStep1">
            <h4 class="service-section-title">Step 1: Service Overview</h4>
            
            <div id="step1Error" class="alert alert-danger d-none mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Please fix the errors below before moving to the next step.
            </div>

            <div class="mb-4">
                <label class="form-label-sh" for="srvTitle">Service Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control-sh w-100" id="srvTitle" name="title" placeholder="I will build a responsive Laravel website for your project" value="{{ old('title') }}" oninput="updateWizardPreview()">
                <span class="text-muted d-block mt-1" style="font-size: 11px;">Start with "I will..." for clarity (min 10 characters).</span>
                <div class="invalid-feedback d-none text-danger mt-1" id="srvTitleError" style="font-size: 12px;"></div>
            </div>

            <div class="mb-4">
                <label class="form-label-sh" for="srvCategory">Category <span class="text-danger">*</span></label>
                <select class="form-select-sh w-100" id="srvCategory" name="category" onchange="updateWizardPreview()">
                    @foreach($categories as $cat)
                        <option value="{{ $cat['name'] }}" {{ old('category') == $cat['name'] ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-none text-danger mt-1" id="srvCategoryError" style="font-size: 12px;"></div>
            </div>

            <div class="mb-4">
                <label class="form-label-sh" for="srvDescription">Service Description <span class="text-danger">*</span></label>
                <textarea class="form-control-sh w-100" id="srvDescription" name="description" rows="6" placeholder="Describe your service in detail. What problems do you solve? What process do you follow?" oninput="updateWizardPreview()">{{ old('description') }}</textarea>
                <span class="text-muted d-block mt-1" style="font-size: 11px;">Min 20 characters detailing what clients will receive.</span>
                <div class="invalid-feedback d-none text-danger mt-1" id="srvDescriptionError" style="font-size: 12px;"></div>
            </div>

            <div class="mb-4">
                <label class="form-label-sh" for="srvTags">Search Tags (comma-separated)</label>
                <input type="text" class="form-control-sh w-100" id="srvTags" name="tags" placeholder="Laravel, PHP, Web App, MySQL" value="{{ old('tags') }}">
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn-sh btn-sh-primary" onclick="goToStep(2)">
                    Next: Packages <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        {{-- STEP 2: Packages --}}
        <div class="card-sh p-4 p-md-5 wizard-pane d-none" id="wizardStep2">
            <h4 class="service-section-title">Step 2: Service Packages</h4>
            
            <div id="step2Error" class="alert alert-danger d-none mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Please provide valid pricing and delivery days.
            </div>

            <div class="row g-4 mb-4">
                {{-- Basic Package --}}
                <div class="col-lg-4">
                    <div class="p-3 border rounded bg-alt h-100">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-box me-1"></i> Basic Package</h6>
                        
                        <div class="mb-3">
                            <label class="form-label-sh">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control-sh w-100" id="basicPrice" name="basic_price" value="{{ old('basic_price', '499') }}" oninput="updateWizardPreview()">
                            <div class="invalid-feedback d-none text-danger mt-1" id="basicPriceError" style="font-size: 12px;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-sh">Delivery (Days) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control-sh w-100" id="basicDelivery" name="basic_delivery" value="{{ old('basic_delivery', '2') }}">
                            <div class="invalid-feedback d-none text-danger mt-1" id="basicDeliveryError" style="font-size: 12px;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-sh">Revisions</label>
                            <input type="number" class="form-control-sh w-100" name="basic_revisions" value="{{ old('basic_revisions', '1') }}">
                        </div>

                        <div>
                            <label class="form-label-sh">Included Features (1 per line)</label>
                            <textarea class="form-control-sh w-100" name="basic_features" rows="4">Basic deliverable&#10;Source file included&#10;1 Revision</textarea>
                        </div>
                    </div>
                </div>

                {{-- Standard Package --}}
                <div class="col-lg-4">
                    <div class="p-3 border border-primary rounded bg-white h-100 shadow-sm">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-star-fill me-1"></i> Standard Package</h6>
                        
                        <div class="mb-3">
                            <label class="form-label-sh">Price (₹)</label>
                            <input type="number" class="form-control-sh w-100" name="standard_price" value="{{ old('standard_price', '999') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label-sh">Delivery (Days)</label>
                            <input type="number" class="form-control-sh w-100" name="standard_delivery" value="{{ old('standard_delivery', '4') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label-sh">Revisions</label>
                            <input type="number" class="form-control-sh w-100" name="standard_revisions" value="{{ old('standard_revisions', '3') }}">
                        </div>

                        <div>
                            <label class="form-label-sh">Included Features (1 per line)</label>
                            <textarea class="form-control-sh w-100" name="standard_features" rows="4">Everything in Basic&#10;Database setup&#10;Admin dashboard&#10;3 Revisions</textarea>
                        </div>
                    </div>
                </div>

                {{-- Premium Package --}}
                <div class="col-lg-4">
                    <div class="p-3 border rounded bg-alt h-100">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-award-fill me-1"></i> Premium Package</h6>
                        
                        <div class="mb-3">
                            <label class="form-label-sh">Price (₹)</label>
                            <input type="number" class="form-control-sh w-100" name="premium_price" value="{{ old('premium_price', '1999') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label-sh">Delivery (Days)</label>
                            <input type="number" class="form-control-sh w-100" name="premium_delivery" value="{{ old('premium_delivery', '7') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label-sh">Revisions</label>
                            <input type="number" class="form-control-sh w-100" name="premium_revisions" value="{{ old('premium_revisions', '5') }}">
                        </div>

                        <div>
                            <label class="form-label-sh">Included Features (1 per line)</label>
                            <textarea class="form-control-sh w-100" name="premium_features" rows="4">Full production app&#10;Deployment help&#10;Commercial license&#10;14 days support</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn-sh btn-sh-outline" onclick="goToStep(1)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn-sh btn-sh-primary" onclick="goToStep(3)">
                    Next: Gallery <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        {{-- STEP 3: Gallery --}}
        <div class="card-sh p-4 p-md-5 wizard-pane d-none" id="wizardStep3">
            <h4 class="service-section-title">Step 3: Service Image & Gallery</h4>
            
            <div class="mb-4">
                <label class="form-label-sh" for="srvImageUrl">Cover Image URL</label>
                <input type="url" class="form-control-sh w-100" id="srvImageUrl" name="image_url" placeholder="https://images.unsplash.com/photo-1555066931-4365d14bab8c" value="{{ old('image_url') }}" oninput="updateWizardPreview()">
                <span class="text-muted d-block mt-1" style="font-size: 11px;">Leave empty to generate an automatic placeholder image.</span>
            </div>

            <div class="p-4 border rounded bg-alt text-center mb-4">
                <h6 class="mb-2">Image Preview</h6>
                <img id="wizardImagePreview" src="https://placehold.co/900x540/e8e0f5/6D28D9?text=Cover+Preview" alt="Preview" style="max-width: 320px; width: 100%; height: 180px; object-fit: cover; border-radius: var(--radius-md);" class="shadow-sm">
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn-sh btn-sh-outline" onclick="goToStep(2)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn-sh btn-sh-primary" onclick="goToStep(4)">
                    Next: Review & Publish <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        {{-- STEP 4: Review & Publish --}}
        <div class="card-sh p-4 p-md-5 wizard-pane d-none" id="wizardStep4">
            <h4 class="service-section-title">Step 4: Review Marketplace Card Preview</h4>
            
            <p class="text-secondary" style="font-size: var(--fs-sm); margin-bottom: var(--space-4);">
                Here is exactly how your service will appear to clients in the SkillHive marketplace search and category pages:
            </p>

            <div class="row justify-content-center mb-5">
                <div class="col-md-6 col-lg-5">
                    <div class="card-sh service-card">
                        <img id="previewCardImg" src="https://placehold.co/900x540/e8e0f5/6D28D9?text=Service+Card" alt="Preview Card" class="service-card__img">
                        <div class="service-card__body">
                            <div class="service-card__freelancer">
                                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=Student&background=6D28D9&color=fff' }}" alt="User" class="service-card__avatar">
                                <span class="service-card__name">
                                    {{ Auth::user()->name ?? 'Dhruv Patil' }}
                                    <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.75rem;"></i>
                                </span>
                            </div>
                            <div class="service-card__title">
                                <a href="#" id="previewCardTitle">I will build a responsive Laravel website for your project</a>
                            </div>
                            <div class="service-card__meta">
                                <div class="service-card__rating">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <strong>5.0</strong>
                                    <span>(New)</span>
                                </div>
                                <div class="service-card__price">
                                    <span id="previewCardPrice">₹499</span> <span>starting</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn-sh btn-sh-outline" onclick="goToStep(3)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="submit" id="publishBtn" class="btn-sh btn-sh-primary btn-sh-lg">
                    <i class="bi bi-rocket-takeoff-fill me-1"></i> Publish Service to Marketplace
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function clearErrors() {
    ['srvTitle', 'srvCategory', 'srvDescription', 'basicPrice', 'basicDelivery'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.remove('is-invalid');
        const errEl = document.getElementById(id + 'Error');
        if (errEl) {
            errEl.textContent = '';
            errEl.classList.add('d-none');
        }
    });
    const s1Err = document.getElementById('step1Error');
    if (s1Err) s1Err.classList.add('d-none');
    const s2Err = document.getElementById('step2Error');
    if (s2Err) s2Err.classList.add('d-none');
}

function showFieldError(fieldId, message) {
    const el = document.getElementById(fieldId);
    if (el) el.classList.add('is-invalid');
    const errEl = document.getElementById(fieldId + 'Error');
    if (errEl) {
        errEl.textContent = message;
        errEl.classList.remove('d-none');
    }
}

function validateStep1() {
    clearErrors();
    let valid = true;
    const titleEl = document.getElementById('srvTitle');
    const catEl = document.getElementById('srvCategory');
    const descEl = document.getElementById('srvDescription');

    const title = titleEl ? titleEl.value.trim() : '';
    const category = catEl ? catEl.value.trim() : '';
    const description = descEl ? descEl.value.trim() : '';

    if (title.length < 10) {
        showFieldError('srvTitle', 'Service title must be at least 10 characters long.');
        valid = false;
    }
    if (!category) {
        showFieldError('srvCategory', 'Please select a service category.');
        valid = false;
    }
    if (description.length < 20) {
        showFieldError('srvDescription', 'Description must be at least 20 characters long.');
        valid = false;
    }

    if (!valid) {
        const s1Err = document.getElementById('step1Error');
        if (s1Err) s1Err.classList.remove('d-none');
    }
    return valid;
}

function validateStep2() {
    clearErrors();
    let valid = true;
    const priceEl = document.getElementById('basicPrice');
    const delEl = document.getElementById('basicDelivery');

    const price = priceEl ? parseInt(priceEl.value) : 0;
    const delivery = delEl ? parseInt(delEl.value) : 0;

    if (isNaN(price) || price < 100) {
        showFieldError('basicPrice', 'Basic package price must be at least ₹100.');
        valid = false;
    }
    if (isNaN(delivery) || delivery < 1) {
        showFieldError('basicDelivery', 'Delivery time must be at least 1 day.');
        valid = false;
    }

    if (!valid) {
        const s2Err = document.getElementById('step2Error');
        if (s2Err) s2Err.classList.remove('d-none');
    }
    return valid;
}

function goToStep(stepNum) {
    if (stepNum > 1 && !validateStep1()) {
        document.querySelectorAll('.wizard-pane').forEach(p => p.classList.add('d-none'));
        document.getElementById('wizardStep1').classList.remove('d-none');
        setActiveIndicator(1);
        window.scrollTo({ top: 150, behavior: 'smooth' });
        return;
    }

    if (stepNum > 2 && !validateStep2()) {
        document.querySelectorAll('.wizard-pane').forEach(p => p.classList.add('d-none'));
        document.getElementById('wizardStep2').classList.remove('d-none');
        setActiveIndicator(2);
        window.scrollTo({ top: 150, behavior: 'smooth' });
        return;
    }

    document.querySelectorAll('.wizard-pane').forEach(p => p.classList.add('d-none'));
    document.getElementById('wizardStep' + stepNum).classList.remove('d-none');
    setActiveIndicator(stepNum);

    updateWizardPreview();
    window.scrollTo({ top: 150, behavior: 'smooth' });
}

function setActiveIndicator(stepNum) {
    document.querySelectorAll('.wizard-step').forEach((el, index) => {
        el.classList.toggle('active', (index + 1) === stepNum);
    });
}

function updateWizardPreview() {
    const titleEl = document.getElementById('srvTitle');
    const priceEl = document.getElementById('basicPrice');
    const imgEl = document.getElementById('srvImageUrl');

    const title = (titleEl && titleEl.value.trim()) ? titleEl.value.trim() : 'I will build a responsive Laravel website';
    const price = (priceEl && priceEl.value) ? priceEl.value : '499';
    const imageUrl = (imgEl && imgEl.value.trim()) ? imgEl.value.trim() : '';

    const cardTitle = document.getElementById('previewCardTitle');
    if (cardTitle) cardTitle.textContent = title;

    const cardPrice = document.getElementById('previewCardPrice');
    if (cardPrice) cardPrice.textContent = '₹' + parseInt(price || 499).toLocaleString('en-IN');

    const wizImg = document.getElementById('wizardImagePreview');
    const cardImg = document.getElementById('previewCardImg');

    if (imageUrl) {
        if (wizImg) wizImg.src = imageUrl;
        if (cardImg) cardImg.src = imageUrl;
    } else {
        const placeholder = 'https://placehold.co/900x540/e8e0f5/6D28D9?text=' + encodeURIComponent(title.substring(0, 22));
        if (wizImg) wizImg.src = placeholder;
        if (cardImg) cardImg.src = placeholder;
    }
}

function handleFormSubmit(e) {
    e.preventDefault();

    if (!validateStep1()) {
        goToStep(1);
        return false;
    }

    if (!validateStep2()) {
        goToStep(2);
        return false;
    }

    const btn = document.getElementById('publishBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Publishing Service...';
    }

    document.getElementById('createServiceForm').submit();
    return true;
}

document.addEventListener('DOMContentLoaded', () => {
    updateWizardPreview();
});
</script>
<style>
.wizard-step {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: var(--fs-sm);
    color: var(--color-text-secondary);
    font-weight: var(--fw-medium);
    transition: all 0.2s ease;
}
.wizard-step.active {
    color: var(--color-primary);
    font-weight: var(--fw-bold);
}
.wizard-step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--color-border);
    color: var(--color-text);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: var(--fs-xs);
}
.wizard-step.active .wizard-step-num {
    background: var(--color-primary);
    color: #fff;
}
.form-control-sh.is-invalid, .form-select-sh.is-invalid {
    border-color: #dc2626 !important;
    background-image: none !important;
}
</style>
@endpush
