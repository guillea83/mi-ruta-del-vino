<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">Mi Bodega Personal</p>
                <h2 class="mt-1 text-xl font-semibold text-gray-900">Guardar un vino</h2>
            </div>
            <a href="{{ route('mi-bodega.index') }}" class="text-sm font-semibold text-rose-800 hover:text-rose-950">Volver a mi bodega</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('mi-bodega.store') }}" enctype="multipart/form-data"
                  x-data="{ modo: '{{ old('vino_id') ? 'existente' : 'nuevo' }}', vinoId: '{{ old('vino_id', '') }}' }"
                  class="space-y-6">
                @csrf

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

                <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-2xl">📸</div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">1. La foto del recuerdo</h3>
                            <p class="mt-1 text-sm text-gray-600">En el celular podés sacar la foto en el momento o elegir una de la galería.</p>
                        </div>
                    </div>
                    <label class="mt-5 block cursor-pointer rounded-2xl border-2 border-dashed border-rose-200 bg-rose-50/60 p-6 text-center hover:bg-rose-50">
                        <span class="block text-3xl">🍷</span>
                        <span class="mt-2 block font-semibold text-rose-900">Sacar foto o elegir imagen</span>
                        <span class="mt-1 block text-xs text-gray-500">JPG, PNG o imagen del teléfono · hasta 10 MB</span>
                        <input type="file" name="foto" accept="image/*" capture="environment" class="mt-4 block w-full text-sm text-gray-600">
                    </label>
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
                    <h3 class="text-lg font-bold text-gray-900">2. ¿Qué vino es?</h3>
                    <p class="mt-1 text-sm text-gray-600">Usá uno que ya exista en el catálogo o agregá uno nuevo.</p>

                    <div class="mt-5 grid grid-cols-2 gap-2 rounded-xl bg-gray-100 p-1">
                        <button type="button" @click="modo='nuevo'; vinoId=''" :class="modo === 'nuevo' ? 'bg-white text-rose-900 shadow-sm' : 'text-gray-600'" class="rounded-lg px-3 py-2.5 text-sm font-semibold">Nuevo vino</button>
                        <button type="button" @click="modo='existente'" :class="modo === 'existente' ? 'bg-white text-rose-900 shadow-sm' : 'text-gray-600'" class="rounded-lg px-3 py-2.5 text-sm font-semibold">Ya existe</button>
                    </div>

                    <div x-show="modo === 'existente'" x-cloak class="mt-5">
                        <label for="vino_id" class="block text-sm font-semibold text-gray-700">Elegí el vino</label>
                        <select id="vino_id" name="vino_id" x-model="vinoId" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                            <option value="">Seleccionar...</option>
                            @foreach ($vinos as $vino)
                                <option value="{{ $vino->id }}">
                                    {{ $vino->nombre }} — {{ $vino->bodega?->nombre ?? 'Sin bodega' }}{{ $vino->anio ? ' ('.$vino->anio.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="modo === 'nuevo'" x-cloak class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="nombre" class="block text-sm font-semibold text-gray-700">Nombre del vino *</label>
                            <input id="nombre" name="nombre" value="{{ old('nombre') }}" type="text" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Ej. Juancito Reserva">
                        </div>
                        <div>
                            <label for="bodega_nombre" class="block text-sm font-semibold text-gray-700">Bodega *</label>
                            <input id="bodega_nombre" name="bodega_nombre" value="{{ old('bodega_nombre') }}" type="text" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Ej. La Rosa">
                        </div>
                        <div>
                            <label for="varietal_nombre" class="block text-sm font-semibold text-gray-700">Varietal</label>
                            <input id="varietal_nombre" name="varietal_nombre" value="{{ old('varietal_nombre') }}" type="text" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Malbec, Pinot Noir...">
                        </div>
                        <div>
                            <label for="anio" class="block text-sm font-semibold text-gray-700">Añada</label>
                            <input id="anio" name="anio" value="{{ old('anio') }}" type="number" min="1800" max="2100" inputmode="numeric" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="2024">
                        </div>
                        <div>
                            <label for="region" class="block text-sm font-semibold text-gray-700">Región</label>
                            <input id="region" name="region" value="{{ old('region') }}" type="text" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Valle de Uco, Mendoza...">
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
                    <h3 class="text-lg font-bold text-gray-900">3. Tu experiencia</h3>
                    <p class="mt-1 text-sm text-gray-600">La opinión queda guardada en el tiempo. Si volvés a tomarlo, vas a poder registrar otra distinta.</p>

                    <div class="mt-6">
                        <label class="mb-3 block text-sm font-semibold text-gray-700">¿Cuánto te gustó?</label>
                        <x-copa-selector :value="old('calificacion_medias_copas', 0)" />
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="fecha_consumo" class="block text-sm font-semibold text-gray-700">¿Cuándo?</label>
                            <input id="fecha_consumo" name="fecha_consumo" value="{{ old('fecha_consumo', now()->toDateString()) }}" type="date" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                        </div>
                        <div>
                            <label for="lugar" class="block text-sm font-semibold text-gray-700">¿Dónde lo tomaste?</label>
                            <input id="lugar" name="lugar" value="{{ old('lugar') }}" type="text" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Casa, bar, restaurante, bodega...">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="acompanamiento" class="block text-sm font-semibold text-gray-700">¿Con qué lo acompañaste?</label>
                            <input id="acompanamiento" name="acompanamiento" value="{{ old('acompanamiento') }}" type="text" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Asado, pastas, quesos, pescado...">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="notas_cata" class="block text-sm font-semibold text-gray-700">Tus notas</label>
                            <textarea id="notas_cata" name="notas_cata" rows="3" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Frutado, suave, mucha madera...">{{ old('notas_cata') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="recuerdo" class="block text-sm font-semibold text-gray-700">El recuerdo</label>
                            <textarea id="recuerdo" name="recuerdo" rows="3" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500" placeholder="Lo tomamos en vacaciones, cena con amigos...">{{ old('recuerdo') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="volveria_a_tomar" class="block text-sm font-semibold text-gray-700">¿Lo volverías a tomar?</label>
                            <select id="volveria_a_tomar" name="volveria_a_tomar" class="mt-2 block w-full rounded-xl border-gray-300 text-base focus:border-rose-500 focus:ring-rose-500">
                                <option value="">Todavía no sé</option>
                                <option value="1" @selected(old('volveria_a_tomar') === '1')>Sí, de una</option>
                                <option value="0" @selected(old('volveria_a_tomar') === '0')>No por ahora</option>
                            </select>
                        </div>
                    </div>
                </section>

                <button type="submit" class="w-full rounded-2xl bg-rose-900 px-5 py-4 text-base font-bold text-white shadow-lg shadow-rose-900/20 transition hover:bg-rose-950 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">Guardar en Mi Bodega Personal</button>
            </form>
        </div>
    </div>
</x-app-layout>
