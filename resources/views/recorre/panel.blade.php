<x-app-layout>
<x-slot name="header"><h2 class="text-xl font-bold text-rose-950">Recorré · Panel de establecimientos</h2></x-slot>
<div class="max-w-6xl mx-auto px-4 py-8 space-y-8">
<a class="text-rose-800 underline" href="{{ route('recorre.index') }}">← Volver al mapa</a>
@if(session('status'))<div class="p-4 bg-green-50 text-green-900 rounded-xl">{{ session('status') }}</div>@endif
@if($errors->any())<div class="p-4 bg-red-50 text-red-900 rounded-xl"><ul class="list-disc ml-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<section class="bg-white p-6 rounded-2xl shadow-sm"><h3 class="text-xl font-bold mb-2">Adherir un establecimiento</h3><p class="text-gray-600 mb-5">Completá los datos y enviaremos la solicitud a revisión. Para la ubicación podés copiar las coordenadas desde Google Maps.</p>
<form method="POST" action="{{ route('recorre.registrar') }}" class="grid md:grid-cols-2 gap-4">@csrf
@foreach(['nombre'=>'Nombre','direccion'=>'Dirección','ciudad'=>'Ciudad','provincia'=>'Provincia','latitud'=>'Latitud','longitud'=>'Longitud','instagram'=>'Instagram (URL)','sitio_web'=>'Sitio web (URL)','whatsapp'=>'WhatsApp'] as $campo=>$etiqueta)
<label class="block text-sm font-semibold">{{ $etiqueta }}<input name="{{ $campo }}" value="{{ old($campo) }}" @if(in_array($campo,['nombre','direccion','ciudad','provincia','latitud','longitud'])) required @endif class="mt-1 w-full rounded-lg border-gray-300"></label>
@endforeach
<label class="block text-sm font-semibold">Tipo<select name="tipo" class="mt-1 w-full rounded-lg border-gray-300"><option value="vinoteca">Vinoteca</option><option value="bodega">Bodega</option><option value="vinedo">Viñedo</option><option value="wine_bar">Wine bar</option><option value="otro">Otro</option></select></label>
<label class="block md:col-span-2 text-sm font-semibold">Descripción<textarea name="descripcion" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('descripcion') }}</textarea></label>
<div class="md:col-span-2"><button class="bg-rose-900 text-white rounded-xl px-5 py-3 font-bold">Enviar solicitud</button></div></form></section>
<section class="bg-white p-6 rounded-2xl shadow-sm"><h3 class="text-xl font-bold mb-4">Mis establecimientos</h3>
@forelse($establecimientos as $local)<div class="border-b py-3"><strong>{{ $local['nombre'] }}</strong> <span class="text-sm text-gray-500">· {{ $local['estado'] }} · Plan: {{ $local['plan'] }}</span></div>@empty<p>Aún no hay establecimientos registrados.</p>@endforelse
</section>
@if($establecimientos->where('estado','aprobado')->where('user_id',auth()->id())->isNotEmpty())
<section class="bg-white p-6 rounded-2xl shadow-sm"><h3 class="text-xl font-bold mb-4">Publicar evento</h3><form method="POST" action="{{ route('recorre.eventos') }}" class="grid md:grid-cols-2 gap-4">@csrf
<label>Establecimiento<select required name="establecimiento_id" class="w-full rounded-lg border-gray-300">@foreach($establecimientos->where('estado','aprobado')->where('user_id',auth()->id()) as $local)<option value="{{ $local['id'] }}">{{ $local['nombre'] }}</option>@endforeach</select></label>
<label>Título<input required name="titulo" class="w-full rounded-lg border-gray-300"></label>
<label>Fecha<input required type="date" name="fecha" min="{{ now()->toDateString() }}" class="w-full rounded-lg border-gray-300"></label>
<label>Hora<input type="time" name="hora" class="w-full rounded-lg border-gray-300"></label>
<label>Enlace de consulta<input name="enlace" type="url" class="w-full rounded-lg border-gray-300"></label>
<label class="md:col-span-2">Descripción<textarea name="descripcion" class="w-full rounded-lg border-gray-300"></textarea></label>
<div class="md:col-span-2"><button class="bg-rose-900 text-white rounded-xl px-5 py-3 font-bold">Enviar evento</button></div></form></section>
@endif
<section class="bg-white p-6 rounded-2xl shadow-sm"><h3 class="text-xl font-bold mb-4">Eventos registrados</h3>@forelse($eventos as $evento)<p class="border-b py-3">{{ $evento['fecha'] }} · {{ $evento['titulo'] }} <span class="text-gray-500">({{ $evento['estado'] }})</span></p>@empty<p>Sin eventos todavía.</p>@endforelse</section>
@if($admin)<section class="bg-amber-50 rounded-2xl p-6"><h3 class="text-xl font-bold mb-4">Moderación administrativa</h3>
@foreach(['establecimientos'=>$establecimientos,'eventos'=>$eventos] as $tipo=>$items)
<h4 class="text-lg font-semibold capitalize my-3">{{ $tipo }}</h4>
@foreach($items as $item)<div class="flex flex-wrap items-center justify-between gap-3 border-b py-3"><span>{{ $item['nombre'] ?? $item['titulo'] }} · {{ $item['estado'] }}</span>
<form method="POST" action="{{ route('recorre.moderar',[$tipo,$item['id']]) }}" class="flex gap-2">@csrf
<select name="estado" class="rounded-lg border-gray-300"><option value="aprobado">Aprobar</option><option value="rechazado">Rechazar</option><option value="pendiente">Pendiente</option></select><button class="rounded-lg bg-rose-900 px-4 py-2 text-white">Guardar</button>
</form></div>@endforeach
@endforeach</section>@endif
</div></x-app-layout>