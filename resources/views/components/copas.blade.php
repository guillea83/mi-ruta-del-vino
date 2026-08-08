@props(['value' => 0, 'showLabel' => true])

@php
    $halfUnits = max(0, min(10, (int) round($value ?? 0)));
@endphp

<div class="flex flex-wrap items-center gap-1.5">
    @for ($i = 0; $i < 5; $i++)
        @php
            $fill = max(0, min(2, $halfUnits - ($i * 2))) * 50;
        @endphp
        <div class="relative h-7 w-7 shrink-0">
            <svg class="absolute inset-0 h-7 w-7 text-gray-200" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor" d="M7 2h10l-1 7a5 5 0 0 1-3 4.58V20h3v2H8v-2h3v-6.42A5 5 0 0 1 8 9L7 2Zm2.28 2L10 9a3 3 0 0 0 4 2.82A3 3 0 0 0 16 9l.72-5H9.28Z"/>
            </svg>
            <div class="absolute inset-y-0 left-0 overflow-hidden" style="width: {{ $fill }}%">
                <svg class="h-7 w-7 max-w-none text-rose-800" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="currentColor" d="M7 2h10l-1 7a5 5 0 0 1-3 4.58V20h3v2H8v-2h3v-6.42A5 5 0 0 1 8 9L7 2Zm2.28 2L10 9a3 3 0 0 0 4 2.82A3 3 0 0 0 16 9l.72-5H9.28Z"/>
                </svg>
            </div>
        </div>
    @endfor

    @if ($showLabel)
        <span class="ml-1 text-sm font-semibold text-rose-900">{{ number_format($halfUnits / 2, $halfUnits % 2 ? 1 : 0, ',', '.') }} copas</span>
    @endif
</div>
