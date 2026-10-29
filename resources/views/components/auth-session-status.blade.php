@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm']) }} style="color:#4ade80;">
        {{ $status }}
    </div>
@endif
