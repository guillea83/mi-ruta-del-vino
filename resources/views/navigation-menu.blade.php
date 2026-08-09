<nav x-data="{ open: false }" class="border-b border-rose-100 bg-white/95 shadow-sm backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-7">
                <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-rose-950 text-xl text-white shadow-sm">🍷</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold leading-tight text-gray-900">Mi Ruta del Vino</p>
                        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.16em] text-rose-700">Tu memoria del vino</p>
                    </div>
                </a>

                <div class="hidden items-center gap-1 md:flex">
                    <a href="{{ route('dashboard') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-rose-50 text-rose-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Inicio
                    </a>
                    <a href="{{ route('mi-bodega.index') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('mi-bodega.index', 'mi-bodega.show') ? 'bg-rose-50 text-rose-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Mi Bodega Personal
                    </a>
                    <a href="{{ route('mi-bodega.create') }}"
                       class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('mi-bodega.create') ? 'bg-rose-50 text-rose-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Guardar un vino
                    </a>
                </div>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <a href="{{ route('mi-bodega.create') }}"
                   class="inline-flex items-center rounded-xl bg-rose-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-rose-950">
                    <span class="mr-2">＋</span> Vino
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 font-bold text-rose-900">
                                    {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            @endif
                            <span class="max-w-32 truncate">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3">
                            <p class="truncate text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <x-dropdown-link href="{{ route('profile.show') }}">
                            Mi perfil
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button type="button"
                    @click="open = !open"
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-gray-600 transition hover:bg-gray-100 md:hidden"
                    :aria-expanded="open.toString()"
                    aria-label="Abrir menú">
                <svg x-show="!open" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" x-cloak class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak class="border-t border-gray-100 bg-white md:hidden">
        <div class="space-y-1 px-4 py-4">
            <a href="{{ route('dashboard') }}"
               class="block rounded-xl px-4 py-3 text-base font-semibold {{ request()->routeIs('dashboard') ? 'bg-rose-50 text-rose-900' : 'text-gray-700' }}">
                🏠 Inicio
            </a>
            <a href="{{ route('mi-bodega.index') }}"
               class="block rounded-xl px-4 py-3 text-base font-semibold {{ request()->routeIs('mi-bodega.index', 'mi-bodega.show') ? 'bg-rose-50 text-rose-900' : 'text-gray-700' }}">
                🍷 Mi Bodega Personal
            </a>
            <a href="{{ route('mi-bodega.create') }}"
               class="block rounded-xl bg-rose-900 px-4 py-3 text-base font-bold text-white">
                ＋ Guardar un vino
            </a>
        </div>

        <div class="border-t border-gray-100 px-4 py-4">
            <div class="mb-3 px-4">
                <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                <p class="truncate text-sm text-gray-500">{{ Auth::user()->email }}</p>
            </div>
            <a href="{{ route('profile.show') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-700">Mi perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-gray-700">Cerrar sesión</button>
            </form>
        </div>
    </div>
</nav>
