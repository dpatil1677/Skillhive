{{-- Empty State Component --}}
{{-- Usage: @include('components.empty-state', ['icon' => 'bi-search', 'title' => '...', 'text' => '...']) --}}
<div class="empty-state">
    <div class="empty-state__icon">
        <i class="{{ $icon ?? 'bi-inbox' }}"></i>
    </div>
    <h5 class="empty-state__title">{{ $title ?? 'Nothing found' }}</h5>
    <p class="empty-state__text">{{ $text ?? 'Try adjusting your search or filters.' }}</p>
    @if(isset($actionUrl) && isset($actionLabel))
        <a href="{{ $actionUrl }}" class="btn-sh btn-sh-primary">{{ $actionLabel }}</a>
    @endif
</div>
