@props(['class' => '', 'disabled' => false])

<button {{ $attributes->merge(['class' => 'btn-danger']) }}>
    {{ $slot }}
</button>
