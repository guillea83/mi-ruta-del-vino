<div class="grid gap-4 md:grid-cols-2 md:col-span-2" data-geocodificador data-geocoding-url="{{ config('recorre.geocoding_url') }}">
    <label class="block text-sm font-semibold text-gray-800">
        Nombre del establecimiento
        <input name="nombre" value="{{ old('nombre') }}" required maxlength="180" class="mt-1 w-full rounded-lg border-gray-300" />
    </label>
    <label class="block text-sm font-semibold text-gray-800">
        Dirección
        <input name="direccion" value="{{ old('direccion') }}" required maxlength="250" autocomplete="street-address" placeholder="Ej.: San Martín 1234" class="mt-1 w-full rounded-lg border-gray-300" />
    </label>
    <label class="block text-sm font-semibold text-gray-800">
        Ciudad
        <input name="ciudad" value="{{ old('ciudad') }}" required maxlength="120" autocomplete="address-level2" class="mt-1 w-full rounded-lg border-gray-300" />
    </label>
    <label class="block text-sm font-semibold text-gray-800">
        Provincia
        <input name="provincia" value="{{ old('provincia') }}" required maxlength="120" autocomplete="address-level1" class="mt-1 w-full rounded-lg border-gray-300" />
    </label>

    <input type="hidden" name="latitud" value="" data-latitud />
    <input type="hidden" name="longitud" value="" data-longitud />

    <div class="md:col-span-2 rounded-xl bg-rose-50 p-4">
        <p class="text-sm text-gray-700">Escribí la dirección habitual y buscala para ubicar el establecimiento en el mapa. No necesitás ingresar coordenadas.</p>
        <button type="button" data-buscar-ubicacion class="mt-3 rounded-lg border border-rose-900 px-4 py-2 text-sm font-bold text-rose-900 hover:bg-white">
            Buscar dirección en el mapa
        </button>
        <p data-estado-ubicacion class="mt-2 text-sm text-gray-700" role="status" aria-live="polite"></p>
        <label data-confirmar-ubicacion class="mt-3 hidden items-start gap-2 text-sm font-medium text-gray-800">
            <input type="checkbox" data-ubicacion-confirmada class="mt-1 rounded border-gray-300 text-rose-900 focus:ring-rose-800" />
            <span>Confirmo que el resultado corresponde a este establecimiento.</span>
        </label>
        <p class="mt-2 text-xs text-gray-600">La búsqueda manual usa Nominatim de OpenStreetMap; admite como máximo una consulta por segundo y no ofrece autocompletado. Puede requerir una dirección más precisa. Servicio sujeto a <a class="underline" href="https://operations.osmfoundation.org/policies/nominatim/" target="_blank" rel="noopener noreferrer">su política de uso</a>. © OpenStreetMap contributors.</p>
    </div>

    <label class="block text-sm font-semibold text-gray-800">
        Instagram (URL)
        <input name="instagram" type="url" value="{{ old('instagram') }}" maxlength="500" class="mt-1 w-full rounded-lg border-gray-300" />
    </label>
    <label class="block text-sm font-semibold text-gray-800">
        Sitio web (URL)
        <input name="sitio_web" type="url" value="{{ old('sitio_web') }}" maxlength="500" class="mt-1 w-full rounded-lg border-gray-300" />
    </label>
    <label class="block text-sm font-semibold text-gray-800">
        WhatsApp
        <input name="whatsapp" type="text" value="{{ old('whatsapp') }}" maxlength="30" class="mt-1 w-full rounded-lg border-gray-300" />
    </label>
</div>

<label class="block text-sm font-semibold text-gray-800 md:col-span-2">
    Tipo de establecimiento
    <select name="tipo" required class="mt-1 w-full rounded-lg border-gray-300">
        @foreach(['vinoteca' => 'Vinoteca', 'bodega' => 'Bodega', 'vinedo' => 'Viñedo', 'wine_bar' => 'Wine bar', 'otro' => 'Otro'] as $valor => $etiqueta)
            <option value="{{ $valor }}" @selected(old('tipo', 'vinoteca') === $valor)>{{ $etiqueta }}</option>
        @endforeach
    </select>
</label>
@if(!empty($desdeAdministracion))
    <label class="block text-sm font-semibold text-gray-800 md:col-span-2">Email de contacto (opcional)
        <input name="correo_contacto" type="email" value="{{ old('correo_contacto') }}" class="mt-1 w-full rounded-lg border-gray-300">
    </label>
@endif
<label class="block text-sm font-semibold text-gray-800 md:col-span-2">
    Descripción
    <textarea name="descripcion" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('descripcion') }}</textarea>
</label>
<label class="block text-sm font-semibold text-gray-800 md:col-span-2">
    Servicios del establecimiento
    <textarea name="servicios" rows="3" maxlength="1200" placeholder="Ej.: visitas guiadas, degustaciones, restaurante, venta de vinos" class="mt-1 w-full rounded-lg border-gray-300">{{ old('servicios') }}</textarea>
