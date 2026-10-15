@extends('layouts.guest')

@section('title', 'Portfolio Manager — SkillHive')

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Seller Dashboard', 'url' => route('seller.dashboard')],
        ['label' => 'Portfolio Manager'],
    ]])

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Portfolio Manager</h1>
            <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Showcase your best student projects to clients on your profile.</p>
        </div>
        <button type="button" class="btn-sh btn-sh-primary" data-bs-toggle="modal" data-bs-target="#addPortfolioModal">
            <i class="bi bi-plus-lg me-1"></i> Add New Project
        </button>
    </div>

    {{-- Portfolio Items Grid --}}
    <div class="row g-4 mb-5">
        @forelse($portfolio as $item)
            <div class="col-lg-4 col-md-6">
                <div class="card-sh portfolio-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="portfolio-card__img-wrap position-relative">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="portfolio-card__img">
                            <div class="position-absolute top-0 end-0 p-2 d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-light shadow-sm py-1 px-2" onclick="editPortfolioItem({{ json_encode($item) }})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger shadow-sm py-1 px-2" onclick="confirmDeletePortfolio('{{ $item['id'] }}', '{{ addslashes($item['title']) }}')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="portfolio-card__body">
                            <div class="portfolio-card__title">
                                <a href="{{ route('portfolio.show', ['freelancerSlug' => $user['slug'] ?? 'dhruv-patil', 'portfolioSlug' => $item['slug']]) }}">
                                    {{ $item['title'] }}
                                </a>
                            </div>
                            <p class="portfolio-card__desc">{{ $item['description'] }}</p>
                            <div class="portfolio-card__techs mb-3">
                                @foreach($item['technologies'] as $tech)
                                    <span class="portfolio-tech">{{ $tech }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-alt border-top-sh d-flex align-items-center justify-content-between">
                        <a href="{{ route('portfolio.show', ['freelancerSlug' => $user['slug'] ?? 'dhruv-patil', 'portfolioSlug' => $item['slug']]) }}" class="btn-sh btn-sh-outline btn-sh-sm">
                            View Showcase
                        </a>
                        @if(isset($item['project_url']) && $item['project_url'] !== '#')
                            <a href="{{ $item['project_url'] }}" target="_blank" class="text-primary text-decoration-none" style="font-size: var(--fs-xs);">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Live URL
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                @include('components.empty-state', [
                    'icon' => 'bi-images',
                    'title' => 'No portfolio projects added yet',
                    'text' => 'Showcase your work to build trust with buyers.',
                    'actionUrl' => '#',
                    'actionLabel' => 'Add First Project'
                ])
            </div>
        @endforelse
    </div>
</div>

{{-- Add Portfolio Modal --}}
<div class="modal fade" id="addPortfolioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i> Add Portfolio Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('seller.portfolio.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-sh" for="portTitle">Project Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="portTitle" name="title" placeholder="Campus Connect — Student Event Platform" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="portDesc">Project Description <span class="text-danger">*</span></label>
                        <textarea class="form-control-sh w-100" id="portDesc" name="description" rows="4" placeholder="Describe what you built, the problem solved, and key features..." required></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="portTech">Technologies (comma-separated)</label>
                            <input type="text" class="form-control-sh w-100" id="portTech" name="technologies" placeholder="Laravel, MySQL, Bootstrap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="portCategory">Category</label>
                            <input type="text" class="form-control-sh w-100" id="portCategory" name="category" value="Web Development">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="portImg">Cover Image URL</label>
                            <input type="url" class="form-control-sh w-100" id="portImg" name="image_url" placeholder="https://placehold.co/800x500/e8e0f5/6D28D9?text=Project+Cover">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="portUrl">Live Demo URL (Optional)</label>
                            <input type="url" class="form-control-sh w-100" id="portUrl" name="project_url" placeholder="https://myproject.com">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Add Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Portfolio Modal --}}
<div class="modal fade" id="editPortfolioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Portfolio Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPortfolioForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-sh" for="editPortTitle">Project Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="editPortTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="editPortDesc">Project Description <span class="text-danger">*</span></label>
                        <textarea class="form-control-sh w-100" id="editPortDesc" name="description" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="editPortTech">Technologies (comma-separated)</label>
                        <input type="text" class="form-control-sh w-100" id="editPortTech" name="technologies" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editPortImg">Cover Image URL</label>
                            <input type="url" class="form-control-sh w-100" id="editPortImg" name="image_url">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editPortUrl">Live Demo URL</label>
                            <input type="url" class="form-control-sh w-100" id="editPortUrl" name="project_url">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Portfolio Modal --}}
<div class="modal fade" id="deletePortfolioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash-fill me-2"></i> Confirm Delete Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deletePortName">this project</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="deletePortfolioForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn-sh btn-sh-danger">Delete Project</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editPortfolioItem(item) {
    document.getElementById('editPortTitle').value = item.title;
    document.getElementById('editPortDesc').value = item.description;
    document.getElementById('editPortTech').value = Array.isArray(item.technologies) ? item.technologies.join(', ') : item.technologies;
    document.getElementById('editPortImg').value = item.image;
    document.getElementById('editPortUrl').value = item.project_url || '';
    document.getElementById('editPortfolioForm').action = "/seller/portfolio/" + item.id + "/update";

    const modal = new bootstrap.Modal(document.getElementById('editPortfolioModal'));
    modal.show();
}

function confirmDeletePortfolio(id, title) {
    document.getElementById('deletePortName').textContent = '"' + title + '"';
    document.getElementById('deletePortfolioForm').action = "/seller/portfolio/" + id + "/delete";
    const modal = new bootstrap.Modal(document.getElementById('deletePortfolioModal'));
    modal.show();
}
</script>
@endpush
