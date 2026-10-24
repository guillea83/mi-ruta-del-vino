<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Recorré · Mi Ruta del Vino</title><link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin><link rel="preconnect" href="https://tiles.openfreemap.org" crossorigin><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/maplibre-gl@6.15.0/dist/maplibre-gl.css">@vite(['resources/css/app.css', 'resources/js/app.js'])</head><body class="bg-gray-50 text-gray-900"><header class="bg-white border-b"><div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center"><a class="font-bold text-rose-950" href="{{ url('/') }}">🍷 Mi Ruta del Vino</a><a class="text-rose-900 font-semibold" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Mi cuenta' : 'Ingresar' }}</a></div></header>
<div class="max-w-7xl mx-auto px-4 py-8">
 <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
 <div><h1 class="text-3xl font-bold text-rose-950">Descubrí lugares y experiencias del vino</h1><p class="text-gray-600 mt-2">Vinotecas y otros establecimientos adheridos. Eventos publicados por sus organizadores.</p></div>
 @auth <a class="rounded-xl bg-rose-900 px-5 py-3 text-white font-semibold" href="{{ $esAdministrador ? route('recorre.admin') : route('recorre.panel') }}">{{ $esAdministrador ? 'Administrar Recorré' : 'Mi establecimiento' }}</a>
 @else <a class="rounded-xl bg-rose-900 px-5 py-3 text-white font-semibold" href="{{ route('login') }}">Adherir mi establecimiento</a> @endauth
 </div>
 <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-white">
  <div id="mapa-recorre" style="height:460px;min-height:320px;background:#f3f4f6"></div>
  <p id="mapa-estado" class="px-4 py-2 text-sm text-gray-500" aria-live="polite">Cargando mapa…</p>
 </div>
 <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
 @forelse($establecimientos as $local)
 <article class="rounded-2xl bg-white p-5 shadow-sm border"><p class="text-xs uppercase text-rose-700 font-semibold">{{ str_replace('_', ' ', $local['tipo']) }}</p><h3 class="text-xl font-bold mt-1">{{ $local['nombre'] }}</h3><p class="text-sm text-gray-600 mt-2">{{ $local['direccion'] }}, {{ $local['ciudad'] }}, {{ $local['provincia'] }}</p><p class="mt-3">{{ $local['descripcion'] ?? '' }}</p><a class="mt-4 inline-block rounded-lg bg-rose-900 px-4 py-2 text-sm font-bold text-white" href="{{ route('recorre.establecimientos.show', $local['id']) }}">INGRESAR</a><div class="flex flex-wrap gap-3 mt-3 text-rose-800 underline">@if(!empty($local['instagram']))<a target="_blank" rel="noopener noreferrer" href="{{ $local['instagram'] }}">Instagram</a>@endif @if(!empty($local['sitio_web']))<a target="_blank" rel="noopener noreferrer" href="{{ $local['sitio_web'] }}">Sitio web</a>@endif</div></article>
 @empty <p class="text-gray-600">Todavía no hay establecimientos publicados. ¡Pronto vas a encontrar lugares para recorrer!</p> @endforelse
 </div>
 <h2 class="mt-10 text-2xl font-bold text-rose-950">📅 Próximos eventos</h2>
 <div class="grid gap-4 md:grid-cols-2 mt-4">@forelse($eventos as $evento)
 <article class="p-5 bg-white rounded-2xl border shadow-sm"><p class="text-sm font-semibold text-rose-700">{{ $evento['fecha'] }} {{ $evento['hora'] ?? '' }}</p><h3 class="text-lg font-bold mt-1">{{ $evento['titulo'] }}</h3><p class="mt-2">{{ $evento['descripcion'] ?? '' }}</p>@if(!empty($evento['enlace']))<a class="text-rose-800 underline mt-3 inline-block" href="{{ $evento['enlace'] }}" target="_blank" rel="noopener noreferrer">Consultar al organizador ↗</a>@endif</article>
 @empty <p class="text-gray-600">No hay eventos publicados por ahora.</p> @endforelse</div>
