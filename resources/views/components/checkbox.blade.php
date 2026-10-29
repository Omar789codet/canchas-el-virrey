@props(['disabled' => false])

<input type="checkbox" @disabled($disabled) {{ $attributes->merge(['class' => 'rounded shadow-sm']) }}
    style="background:#262626; border:1px solid #333; accent-color:#22c55e;">
