@props(['number' => null])

<h2 class="flex items-center gap-2 text-[16px] font-semibold text-gray-800 mb-3 leading-snug">
    @if ($number)
        <span class="text-[16px] font-semibold text-gray-900">{{ $number }}.</span>
    @endif

    <span class="text-[16px] font-semibold text-gray-800">
        {{ $slot }}
    </span>
</h2>