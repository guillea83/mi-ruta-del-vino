<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-700">Tu historia con el vino</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900">Mi Ruta del Vino</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-rose-950 via-red-900 to-amber-800 shadow-2xl">
                <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-28 left-1/3 h-72 w-72 rounded-full bg-amber-300/10 blur-3xl"></div>

                <div class="relative grid gap-10 px-6 py-10 sm:px-10 sm:py-14 lg:grid-cols-[1.2fr_.8fr] lg:items-center lg:px-14 lg:py-16">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1 text-sm font-medium text-rose-50 backdrop-blur">
                            Bienvenido, {{ auth()->user()->name }}
                        </span>

                        <h1 class="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">Tu memoria personal del vino.</h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-rose-100 sm:text-xl">Guardá las botellas que probaste, calificá cada experiencia con copas y recordá dónde, cuándo y con qué la disfrutaste.</p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('mi-bodega.create') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-rose-900 shadow-sm transition hover:bg-rose-50">🍷 Guardar un vino</a>
                            <a href="{{ route('mi-bodega.index') }}" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">Mi Bodega Personal</a>
                        </div>
                    </div>

                    <div class="lg:justify-self-end">
                        <div class="w-full max-w-sm rounded-3xl border border-white/15 bg-white/10 p-5 text-white shadow-xl backdrop-blur-md">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200">La bitácora</p>
                            <p class="mt-2 text-xl font-bold">Cada vino puede tener muchas experiencias.</p>
                            <div class="mt-5 space-y-3 text-sm text-rose-100">
                                <p class="rounded-2xl bg-black/10 p-3">📸 Foto de la botella o etiqueta</p>
                                <p class="rounded-2xl bg-black/10 p-3">🍷 0 a 5 copas, incluyendo medias copas</p>
                                <p class="rounded-2xl bg-black/10 p-3">📍 Lugar, comida, fecha, notas y recuerdos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-10 grid gap-5 md:grid-cols-3">
                <a href="{{ route('mi-bodega.create') }}" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-2xl">📸</div>
                    <h3 class="mt-5 text-lg font-semibold text-gray-900">Guardar un vino</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Sacá una foto, identificá el vino y registrá cómo fue esa experiencia.</p>
                </a>

                <a href="{{ route('mi-bodega.index') }}" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-2xl">🏠</div>
                    <h3 class="mt-5 text-lg font-semibold text-gray-900">Mi Bodega Personal</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Reencontrá los vinos que ya tomaste y todas tus experiencias con cada uno.</p>
                </a>

                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-200 text-2xl">🌐</div>
                    <h3 class="mt-5 text-lg font-semibold text-gray-900">Próximamente</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Información de bodegas, amigos, eventos, recomendaciones, beneficios y dónde conseguir cada vino.</p>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
