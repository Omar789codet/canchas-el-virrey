@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md shadow-sm w-full']) }}
    style="background:#262626; color:#ececec; border:1px solid #333;">
