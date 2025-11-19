{{-- resources/views/components/pp-text.blade.php --}}
<p {{ $attributes->merge(['class' => 'section-text']) }}>
    {{ $slot }}
</p>