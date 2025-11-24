@props(['items' => []])

<div class="space-y-3">
    @foreach ($items as $item)
        <div class="flex items-start">
            <!-- ICON -->
            <span class="flex-shrink-0 mt-1 text-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>

            <!-- TEKS LIST -->
            <span class="ml-3 text-[15px] leading-relaxed text-gray-700">
                {{ $item }}
            </span>
        </div>
    @endforeach
</div>