</label>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-geocodificador]').forEach(function (bloque) {
        const formulario = bloque.closest('form');
        const boton = bloque.querySelector('[data-buscar-ubicacion]');
        const estado = bloque.querySelector('[data-estado-ubicacion]');
        const confirmacion = bloque.querySelector('[data-confirmar-ubicacion]');
        const checkbox = bloque.querySelector('[data-ubicacion-confirmada]');
        const cacheResultados = new Map();
        let ultimaBusqueda = 0;
        const latitud = bloque.querySelector('[data-latitud]');
        const longitud = bloque.querySelector('[data-longitud]');
        const camposDireccion = ['direccion', 'ciudad', 'provincia'].map(function (nombre) {
            return formulario.querySelector('[name="' + nombre + '"]');
        });

        function limpiarUbicacion() {
            latitud.value = '';
            longitud.value = '';
            checkbox.checked = false;
            confirmacion.classList.add('hidden');
            confirmacion.classList.remove('flex');
            estado.textContent = '';
        }

        async function buscarUbicacion() {
            const [direccion, ciudad, provincia] = camposDireccion.map(function (campo) {
                return campo.value.trim();
            });

            if (!direccion || !ciudad || !provincia) {
                estado.textContent = 'Completá dirección, ciudad y provincia antes de buscar.';
                estado.className = 'mt-2 text-sm text-rose-800';
                return false;
            }

            const consulta = direccion + ', ' + ciudad + ', ' + provincia + ', Argentina';
            if (cacheResultados.has(consulta)) {
                aplicarResultado(cacheResultados.get(consulta));
                return true;
            }

            boton.disabled = true;
            boton.textContent = 'Buscando…';
            estado.className = 'mt-2 text-sm text-gray-700';
            estado.textContent = 'Buscando la dirección en OpenStreetMap…';
            limpiarCoordenadas();

            try {
                const espera = Math.max(0, 1100 - (Date.now() - ultimaBusqueda));
                if (espera) await new Promise(function (resolver) { window.setTimeout(resolver, espera); });
                const url = new URL(bloque.dataset.geocodingUrl);
                url.search = new URLSearchParams({
                    format: 'jsonv2',
                    limit: '1',
                    countrycodes: 'ar',
                    'accept-language': 'es',
                    q: consulta
                }).toString();
                ultimaBusqueda = Date.now();
                const respuesta = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' },
                    referrerPolicy: 'strict-origin-when-cross-origin'
                });

                if (!respuesta.ok) throw new Error('No se pudo consultar el servicio de mapas.');
                const resultados = await respuesta.json();
                const lugar = resultados[0];
                const lat = Number(lugar && lugar.lat);
                const lon = Number(lugar && lugar.lon);

                if (!lugar || !Number.isFinite(lat) || !Number.isFinite(lon)) {
                    estado.textContent = 'No encontramos esa dirección. Probá agregar la altura o revisar ciudad y provincia.';
                    estado.className = 'mt-2 text-sm text-rose-800';
                    return false;
                }

                cacheResultados.set(consulta, lugar);
                aplicarResultado(lugar);
                return true;
            } catch (error) {
                estado.textContent = 'No pudimos buscar la dirección. Revisá tu conexión e intentá de nuevo.';
                estado.className = 'mt-2 text-sm text-rose-800';
                return false;
            } finally {
                boton.disabled = false;
                boton.textContent = 'Buscar dirección en el mapa';
            }
        }

        function aplicarResultado(lugar) {
            const lat = Number(lugar.lat);
            const lon = Number(lugar.lon);
            latitud.value = String(lat);
            longitud.value = String(lon);
            const enlace = document.createElement('a');
            enlace.href = 'https://www.openstreetmap.org/?mlat=' + encodeURIComponent(lat) + '&mlon=' + encodeURIComponent(lon) + '#map=18/' + encodeURIComponent(lat) + '/' + encodeURIComponent(lon);
            enlace.target = '_blank';
            enlace.rel = 'noopener noreferrer';
            enlace.className = 'underline';
            enlace.textContent = 'Ver ubicación sugerida ↗';
            estado.replaceChildren(document.createTextNode('Ubicación encontrada: '), enlace);
            estado.className = 'mt-2 text-sm text-green-800';
            confirmacion.classList.remove('hidden');
            confirmacion.classList.add('flex');
        }

        function limpiarCoordenadas() {
            latitud.value = '';
            longitud.value = '';
            checkbox.checked = false;
            confirmacion.classList.add('hidden');
            confirmacion.classList.remove('flex');
        }

        camposDireccion.forEach(function (campo) {
            campo.addEventListener('input', function () {
                limpiarUbicacion();
            });
        });

        boton.addEventListener('click', buscarUbicacion);
        formulario.addEventListener('submit', function (evento) {
            if (!latitud.value || !longitud.value || !checkbox.checked) {
                evento.preventDefault();
                if (latitud.value && longitud.value) {
                    estado.textContent = 'Confirmá que la ubicación sugerida es correcta para poder continuar.';
                    estado.className = 'mt-2 text-sm text-rose-800';
                } else {
                    buscarUbicacion();
                }
            }
        });
    });
});
</script>
