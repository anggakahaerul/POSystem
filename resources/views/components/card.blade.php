{{--
    Card Component
    
    Usage:
    @include('components.card', [
        'title' => 'Card Title',
        'icon' => 'fas fa-list',
        'color' => 'primary',
        'tools' => '<a href="#" class="btn btn-tool"><i class="fas fa-wrench"></i></a>',
        'footer' => 'Card footer content',
        'collapsed' => false,
        'removable' => false,
        'content' => 'Card body content here'
    ])
    
    Or use as a wrapper:
    @component('components.card', ['title' => 'My Card'])
        Your card content here
    @endcomponent
--}}

<div class="card card-{{ $color ?? 'default' }} {{ isset($outline) && $outline ? 'card-outline' : '' }}">
    @if(isset($title))
    <div class="card-header">
        <h3 class="card-title">
            @if(isset($icon))<i class="{{ $icon }} mr-2"></i>@endif
            {{ $title }}
        </h3>
        <div class="card-tools">
            @if(isset($tools))
                {!! $tools !!}
            @endif
            @if(isset($collapsible) && $collapsible)
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas {{ isset($collapsed) && $collapsed ? 'fa-plus' : 'fa-minus' }}"></i>
                </button>
            @endif
            @if(isset($maximizable) && $maximizable)
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            @endif
            @if(isset($removable) && $removable)
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </div>
    @endif
    
    <div class="card-body {{ isset($collapsed) && $collapsed ? 'collapse' : '' }}" {{ isset($bodyStyle) ? 'style='.$bodyStyle : '' }}>
        @if(isset($content))
            {!! $content !!}
        @else
            {{ $slot ?? '' }}
        @endif
    </div>
    
    @if(isset($footer))
    <div class="card-footer">
        {!! $footer !!}
    </div>
    @endif
</div>
