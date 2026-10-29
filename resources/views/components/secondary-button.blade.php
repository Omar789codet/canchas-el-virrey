@props(['type' => 'submit'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150']) }}
    style="background:#262626; color:#ececec; border:1px solid #333;">
    {{ $slot }}
</button>
