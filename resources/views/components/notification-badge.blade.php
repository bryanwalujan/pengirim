@props(['count' => 0])

@if ($count > 0)
    <span {{ $attributes->merge(['class' => 'badge bg-danger rounded-pill']) }}>
        {{ $count > 99 ? '99+' : $count }}
    </span>
@endif
