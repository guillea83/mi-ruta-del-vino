<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $establecimiento['nombre'] }} · Mi Ruta del Vino</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <header class="border-b bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
            <a class="font-bold text-rose-950" href="{{ route('recorre.index') }}">🍷 Mi Ruta del Vino</a>
            <a class="font-semibold text-rose-900" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Mi cuenta' : 'Ingresar' }}</a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl space-y-6 px-4 py-8">
        <a href="{{ route('recorre.index') }}" class="font-semibold text-rose-800 underline">← Volver al recorrido</a>

        <section class="rounded-2xl border bg-white p-6 shadow-sm sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-rose-700">{{ str_replace('_', ' ', $establecimiento['tipo'] ?? 'establecimiento') }}</p>
            <h1 class="mt-2 text-3xl font-bold text-rose-950">{{ $establecimiento['nombre'] }}</h1>
            <p class="mt-4 text-gray-700">{{ $establecimiento['direccion'] }}, {{ $establecimiento['ciudad'] }}, {{ $establecimiento['provincia'] }}</p>
            <a class="mt-3 inline-block text-sm font-semibold text-rose-800 underline" target="_blank" rel="noopener noreferrer"
               href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($establecimiento['direccion'] ?? '').', '.($establecimiento['ciudad'] ?? '').', '.($establecimiento['provincia'] ?? '')) }}">
                Cómo llegar ↗
            </a>

            @if(!empty($establecimiento['descripcion']))
                <div class="mt-6">
                    <h2 class="text-lg font-bold">Sobre el establecimiento</h2>
                    <p class="mt-2 whitespace-pre-line text-gray-700">{{ $establecimiento['descripcion'] }}</p>
                </div>
            @endif

            <div class="mt-6">
                <h2 class="text-lg font-bold">Servicios</h2>
                @if(!empty($establecimiento['servicios']))
                    <p class="mt-2 whitespace-pre-line text-gray-700">{{ $establecimiento['servicios'] }}</p>
                @else
                    <p class="mt-2 text-gray-500">El establecimiento todavía no informó sus servicios.</p>
                @endif
            </div>

            @if(!empty($establecimiento['sitio_web']) || !empty($establecimiento['instagram']) || !empty($establecimiento['whatsapp']))
                <div class="mt-6 border-t pt-5">
                    <h2 class="text-lg font-bold">Contacto</h2>
                    <div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-rose-800 underline">
                        @if(!empty($establecimiento['sitio_web']))<a href="{{ $establecimiento['sitio_web'] }}" target="_blank" rel="noopener noreferrer">Sitio web ↗</a>@endif
                        @if(!empty($establecimiento['instagram']))<a href="{{ $establecimiento['instagram'] }}" target="_blank" rel="noopener noreferrer">Instagram ↗</a>@endif
                        @if(!empty($establecimiento['whatsapp']))<span class="text-gray-700 no-underline">WhatsApp: {{ $establecimiento['whatsapp'] }}</span>@endif
                    </div>
                </div>
            @endif
        </section>

        <section class="rounded-2xl border bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-bold text-rose-950">Próximos eventos</h2>
            <div class="mt-4 space-y-4">
                @forelse($eventos as $evento)
                    <article class="rounded-xl bg-rose-50 p-4">
                        <p class="text-sm font-semibold text-rose-800">{{ $evento['fecha'] }} {{ $evento['hora'] ?? '' }}</p>
                        <h3 class="mt-1 text-lg font-bold">{{ $evento['titulo'] }}</h3>
                        @if(!empty($evento['descripcion']))<p class="mt-2 whitespace-pre-line text-gray-700">{{ $evento['descripcion'] }}</p>@endif
                        @if(!empty($evento['enlace']))<a class="mt-3 inline-block font-semibold text-rose-800 underline" href="{{ $evento['enlace'] }}" target="_blank" rel="noopener noreferrer">Más información ↗</a>@endif
                    </article>
                @empty
                    <p class="text-gray-600">Por ahora no hay próximos eventos publicados.</p>
                @endforelse
            </div>
        </section>
    </main>
</body>
</html>
