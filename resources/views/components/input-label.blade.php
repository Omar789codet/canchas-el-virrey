@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium']) }} style="color:#ececec;">
    {{ $value ?? $slot }}
</label>
