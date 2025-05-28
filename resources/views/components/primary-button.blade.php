@props(['class' => '', 'disabled' => false])

<button {{ $attributes->merge(['class' => 'btn-primary']) }}>
    {{ $slot }}
</button>