</div>
<script type="module">
import * as maplibregl from 'https://unpkg.com/maplibre-gl@6.15.0/dist/maplibre-gl.mjs';

const estado = document.getElementById('mapa-estado');
const lugares = @json($establecimientos);
const urlFicha = @json(route('recorre.establecimientos.show', ['id' => '__ID__']));
const mapa = new maplibregl.Map({
 container: 'mapa-recorre',
 style: 'https://tiles.openfreemap.org/styles/bright',
 center: [-63.5, -37.5],
 zoom: 4,
 attributionControl: true,
});

mapa.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'top-left');
mapa.touchZoomRotate.disableRotation();
mapa.dragRotate.disable();
mapa.touchPitch.disable();

mapa.on('error', function () {
 if (estado) {
  estado.hidden = false;
  estado.textContent = 'No se pudo cargar el mapa. Revisá tu conexión e intentá actualizar la página.';
 }
});

mapa.on('load', function () {
 if (estado) estado.hidden = true;
 const bounds = new maplibregl.LngLatBounds();
 let cantidad = 0;

 lugares.forEach(function (lugar) {
  const lat = Number(lugar.latitud);
  const lng = Number(lugar.longitud);
  if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

  const icono = document.createElement('div');
  icono.className = 'recorre-copa-marker';
  icono.setAttribute('aria-label', 'Ubicación de ' + lugar.nombre);
  icono.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 42" width="38" height="42" aria-hidden="true"><path d="M8 5h20l-2.1 12.2a8 8 0 0 1-6.4 6.5V34h7v3H9.5v-3h7v-10.3a8 8 0 0 1-6.4-6.5L8 5Z" fill="#9f1239" stroke="#fff" stroke-width="2" stroke-linejoin="round"/><path d="M11 9h14l-1.1 7.5H12.1L11 9Z" fill="#fce7f3"/></svg>';

  const contenido = document.createElement('div');
  const titulo = document.createElement('strong');
  titulo.className = 'block text-base font-bold text-gray-900';
  titulo.textContent = lugar.nombre;
  contenido.appendChild(titulo);
  const direccion = document.createElement('p');
  direccion.className = 'mt-1 text-sm text-gray-600';
  direccion.textContent = [lugar.direccion, lugar.ciudad].filter(Boolean).join(', ');
  contenido.appendChild(direccion);
  const ingresar = document.createElement('a');
  ingresar.href = urlFicha.replace('__ID__', encodeURIComponent(String(lugar.id)));
  ingresar.textContent = 'INGRESAR';
  ingresar.className = 'mt-2 inline-block rounded-lg bg-rose-900 px-3 py-2 text-sm font-bold text-white no-underline';
  contenido.appendChild(ingresar);

  const popup = new maplibregl.Popup({ offset: 24, closeButton: true, closeOnClick: true })
   .setDOMContent(contenido);
  new maplibregl.Marker({ element: icono, anchor: 'bottom' })
   .setLngLat([lng, lat])
   .setPopup(popup)
   .addTo(mapa);
  bounds.extend([lng, lat]);
  cantidad++;
 });

 if (cantidad > 0) mapa.fitBounds(bounds, { padding: 48, maxZoom: 13, duration: 0 });
});
</script>
<style>
.recorre-copa-marker { display:grid; place-items:center; width:42px; height:46px; cursor:pointer; filter:drop-shadow(0 2px 4px #0009); transition:transform .15s ease; }
.recorre-copa-marker:hover { transform:translateY(-3px) scale(1.08); }
.maplibregl-popup-content { border-radius:14px; padding:14px; box-shadow:0 8px 28px #0005; }
.maplibregl-popup-close-button { padding:6px 9px; color:#4b5563; font-size:18px; }
.maplibregl-ctrl-group { overflow:hidden; border-radius:12px !important; }
</style>
</body></html>
