@props(['name' => 'calificacion_medias_copas', 'value' => 0])

@php
    $ratingInicial = (int) $value;
    $idBase = str_replace(['[', ']', '.'], '_', $name);
@endphp

<div x-data="{ rating: {{ $ratingInicial }} }" class="space-y-3">
    <div class="flex items-center gap-1.5" role="radiogroup" aria-label="Calificación de 0 a 5 copas">
        @for ($i = 0; $i < 5; $i++)
            @php
                $media = $i * 2 + 1;
                $entera = $i * 2 + 2;
            @endphp

            <div class="relative h-10 w-10 shrink-0">
                <svg class="pointer-events-none absolute inset-0 h-10 w-10 text-gray-200" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="currentColor" d="M7 2h10l-1 7a5 5 0 0 1-3 4.58V20h3v2H8v-2h3v-6.42A5 5 0 0 1 8 9L7 2Zm2.28 2L10 9a3 3 0 0 0 4 2.82A3 3 0 0 0 16 9l.72-5H9.28Z"/>
                </svg>

                <div class="pointer-events-none absolute inset-y-0 left-0 overflow-hidden"
                     :style="{ width: (Math.min(Math.max(rating - {{ $i * 2 }}, 0), 2) * 50) + '%' }">
                    <svg class="h-10 w-10 max-w-none text-rose-800" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M7 2h10l-1 7a5 5 0 0 1-3 4.58V20h3v2H8v-2h3v-6.42A5 5 0 0 1 8 9L7 2Zm2.28 2L10 9a3 3 0 0 0 4 2.82A3 3 0 0 0 16 9l.72-5H9.28Z"/>
                    </svg>
                </div>

                <input
                    id="{{ $idBase }}_{{ $media }}"
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $media }}"
                    x-model.number="rating"
                    @checked($ratingInicial === $media)
                    aria-label="{{ $i + 0.5 }} copas"
                    class="absolute inset-y-0 left-0 w-1/2 cursor-pointer rounded-l border-0"
                    style="z-index: 10; opacity: 0; touch-action: manipulation;"
                >

                <input
                    id="{{ $idBase }}_{{ $entera }}"
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $entera }}"
                    x-model.number="rating"
                    @checked($ratingInicial === $entera)
                    aria-label="{{ $i + 1 }} copas"
                    class="absolute inset-y-0 right-0 w-1/2 cursor-pointer rounded-r border-0"
                    style="z-index: 10; opacity: 0; touch-action: manipulation;"
                >
            </div>
        @endfor
    </div>

    <div class="flex items-center gap-3 text-sm">
        <input
            id="{{ $idBase }}_0"
            type="radio"
            name="{{ $name }}"
            value="0"
            x-model.number="rating"
            @checked($ratingInicial === 0)
            class="sr-only"
        >
        <label
            for="{{ $idBase }}_0"
            class="cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-2 font-medium text-gray-700 hover:bg-gray-50"
            style="touch-action: manipulation;"
        >0 copas</label>

        <strong class="text-rose-900" x-text="(rating / 2).toLocaleString('es-AR', { minimumFractionDigits: rating % 2 ? 1 : 0, maximumFractionDigits: 1 }) + ' copas'">
            {{ number_format($ratingInicial / 2, $ratingInicial % 2 ? 1 : 0, ',', '.') }} copas
        </strong>
    </div>
</div>
