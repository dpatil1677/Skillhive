{{-- Breadcrumb Component --}}
{{-- Usage: @include('components.breadcrumb', ['items' => [['label' => 'Home', 'url' => '/'], ['label' => 'Explore']]]) --}}
<nav aria-label="breadcrumb">
    <ol class="breadcrumb-sh">
        @foreach($items as $index => $item)
            @if($index > 0)
                <span class="separator"><i class="bi bi-chevron-right"></i></span>
            @endif
            <li class="{{ $loop->last ? 'active' : '' }}">
                @if(!$loop->last && isset($item['url']))
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @else
                    {{ $item['label'] }}
                @endif
            </li>
        @endforeach
    </ol>
</nav>
