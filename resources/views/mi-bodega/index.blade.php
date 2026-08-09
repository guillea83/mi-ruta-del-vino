<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">Mi Ruta del Vino</p>
                <h2 class="mt-1 text-xl font-semibold text-gray-900">Mi Bodega Personal</h2>
            </div>
            <a href="{{ route('mi-bodega.create') }}" class="inline-flex items-center rounded-xl bg-rose-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-950">
                + Guardar un vino
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-7 rounded-3xl bg-gradient-to-br from-rose-950 via-red-900 to-amber-800 p-6 text-white shadow-xl sm:p-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold text-amber-200">Tu memoria del vino</p>
                    <h3 class="mt-2 text-3xl font-bold">Cada botella guarda una historia.</h3>
                    <p class="mt-3 text-sm leading-6 text-rose-100 sm:text-base">Buscá por vino, bodega o varietal y combiná filtros para reencontrar exactamente lo que querés tomar.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('mi-bodega.index') }}" x-data="{ abiertos: {{ $hayFiltros ? 'true' : 'false' }} }" class="mb-7 rounded-3xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input type="search" name="buscar" value="{{ $buscar }}" placeholder="Buscar por vino, bodega o varietal..." class="block min-w-0 flex-1 rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                    <button type="button" @click="abiertos = ! abiertos" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        <span>⚙️ Filtros</span>
                        @if ($hayFiltros)
                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-bold text-rose-800">Activos</span>
                        @endif
                    </button>
                    <button class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-black">Buscar</button>
                </div>

                <div x-show="abiertos" x-cloak class="mt-4 border-t border-gray-100 pt-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="bodega_id" class="block text-sm font-semibold text-gray-700">Bodega</label>
                            <select id="bodega_id" name="bodega_id" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                <option value="">Todas</option>
                                @foreach ($bodegas as $bodega)
                                    <option value="{{ $bodega->id }}" @selected($bodegaId === $bodega->id)>{{ $bodega->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="varietal_id" class="block text-sm font-semibold text-gray-700">Varietal</label>
                            <select id="varietal_id" name="varietal_id" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                <option value="">Todos</option>
                                @foreach ($varietales as $varietal)
                                    <option value="{{ $varietal->id }}" @selected($varietalId === $varietal->id)>{{ $varietal->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="min_copas" class="block text-sm font-semibold text-gray-700">Promedio mínimo</label>
                            <select id="min_copas" name="min_copas" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                <option value="">Cualquier puntaje</option>
                                @foreach ([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5] as $copas)
                                    <option value="{{ $copas }}" @selected($minCopas !== null && (float) $minCopas === (float) $copas)>{{ str_replace('.', ',', (string) $copas) }} copas o más</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="volveria" class="block text-sm font-semibold text-gray-700">¿Lo repetirías?</label>
                            <select id="volveria" name="volveria" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                <option value="">Todos</option>
                                <option value="si" @selected($volveria === 'si')>Sí, marqué que lo volvería a tomar</option>
                                <option value="no" @selected($volveria === 'no')>No, marqué que no lo repetiría</option>
                            </select>
                        </div>

                        <div>
                            <label for="orden" class="block text-sm font-semibold text-gray-700">Orden</label>
                            <select id="orden" name="orden" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                <option value="recientes" @selected($orden === 'recientes')>Últimos actualizados</option>
                                <option value="mejor_valorados" @selected($orden === 'mejor_valorados')>Mejor valorados</option>
                            </select>
                        </div>

                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 sm:self-end">
                            <input type="checkbox" name="favoritos" value="1" @checked($favoritos) class="rounded border-gray-300 text-rose-800 focus:ring-rose-500">
                            <span class="text-sm font-semibold text-gray-700">❤️ Solo favoritos</span>
                        </label>
                    </div>

                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-end">
                        @if ($hayFiltros)
                            <a href="{{ route('mi-bodega.index') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Limpiar todo</a>
                        @endif
                        <button class="rounded-xl bg-rose-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-950">Aplicar filtros</button>
                    </div>
                </div>
            </form>

            <div class="mb-4 flex items-center justify-between gap-3">
                <p class="text-sm text-gray-600">
                    <strong class="text-gray-900">{{ $items->count() }}</strong>
                    {{ $items->count() === 1 ? 'vino encontrado' : 'vinos encontrados' }}
                </p>
                @if ($hayFiltros)
                    <span class="text-xs font-semibold uppercase tracking-wide text-rose-700">Vista filtrada</span>
                @endif
            </div>

            @if ($items->isEmpty())
                <div class="rounded-3xl border-2 border-dashed border-gray-200 bg-white p-10 text-center">
                    <div class="text-5xl">🍷</div>
                    <h3 class="mt-4 text-xl font-bold text-gray-900">{{ $hayFiltros ? 'No encontramos vinos con esos filtros' : 'Tu bodega todavía está vacía' }}</h3>
                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-gray-600">{{ $hayFiltros ? 'Probá aflojando algún filtro o limpiándolos todos.' : 'La próxima vez que tomes un vino, sacale una foto y guardá la experiencia.' }}</p>
                    @if ($hayFiltros)
                        <a href="{{ route('mi-bodega.index') }}" class="mt-5 inline-flex rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">Limpiar filtros</a>
                    @else
                        <a href="{{ route('mi-bodega.create') }}" class="mt-5 inline-flex rounded-xl bg-rose-900 px-5 py-3 text-sm font-bold text-white hover:bg-rose-950">Guardar mi primer vino</a>
                    @endif
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $item)
                        @php
                            $ultima = $item->experiencias->first();
                            $foto = $ultima?->fotos->firstWhere('es_principal', true) ?? $ultima?->fotos->first();
                            $promedioHalf = $item->promedio_medias_copas === null ? null : (int) round($item->promedio_medias_copas);
                        @endphp
                        <a href="{{ route('mi-bodega.show', $item) }}" class="group overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-rose-100 to-amber-50">
                                @if ($foto)
                                    <img src="{{ asset('storage/'.$foto->ruta) }}" alt="{{ $item->vino->nombre }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                                @else
                                    <div class="flex h-full items-center justify-center text-7xl">🍷</div>
                                @endif

                                @if ($item->favorito)
                                    <span class="absolute right-3 top-3 rounded-full bg-white/95 px-3 py-1.5 text-xs font-bold text-rose-800 shadow-sm">❤️ Favorito</span>
                                @endif
                            </div>
                            <div class="p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-rose-700">{{ $item->vino->bodega?->nombre ?? 'Bodega sin definir' }}</p>
                                <h3 class="mt-1 text-xl font-bold text-gray-900">{{ $item->vino->nombre }}</h3>
                                <div class="mt-1 flex flex-wrap gap-x-2 text-sm text-gray-500">
                                    @if ($item->vino->anio)<span>{{ $item->vino->anio }}</span>@endif
                                    @if ($item->vino->varietales->isNotEmpty())<span>· {{ $item->vino->varietales->pluck('nombre')->join(' / ') }}</span>@endif
                                </div>

                                <div class="mt-4">
                                    @if ($promedioHalf !== null)
                                        <x-copas :value="$promedioHalf" />
                                        @if ($item->experiencias_count > 1)
                                            <p class="mt-1 text-xs text-gray-500">Promedio de {{ $item->experiencias_count }} experiencias</p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-500">Tu primera experiencia</p>
                                        @endif
                                    @else
                                        <p class="text-sm text-gray-500">Sin calificación</p>
                                    @endif
                                </div>

                                @if ($ultima)
                                    <div class="mt-4 border-t border-gray-100 pt-4 text-sm text-gray-600">
                                        <p><strong>Última vez:</strong> {{ $ultima->fecha_consumo?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                                        @if ($ultima->lugar)<p class="mt-1 truncate">📍 {{ $ultima->lugar }}</p>@endif
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
