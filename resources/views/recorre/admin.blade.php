<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-rose-950">🍇 Recorré · Administración</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-rose-950">Administrar establecimientos y eventos</h1>
                <p class="mt-1 text-sm text-gray-600">Solo tu cuenta administradora puede aprobar incorporaciones y publicaciones.</p>
            </div>
            <a href="{{ route('recorre.index') }}" class="font-semibold text-rose-800 underline">← Ir al mapa</a>
        </div>

        @if(session('status'))
            <div class="rounded-xl bg-green-50 p-4 text-green-900">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-xl bg-red-50 p-4 text-red-900">
                <ul class="ml-5 list-disc">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Solicitudes de alta pendientes</p>
                <p class="mt-2 text-3xl font-bold text-rose-950">{{ $establecimientos->where('estado','pendiente')->count() }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Establecimientos aprobados</p>
                <p class="mt-2 text-3xl font-bold text-rose-950">{{ $establecimientos->where('estado','aprobado')->count() }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Eventos pendientes</p>
                <p class="mt-2 text-3xl font-bold text-rose-950">{{ $eventos->where('estado','pendiente')->count() }}</p>
            </div>
        </div>

        <section class="rounded-2xl bg-white p-5 shadow-sm sm:p-7">
            <h3 class="text-xl font-bold text-gray-900">Solicitudes de establecimientos</h3>
            <p class="mb-5 mt-1 text-sm text-gray-600">Las altas realizadas por otras cuentas requieren tu aprobación antes de aparecer en Recorré.</p>
            <div class="space-y-4">
                @forelse($establecimientos->where('estado','pendiente') as $local)
                    <article class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="space-y-1">
                                <h4 class="font-bold text-gray-900">{{ $local['nombre'] }}</h4>
                                <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_',' ', $local['tipo'])) }} · {{ $local['direccion'] }}, {{ $local['ciudad'] }}, {{ $local['provincia'] }}</p>
                                @if(!empty($local['descripcion']))<p class="text-sm">{{ $local['descripcion'] }}</p>@endif
                                <p class="text-sm">Solicitante: <strong>{{ $local['correo_contacto'] ?? 'Usuario #'.($local['user_id'] ?? '—') }}</strong></p>
                                @if(!empty($local['sitio_web']))<p class="text-sm"><a href="{{ $local['sitio_web'] }}" rel="noopener noreferrer" target="_blank" class="text-rose-800 underline">Ver sitio web ↗</a></p>@endif
                            </div>
                            @if(($local['user_id'] ?? null) !== null && (string) $local['user_id'] === (string) auth()->id())
                                <p class="rounded-lg bg-rose-100 p-2 text-xs text-rose-900">Alta propia anterior: no puede autoaprobarse.</p>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('recorre.moderar', ['establecimientos', $local['id']]) }}">
                                        @csrf<input type="hidden" name="estado" value="aprobado">
                                        <button class="rounded-lg bg-green-800 px-4 py-2 text-sm font-bold text-white">Aprobar</button>
                                    </form>
                                    <form method="POST" action="{{ route('recorre.moderar', ['establecimientos', $local['id']]) }}">
                                        @csrf<input type="hidden" name="estado" value="rechazado">
                                        <button class="rounded-lg bg-rose-900 px-4 py-2 text-sm font-bold text-white">Rechazar</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600">No hay solicitudes pendientes.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm sm:p-7">
            <h3 class="text-xl font-bold text-gray-900">Agregar un establecimiento aprobado</h3>
            <p class="mb-5 mt-1 text-sm text-gray-600">Alta directa de administración. Aparecerá en el mapa sin esperar una solicitud de otro usuario.</p>
            <form method="POST" action="{{ route('recorre.admin.crear') }}" class="grid gap-4 md:grid-cols-2">
                @csrf
                @include('recorre.partials.form-establecimiento')
                <div class="md:col-span-2">
                    <button class="rounded-xl bg-rose-900 px-5 py-3 font-bold text-white hover:bg-rose-950">Agregar y publicar en el mapa</button>
                </div>
            </form>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm sm:p-7">
            <h3 class="text-xl font-bold text-gray-900">Eventos que esperan aprobación</h3>
            <div class="mt-4 space-y-4">
                @forelse($eventos->where('estado','pendiente') as $evento)
                    @php
                        $localEvento = $establecimientos->firstWhere('id', $evento['establecimiento_id'] ?? null);
                        $propietarioEvento = $localEvento['user_id'] ?? null;
                    @endphp
                    <article class="flex flex-wrap items-start justify-between gap-4 rounded-xl border p-4">
                        <div>
                            <h4 class="font-bold">{{ $evento['titulo'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $evento['fecha'] }} {{ $evento['hora'] ?? '' }} · {{ $localEvento['nombre'] ?? 'Establecimiento no encontrado' }}</p>
                            @if(!empty($evento['descripcion']))<p class="mt-2 text-sm">{{ $evento['descripcion'] }}</p>@endif
                            @if(!empty($evento['enlace']))<a class="text-sm text-rose-800 underline" href="{{ $evento['enlace'] }}" rel="noopener noreferrer" target="_blank">Ver enlace del evento ↗</a>@endif
                        </div>
                        @if($propietarioEvento !== null && (string) $propietarioEvento === (string) auth()->id())
                            <p class="text-xs text-rose-800">No se permite autoaprobar.</p>
                        @else
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('recorre.moderar', ['eventos', $evento['id']]) }}">
                                    @csrf<input type="hidden" name="estado" value="aprobado"><button class="rounded-lg bg-green-800 px-4 py-2 text-sm font-bold text-white">Aprobar</button>
                                </form>
                                <form method="POST" action="{{ route('recorre.moderar', ['eventos', $evento['id']]) }}">
                                    @csrf<input type="hidden" name="estado" value="rechazado"><button class="rounded-lg bg-rose-900 px-4 py-2 text-sm font-bold text-white">Rechazar</button>
                                </form>
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600">No hay eventos pendientes.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm sm:p-7">
            <h3 class="text-xl font-bold text-gray-900">Todos los establecimientos</h3>
            <div class="mt-4 space-y-3">
                @forelse($establecimientos as $local)
                    <div class="flex flex-wrap justify-between gap-3 border-b py-3">
                        <div><strong>{{ $local['nombre'] }}</strong><p class="text-sm text-gray-600">{{ $local['ciudad'] }} · {{ $local['correo_contacto'] ?? 'Alta administrativa' }}</p></div>
                        <span class="text-sm font-semibold">{{ ucfirst($local['estado']) }} · {{ ucfirst($local['plan'] ?? 'gratis') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Todavía no hay establecimientos.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
