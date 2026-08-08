@props(['name' => 'calificacion_medias_copas', 'value' => 0])

<div x-data="{ rating: {{ (int) $value }} }" class="space-y-3">
    <input type="hidden" name="{{ $name }}" :value="rating">

    <div class="flex items-center gap-1.5" aria-label="Calificación de 0 a 5 copas">
        @for ($i = 0; $i < 5; $i++)
            <div class="relative h-10 w-10 shrink-0">
                <svg class="absolute inset-0 h-10 w-10 text-gray-200" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="currentColor" d="M7 2h10l-1 7a5 5 0 0 1-3 4.58V20h3v2H8v-2h3v-6.42A5 5 0 0 1 8 9L7 2Zm2.28 2L10 9a3 3 0 0 0 4 2.82A3 3 0 0 0 16 9l.72-5H9.28Z"/>
                </svg>
                <div class="absolute inset-y-0 left-0 overflow-hidden" :style="{ width: (Math.min(Math.max(rating - {{ $i * 2 }}, 0), 2) * 50) + '%' }">
                    <svg class="h-10 w-10 max-w-none text-rose-800" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M7 2h10l-1 7a5 5 0 0 1-3 4.58V20h3v2H8v-2h3v-6.42A5 5 0 0 1 8 9L7 2Zm2.28 2L10 9a3 3 0 0 0 4 2.82A3 3 0 0 0 16 9l.72-5H9.28Z"/>
                    </svg>
                </div>
                <button type="button" class="absolute inset-y-0 left-0 w-1/2 rounded-l focus:outline-none focus:ring-2 focus:ring-rose-500" @click="rating = {{ $i * 2 + 1 }}" aria-label="{{ $i + 0.5 }} copas"></button>
                <button type="button" class="absolute inset-y-0 right-0 w-1/2 rounded-r focus:outline-none focus:ring-2 focus:ring-rose-500" @click="rating = {{ $i * 2 + 2 }}" aria-label="{{ $i + 1 }} copas"></button>
            </div>
        @endfor
    </div>

    <div class="flex items-center gap-3 text-sm">
        <button type="button" class="rounded-lg border border-gray-300 bg-white px-3 py-2 font-medium text-gray-700 hover:bg-gray-50" @click="rating = 0">0 copas</button>
        <strong class="text-rose-900" x-text="(rating / 2).toLocaleString('es-AR', { minimumFractionDigits: rating % 2 ? 1 : 0, maximumFractionDigits: 1 }) + ' copas'"></strong>
    </div>
</div>
