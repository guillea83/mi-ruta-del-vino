<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Recorré · Mi Ruta del Vino</title><link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin><link rel="preconnect" href="https://tile.openstreetmap.org" crossorigin><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">@vite(['resources/css/app.css', 'resources/js/app.js'])</head><body class="bg-gray-50 text-gray-900"><header class="bg-white border-b"><div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center"><a class="font-bold text-rose-950" href="{{ url('/') }}">🍷 Mi Ruta del Vino</a><a class="text-rose-900 font-semibold" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Mi cuenta' : 'Ingresar' }}</a></div></header>
<div class="max-w-7xl mx-auto px-4 py-8">
 <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
 <div><h1 class="text-3xl font-bold text-rose-950">Descubrí lugares y experiencias del vino</h1><p class="text-gray-600 mt-2">Vinotecas y otros establecimientos adheridos. Eventos publicados por sus organizadores.</p></div>
 @auth <a class="rounded-xl bg-rose-900 px-5 py-3 text-white font-semibold" href="{{ $esAdministrador ? route('recorre.admin') : route('recorre.panel') }}">{{ $esAdministrador ? 'Administrar Recorré' : 'Mi establecimiento' }}</a>
 @else <a class="rounded-xl bg-rose-900 px-5 py-3 text-white font-semibold" href="{{ route('login') }}">Adherir mi establecimiento</a> @endauth
 </div>
 <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-white">
  <div id="mapa-recorre" style="height:460px;min-height:320px;background:#eef0ed"></div>
  <p id="mapa-estado" class="px-4 py-2 text-sm text-gray-500" aria-live="polite">Cargando mapa…</p>
 </div>
 <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
 @forelse($establecimientos as $local)
 <article class="rounded-2xl bg-white p-5 shadow-sm border"><p class="text-xs uppercase text-rose-700 font-semibold">{{ str_replace('_', ' ', $local['tipo']) }}</p><h3 class="text-xl font-bold mt-1">{{ $local['nombre'] }}</h3><p class="text-sm text-gray-600 mt-2">{{ $local['direccion'] }}, {{ $local['ciudad'] }}, {{ $local['provincia'] }}</p><p class="mt-3">{{ $local['descripcion'] ?? '' }}</p><div class="flex flex-wrap gap-3 mt-3 text-rose-800 underline">@if(!empty($local['instagram']))<a target="_blank" rel="noopener noreferrer" href="{{ $local['instagram'] }}">Instagram</a>@endif @if(!empty($local['sitio_web']))<a target="_blank" rel="noopener noreferrer" href="{{ $local['sitio_web'] }}">Sitio web</a>@endif</div></article>
 @empty <p class="text-gray-600">Todavía no hay establecimientos publicados. ¡Pronto vas a encontrar lugares para recorrer!</p> @endforelse
 </div>
 <h2 class="mt-10 text-2xl font-bold text-rose-950">📅 Próximos eventos</h2>
 <div class="grid gap-4 md:grid-cols-2 mt-4">@forelse($eventos as $evento)
 <article class="p-5 bg-white rounded-2xl border shadow-sm"><p class="text-sm font-semibold text-rose-700">{{ $evento['fecha'] }} {{ $evento['hora'] ?? '' }}</p><h3 class="text-lg font-bold mt-1">{{ $evento['titulo'] }}</h3><p class="mt-2">{{ $evento['descripcion'] ?? '' }}</p>@if(!empty($evento['enlace']))<a class="text-rose-800 underline mt-3 inline-block" href="{{ $evento['enlace'] }}" target="_blank" rel="noopener noreferrer">Consultar al organizador ↗</a>@endif</article>
 @empty <p class="text-gray-600">No hay eventos publicados por ahora.</p> @endforelse</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
 if (typeof L === 'undefined') return;
 // Una sola capa de teselas estándar (256 × 256 píxeles). La combinación
 // anterior de capas de 512/256 provocaba parches y espacios en blanco.
 const mapa = L.map('mapa-recorre', {
  zoomControl: true,
  fadeAnimation: false,
  preferCanvas: true
 }).setView([-37.5, -63.5], 4);

 const estado = document.getElementById('mapa-estado');
 let fallas = 0;
 const capa = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  minZoom: 3,
  maxZoom: 19,
  tileSize: 256,
  updateWhenIdle: true,
  keepBuffer: 2
 });
 capa.on('tileerror', function () {
  fallas++;
  if (estado) estado.textContent = 'No se pudieron cargar algunas zonas del mapa. Probá actualizar la página o revisar la conexión.';
 });
 capa.on('load', function () {
  if (estado && fallas === 0) estado.hidden = true;
 });
 capa.addTo(mapa);
 const lugares = @json($establecimientos);
 const puntos=[];
 lugares.forEach(function(l){
  const lat=Number(l.latitud), lng=Number(l.longitud);
  if(!Number.isFinite(lat)||!Number.isFinite(lng)) return;
  const nodo=document.createElement('div');
  const titulo=document.createElement('strong'); titulo.textContent=l.nombre; nodo.appendChild(titulo);
  const dir=document.createElement('p'); dir.textContent=l.direccion+', '+l.ciudad; nodo.appendChild(dir);
  puntos.push(L.marker([lat,lng]).addTo(mapa).bindPopup(nodo));
 });
 if(puntos.length) mapa.fitBounds(L.featureGroup(puntos).getBounds().pad(.15),{maxZoom:13});
});
</script>
</body></html>
