<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-700">Experiencias entre viñedos</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900">
                    Mi Ruta del Vino
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-rose-950 via-red-900 to-amber-800 shadow-2xl">
                <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-28 left-1/3 h-72 w-72 rounded-full bg-amber-300/10 blur-3xl"></div>

                <div class="relative grid gap-10 px-6 py-10 sm:px-10 sm:py-14 lg:grid-cols-[1.25fr_.75fr] lg:items-center lg:px-14 lg:py-16">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1 text-sm font-medium text-rose-50 backdrop-blur">
                            Bienvenido, {{ auth()->user()->name }}
                        </span>

                        <h1 class="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Tu próxima ruta empieza con una buena copa.
                        </h1>

                        <p class="mt-6 max-w-2xl text-lg leading-8 text-rose-100 sm:text-xl">
                            Descubrí bodegas, organizá tus paradas y armá una experiencia a tu medida. Todo lo que necesitás para recorrer el mundo del vino, en un solo lugar.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="#primeros-pasos"
                               class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-rose-900 shadow-sm transition hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-rose-900">
                                Conocé la experiencia
                                <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69L10.72 5.53a.75.75 0 0 1 1.06-1.06l5 5a.75.75 0 0 1 0 1.06l-5 5a.75.75 0 1 1-1.06-1.06l3.72-3.72H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="lg:justify-self-end">
                        <div class="w-full max-w-sm rounded-3xl border border-white/15 bg-white/10 p-5 text-white shadow-xl backdrop-blur-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200">Tu viaje</p>
                                    <p class="mt-1 text-lg font-semibold">Una ruta para recordar</p>
                                </div>
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6-4.35 6-11a6 6 0 1 0-12 0c0 6.65 6 11 6 11Z" />
                                        <circle cx="12" cy="10" r="2.2" />
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4">
                                <div class="flex gap-3 rounded-2xl bg-black/10 p-3">
                                    <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-amber-300"></span>
                                    <div>
                                        <p class="text-sm font-semibold">Elegí tu destino</p>
                                        <p class="mt-1 text-xs leading-5 text-rose-100">Encontrá bodegas y experiencias que encajen con vos.</p>
                                    </div>
                                </div>

                                <div class="flex gap-3 rounded-2xl bg-black/10 p-3">
                                    <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-amber-300"></span>
                                    <div>
                                        <p class="text-sm font-semibold">Armá el recorrido</p>
                                        <p class="mt-1 text-xs leading-5 text-rose-100">Ordená tus paradas y prepará cada momento del viaje.</p>
                                    </div>
                                </div>

                                <div class="flex gap-3 rounded-2xl bg-black/10 p-3">
                                    <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-amber-300"></span>
                                    <div>
                                        <p class="text-sm font-semibold">Viví la experiencia</p>
                                        <p class="mt-1 text-xs leading-5 text-rose-100">Guardá tus rutas favoritas y disfrutá el camino.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="primeros-pasos" class="mt-10">
                <div class="mb-6">
                    <p class="text-sm font-semibold text-rose-700">Todo empieza acá</p>
                    <h3 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Diseñá una salida a tu manera</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">
                        Mi Ruta del Vino está pensada para ayudarte a descubrir, planificar y disfrutar cada recorrido sin perderte lo mejor del camino.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-800">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 3h8l-1 5a4 4 0 0 1-6 0L8 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v7m-3 3h6" />
                            </svg>
                        </div>
                        <h4 class="mt-5 text-lg font-semibold text-gray-900">Descubrí</h4>
                        <p class="mt-2 text-sm leading-6 text-gray-600">Conocé bodegas, propuestas y lugares que vale la pena sumar a tu recorrido.</p>
                    </article>

                    <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-800">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m17 14 3 3-3 3" />
                            </svg>
                        </div>
                        <h4 class="mt-5 text-lg font-semibold text-gray-900">Organizá</h4>
                        <p class="mt-2 text-sm leading-6 text-gray-600">Prepará una ruta clara, ordenada y cómoda antes de salir a la ruta.</p>
                    </article>

                    <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.5s-7-4.25-7-10A4.25 4.25 0 0 1 12 7.27a4.25 4.25 0 0 1 7 3.23c0 5.75-7 10-7 10Z" />
                            </svg>
                        </div>
                        <h4 class="mt-5 text-lg font-semibold text-gray-900">Disfrutá</h4>
                        <p class="mt-2 text-sm leading-6 text-gray-600">Hacé del recorrido parte de la experiencia y guardá esas rutas que querés repetir.</p>
                    </article>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
