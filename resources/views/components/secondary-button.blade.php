@props(['class' => '', 'disabled' => false])

<button {{ $attributes->merge(['class' => 'btn-secondary']) }}>
    {{ $slot }}
</button>
