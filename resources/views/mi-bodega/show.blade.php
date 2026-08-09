<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">Mi Bodega Personal</p>
                <h2 class="mt-1 text-xl font-semibold text-gray-900">{{ $item->vino->nombre }}</h2>
            </div>
            <a href="{{ route('mi-bodega.index') }}" class="text-sm font-semibold text-rose-800 hover:text-rose-950">Volver a mi bodega</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-7 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-semibold">Revisá estos datos:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $ultima = $item->experiencias->first();
                $fotoPrincipal = $ultima?->fotos->firstWhere('es_principal', true) ?? $ultima?->fotos->first();
                $promedioHalf = $item->promedio_medias_copas === null ? null : (int) round($item->promedio_medias_copas);
            @endphp

            <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-rose-950 via-red-900 to-amber-800 text-white shadow-xl">
                <div class="grid md:grid-cols-[0.8fr_1.2fr]">
                    <div class="min-h-72 bg-black/10">
                        @if ($fotoPrincipal)
                            <img src="{{ asset('storage/'.$fotoPrincipal->ruta) }}" alt="{{ $item->vino->nombre }}" class="h-full max-h-[430px] w-full object-cover">
                        @else
                            <div class="flex h-full min-h-72 items-center justify-center text-8xl">🍷</div>
                        @endif
                    </div>
                    <div class="p-6 sm:p-9">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-200">{{ $item->vino->bodega?->nombre ?? 'Bodega sin definir' }}</p>
                                <h1 class="mt-2 text-3xl font-bold sm:text-4xl">{{ $item->vino->nombre }}</h1>
                            </div>

                            <form method="POST" action="{{ route('mi-bodega.favorito', $item) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-4 py-2.5 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                                    <span class="text-lg">{{ $item->favorito ? '❤️' : '🤍' }}</span>
                                    {{ $item->favorito ? 'Favorito' : 'Agregar a favoritos' }}
                                </button>
                            </form>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2 text-sm text-rose-100">
                            @if ($item->vino->anio)<span class="rounded-full bg-white/10 px-3 py-1">Añada {{ $item->vino->anio }}</span>@endif
                            @foreach ($item->vino->varietales as $varietal)
                                <span class="rounded-full bg-white/10 px-3 py-1">{{ $varietal->nombre }}</span>
                            @endforeach
                            @if ($item->vino->region)<span class="rounded-full bg-white/10 px-3 py-1">{{ $item->vino->region }}</span>@endif
                        </div>

                        <div class="mt-7 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white/10 p-4">
                                <p class="text-xs uppercase tracking-wide text-rose-200">Última valoración</p>
                                @if ($ultima?->calificacion_medias_copas !== null)
                                    <p class="mt-2 text-2xl font-bold">{{ number_format($ultima->calificacion_medias_copas / 2, $ultima->calificacion_medias_copas % 2 ? 1 : 0, ',', '.') }} copas</p>
                                @else
                                    <p class="mt-2 text-lg">Sin calificación</p>
                                @endif
                            </div>
                            <div class="rounded-2xl bg-white/10 p-4">
                                <p class="text-xs uppercase tracking-wide text-rose-200">Promedio personal</p>
                                @if ($promedioHalf !== null)
                                    <p class="mt-2 text-2xl font-bold">{{ number_format($promedioHalf / 2, $promedioHalf % 2 ? 1 : 0, ',', '.') }} copas</p>
                                    <p class="mt-1 text-xs text-rose-100">{{ $item->experiencias->count() }} {{ $item->experiencias->count() === 1 ? 'experiencia' : 'experiencias' }}</p>
                                @else
                                    <p class="mt-2 text-lg">Sin promedio</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-rose-700">Tu historia con este vino</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-900">Experiencias</h3>
                    </div>
                    <p class="text-sm text-gray-500">Cada vez que lo tomás queda como un recuerdo separado.</p>
                </div>

                <div class="mt-6 space-y-5">
                    @forelse ($item->experiencias as $experiencia)
                        <article x-data="{ editando: false }" class="rounded-2xl border border-gray-200 p-4 sm:p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-500">{{ $experiencia->fecha_consumo?->format('d/m/Y') ?? 'Fecha sin registrar' }}</p>
                                    @if ($experiencia->calificacion_medias_copas !== null)
                                        <div class="mt-2"><x-copas :value="$experiencia->calificacion_medias_copas" /></div>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($experiencia->volveria_a_tomar !== null)
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $experiencia->volveria_a_tomar ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $experiencia->volveria_a_tomar ? 'Lo volvería a tomar' : 'No lo repetiría por ahora' }}
                                        </span>
                                    @endif
                                    <button type="button" @click="editando = !editando" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                        <span x-text="editando ? 'Cancelar edición' : 'Editar'"></span>
                                    </button>
                                </div>
                            </div>

                            @if ($experiencia->fotos->isNotEmpty())
                                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    @foreach ($experiencia->fotos as $foto)
                                        <img src="{{ asset('storage/'.$foto->ruta) }}" alt="Foto de {{ $item->vino->nombre }}" class="aspect-square w-full rounded-xl object-cover">
                                    @endforeach
                                </div>
                            @endif

                            <div x-show="!editando" class="mt-4 grid gap-3 text-sm text-gray-700 sm:grid-cols-2">
                                @if ($experiencia->lugar)<p>📍 <strong>Dónde:</strong> {{ $experiencia->lugar }}</p>@endif
                                @if ($experiencia->acompanamiento)<p>🍽️ <strong>Con qué:</strong> {{ $experiencia->acompanamiento }}</p>@endif
                                @if ($experiencia->notas_cata)<p class="sm:col-span-2"><strong>Notas:</strong> {{ $experiencia->notas_cata }}</p>@endif
                                @if ($experiencia->recuerdo)<p class="sm:col-span-2 rounded-xl bg-amber-50 p-3"><strong>Recuerdo:</strong> {{ $experiencia->recuerdo }}</p>@endif
                            </div>

                            <form x-show="editando" x-cloak method="POST" action="{{ route('mi-bodega.experiencias.update', [$item, $experiencia]) }}" enctype="multipart/form-data" class="mt-5 space-y-5 rounded-2xl bg-gray-50 p-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="mb-3 block text-sm font-semibold text-gray-700">Calificación</label>
                                    <x-copa-selector :value="$experiencia->calificacion_medias_copas ?? 0" />
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">Fecha</label>
                                        <input name="fecha_consumo" type="date" value="{{ $experiencia->fecha_consumo?->format('Y-m-d') }}" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">Lugar</label>
                                        <input name="lugar" value="{{ $experiencia->lugar }}" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700">¿Con qué lo acompañaste?</label>
                                        <input name="acompanamiento" value="{{ $experiencia->acompanamiento }}" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700">Notas</label>
                                        <textarea name="notas_cata" rows="3" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">{{ $experiencia->notas_cata }}</textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700">Recuerdo</label>
                                        <textarea name="recuerdo" rows="3" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">{{ $experiencia->recuerdo }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">¿Lo volverías a tomar?</label>
                                        <select name="volveria_a_tomar" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                            <option value="" @selected($experiencia->volveria_a_tomar === null)>Todavía no sé</option>
                                            <option value="1" @selected($experiencia->volveria_a_tomar === true)>Sí</option>
                                            <option value="0" @selected($experiencia->volveria_a_tomar === false)>No por ahora</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">Agregar otra foto</label>
                                        <input type="file" name="foto" accept="image/*" class="mt-2 block w-full text-sm text-gray-600">
                                        <p class="mt-1 text-xs text-gray-500">Podés sacar una foto o elegir una de tu galería.</p>
                                    </div>
                                </div>

                                <button type="submit" class="w-full rounded-xl bg-rose-900 px-5 py-3 font-bold text-white hover:bg-rose-950">Guardar cambios</button>
                            </form>

                            <form method="POST" action="{{ route('mi-bodega.experiencias.destroy', [$item, $experiencia]) }}" class="mt-4 border-t border-gray-100 pt-4" onsubmit="return confirm('¿Eliminar esta experiencia? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Eliminar esta experiencia</button>
                            </form>
                        </article>
                    @empty
                        <p class="text-sm text-gray-500">Todavía no hay experiencias registradas.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-rose-200 bg-rose-50/50 p-5 sm:p-7" x-data="{ abierto: false }">
                <button type="button" @click="abierto = !abierto" class="flex w-full items-center justify-between gap-4 text-left">
                    <div>
                        <p class="text-sm font-semibold text-rose-700">¿Lo volviste a tomar?</p>
                        <h3 class="mt-1 text-xl font-bold text-gray-900">Agregar una nueva experiencia</h3>
                    </div>
                    <span class="text-2xl text-rose-800" x-text="abierto ? '−' : '+'"></span>
                </button>

                <form x-show="abierto" x-cloak method="POST" action="{{ route('mi-bodega.experiencias.store', $item) }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label class="mb-3 block text-sm font-semibold text-gray-700">Nueva calificación</label>
                        <x-copa-selector :value="old('calificacion_medias_copas', 0)" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Fecha</label>
                            <input name="fecha_consumo" type="date" value="{{ old('fecha_consumo', now()->toDateString()) }}" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Lugar</label>
                            <input name="lugar" value="{{ old('lugar') }}" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Bar, casa, bodega...">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700">¿Con qué lo acompañaste?</label>
                            <input name="acompanamiento" value="{{ old('acompanamiento') }}" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Pastas, asado, quesos...">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700">Notas</label>
                            <textarea name="notas_cata" rows="3" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">{{ old('notas_cata') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700">Recuerdo</label>
                            <textarea name="recuerdo" rows="3" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">{{ old('recuerdo') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">¿Lo volverías a tomar?</label>
                            <select name="volveria_a_tomar" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                <option value="">Todavía no sé</option>
                                <option value="1">Sí</option>
                                <option value="0">No por ahora</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nueva foto</label>
                            <input type="file" name="foto" accept="image/*" class="mt-2 block w-full text-sm text-gray-600">
                            <p class="mt-1 text-xs text-gray-500">Podés sacar una foto o elegir una de tu galería.</p>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-rose-900 px-5 py-3.5 font-bold text-white hover:bg-rose-950">Guardar nueva experiencia</button>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>