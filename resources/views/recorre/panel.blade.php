<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-rose-950">🍇 Recorré · Mi establecimiento</h2>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-8 px-4 py-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-rose-950">Panel de establecimientos</h1>
                <p class="mt-1 text-sm text-gray-600">Solicitá tu incorporación y, cuando estés aprobado, publicá eventos.</p>
            </div>
            <a class="font-semibold text-rose-800 underline" href="{{ route('recorre.index') }}">← Volver al mapa</a>
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

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-xl font-bold text-gray-900">Mis establecimientos</h3>
            <p class="mt-1 text-sm text-gray-600">Solo el administrador puede aprobar tu solicitud. Hasta entonces, no aparece en el mapa.</p>
            <div class="mt-4 space-y-3">
                @forelse($establecimientos as $local)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border p-4">
                        <div>
                            <strong>{{ $local['nombre'] }}</strong>
                            <p class="text-sm text-gray-600">{{ $local['ciudad'] }}, {{ $local['provincia'] }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $local['estado'] === 'aprobado' ? 'bg-green-100 text-green-900' : ($local['estado'] === 'pendiente' ? 'bg-amber-100 text-amber-900' : 'bg-gray-100 text-gray-700') }}">
                            {{ ucfirst($local['estado']) }}
                        </span>
                    </div>
                @empty
                    <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600">Todavía no registraste ningún establecimiento.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-xl font-bold text-gray-900">Solicitar incorporación</h3>
            <p class="mb-5 mt-1 text-sm text-gray-600">Tu alta quedará pendiente hasta que la apruebe el administrador. Escribí la dirección habitual y el sistema la ubicará en el mapa.</p>
            <form method="POST" action="{{ route('recorre.registrar') }}" class="grid gap-4 md:grid-cols-2">
                @csrf
                @include('recorre.partials.form-establecimiento')
                <div class="md:col-span-2">
                    <button class="rounded-xl bg-rose-900 px-5 py-3 font-bold text-white hover:bg-rose-950">Enviar solicitud de alta</button>
                </div>
            </form>
        </section>

        @if($establecimientos->where('estado', 'aprobado')->isNotEmpty())
            <section class="rounded-2xl bg-white p-6 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900">Publicar un evento</h3>
                <p class="mb-5 mt-1 text-sm text-gray-600">Los eventos también necesitan aprobación antes de aparecer en la agenda pública.</p>
                <form method="POST" action="{{ route('recorre.eventos') }}" class="grid gap-4 md:grid-cols-2">
                    @csrf
                    <label class="text-sm font-semibold">Establecimiento
                        <select required name="establecimiento_id" class="mt-1 w-full rounded-lg border-gray-300">
                            @foreach($establecimientos->where('estado','aprobado') as $local)
                                <option value="{{ $local['id'] }}">{{ $local['nombre'] }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-semibold">Título
                        <input required name="titulo" value="{{ old('titulo') }}" class="mt-1 w-full rounded-lg border-gray-300">
                    </label>
                    <label class="text-sm font-semibold">Fecha
                        <input required type="date" name="fecha" min="{{ now()->toDateString() }}" value="{{ old('fecha') }}" class="mt-1 w-full rounded-lg border-gray-300">
                    </label>
                    <label class="text-sm font-semibold">Hora
                        <input type="time" name="hora" value="{{ old('hora') }}" class="mt-1 w-full rounded-lg border-gray-300">
                    </label>
                    <label class="text-sm font-semibold">Enlace externo del evento
                        <input type="url" name="enlace" value="{{ old('enlace') }}" class="mt-1 w-full rounded-lg border-gray-300">
                    </label>
                    <label class="text-sm font-semibold md:col-span-2">Descripción
                        <textarea name="descripcion" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('descripcion') }}</textarea>
                    </label>
                    <div class="md:col-span-2">
                        <button class="rounded-xl bg-rose-900 px-5 py-3 font-bold text-white hover:bg-rose-950">Enviar evento para aprobación</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-xl font-bold text-gray-900">Mis eventos</h3>
            <div class="mt-4 space-y-2">
                @forelse($eventos as $evento)
                    <div class="flex flex-wrap justify-between gap-2 border-b py-3">
                        <span>{{ $evento['fecha'] }} · {{ $evento['titulo'] }}</span>
                        <span class="text-sm font-semibold text-gray-600">{{ ucfirst($evento['estado']) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">No cargaste eventos todavía.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
