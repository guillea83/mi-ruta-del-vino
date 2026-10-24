@foreach([
    'nombre' => 'Nombre del establecimiento',
    'direccion' => 'Dirección',
    'ciudad' => 'Ciudad',
    'provincia' => 'Provincia',
    'latitud' => 'Latitud (ej.: -31.6333)',
    'longitud' => 'Longitud (ej.: -60.7000)',
    'instagram' => 'Instagram (URL)',
    'sitio_web' => 'Sitio web (URL)',
    'whatsapp' => 'WhatsApp'
] as $campo => $etiqueta)
    <label class="block text-sm font-semibold text-gray-800">
        {{ $etiqueta }}
        <input name="{{ $campo }}" value="{{ old($campo) }}"
               @if(in_array($campo, ['nombre', 'direccion', 'ciudad', 'provincia', 'latitud', 'longitud'])) required @endif
               @if(in_array($campo, ['instagram', 'sitio_web'])) type="url" @else type="text" @endif
               class="mt-1 w-full rounded-lg border-gray-300" />
    </label>
@endforeach
<label class="block text-sm font-semibold text-gray-800">
    Tipo de establecimiento
    <select name="tipo" required class="mt-1 w-full rounded-lg border-gray-300">
        @foreach(['vinoteca' => 'Vinoteca', 'bodega' => 'Bodega', 'vinedo' => 'Viñedo', 'wine_bar' => 'Wine bar', 'otro' => 'Otro'] as $valor => $etiqueta)
            <option value="{{ $valor }}" @selected(old('tipo', 'vinoteca') === $valor)>{{ $etiqueta }}</option>
        @endforeach
    </select>
</label>
@if(!empty($desdeAdministracion))
    <label class="block text-sm font-semibold text-gray-800">Email de contacto (opcional)
        <input name="correo_contacto" type="email" value="{{ old('correo_contacto') }}" class="mt-1 w-full rounded-lg border-gray-300">
    </label>
@endif
<label class="block text-sm font-semibold text-gray-800 md:col-span-2">
    Descripción
    <textarea name="descripcion" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('descripcion') }}</textarea>
</label>
