@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm mt-2 space-y-1']) }} style="color:#f87171;">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
