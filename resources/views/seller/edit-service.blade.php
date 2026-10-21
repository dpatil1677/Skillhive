@extends('layouts.guest')

@section('title', 'Edit Service — ' . $service['title'])

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Seller Dashboard', 'url' => route('seller.dashboard')],
        ['label' => 'My Services', 'url' => route('seller.services')],
        ['label' => 'Edit Service'],
    ]])

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card-sh p-4 p-md-5">
                <h2 style="font-size: var(--fs-xl); font-weight: var(--fw-bold);" class="mb-4">Edit Service Listing</h2>

                <form action="{{ route('seller.services.update', $service['id']) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label-sh" for="srvTitle">Service Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="srvTitle" name="title" value="{{ old('title', $service['title']) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-sh" for="srvCategory">Category <span class="text-danger">*</span></label>
                        <select class="form-select-sh w-100" id="srvCategory" name="category" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['name'] }}" {{ (($service->category_name ?? ($service['category_name'] ?? '')) === $cat['name'] || ($service['category_id'] ?? 0) === $cat['id']) ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-sh" for="srvDescription">Service Description <span class="text-danger">*</span></label>
                        <textarea class="form-control-sh w-100" id="srvDescription" name="description" rows="6" required>{{ old('description', $service['description']) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-sh" for="srvImage">Cover Image URL</label>
                        <input type="url" class="form-control-sh w-100" id="srvImage" name="image_url" value="{{ old('image_url', $service['image']) }}">
                    </div>

                    <h5 class="service-section-title mt-5">Packages Pricing</h5>
                    
                    <div class="row g-3 mb-5">
                        <div class="col-md-4">
                            <label class="form-label-sh">Basic Price (₹)</label>
                            <input type="number" class="form-control-sh w-100" name="basic_price" value="{{ $service['packages']['basic']['price'] ?? 499 }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sh">Standard Price (₹)</label>
                            <input type="number" class="form-control-sh w-100" name="standard_price" value="{{ $service['packages']['standard']['price'] ?? 999 }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sh">Premium Price (₹)</label>
                            <input type="number" class="form-control-sh w-100" name="premium_price" value="{{ $service['packages']['premium']['price'] ?? 1999 }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('seller.services') }}" class="btn-sh btn-sh-outline">Cancel</a>
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
