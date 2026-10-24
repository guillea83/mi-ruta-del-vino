<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RecorreController extends Controller
{
    private function leer(string $archivo): array
    {
        $disco = Storage::disk('local');

        if (! $disco->exists('recorre/'.$archivo)) {
            return [];
        }

        return json_decode($disco->get('recorre/'.$archivo), true) ?: [];
    }

    private function guardar(string $archivo, array $registros): void
    {
        Storage::disk('local')->put(
            'recorre/'.$archivo,
            json_encode(array_values($registros), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
    }

    private function esAdministrador(Request $request): bool
    {
        $email = trim((string) config('recorre.admin_email', ''));

        return $email !== ''
            && $request->user() !== null
            && strcasecmp($request->user()->email, $email) === 0;
    }

    private function validarEstablecimiento(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:180',
            'tipo' => 'required|in:vinoteca,bodega,vinedo,wine_bar,otro',
            'descripcion' => 'nullable|string|max:2000',
            'direccion' => 'required|string|max:250',
            'ciudad' => 'required|string|max:120',
            'provincia' => 'required|string|max:120',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'instagram' => 'nullable|url|max:500',
            'sitio_web' => 'nullable|url|max:500',
            'whatsapp' => 'nullable|string|max:30',
            'correo_contacto' => 'nullable|email|max:255',
        ]);
    }

    public function index(Request $request): View
    {
        $esAdministrador = $this->esAdministrador($request);
        $establecimientos = collect($this->leer('establecimientos.json'))
            ->where('estado', 'aprobado')->values();

        $ids = $establecimientos->pluck('id')->all();
        $eventos = collect($this->leer('eventos.json'))
            ->where('estado', 'aprobado')
            ->filter(fn ($evento) => in_array($evento['establecimiento_id'] ?? null, $ids, true)
                && ($evento['fecha'] ?? '') >= now()->toDateString())
            ->sortBy('fecha')->values();

        return view('recorre.index', compact('establecimientos', 'eventos', 'esAdministrador'));
    }

    public function panel(Request $request): View|RedirectResponse
    {
        // Un administrador no usa ni puede usar el panel de alta de socios.
        if ($this->esAdministrador($request)) {
            return redirect()->route('recorre.admin');
        }

        $establecimientos = collect($this->leer('establecimientos.json'))
            ->filter(fn ($e) => ($e['user_id'] ?? null) == $request->user()->id)
            ->values();

        $eventos = collect($this->leer('eventos.json'))
            ->filter(fn ($e) => $establecimientos->contains('id', $e['establecimiento_id'] ?? null))
            ->sortByDesc('fecha')->values();

        return view('recorre.panel', compact('establecimientos', 'eventos'));
    }

    public function administrar(Request $request): View
    {
        abort_unless($this->esAdministrador($request), 403);

        $establecimientos = collect($this->leer('establecimientos.json'));
        $eventos = collect($this->leer('eventos.json'));

        return view('recorre.admin', compact('establecimientos', 'eventos'));
    }

    public function registrar(Request $request): RedirectResponse
    {
        // El socio solo puede solicitar el alta. Jamás decide su estado.
        abort_if($this->esAdministrador($request), 403);

        $data = $this->validarEstablecimiento($request);
        $data['correo_contacto'] = $request->user()->email;

        $registros = $this->leer('establecimientos.json');
        $registros[] = array_merge($data, [
            'id' => (string) Str::uuid(),
            'user_id' => $request->user()->id,
            'estado' => 'pendiente',
            'plan' => 'gratis',
            'creado_en' => now()->toIso8601String(),
        ]);

        $this->guardar('establecimientos.json', $registros);

        return redirect()->route('recorre.panel')
            ->with('status', 'Solicitud enviada. El administrador debe aprobar tu establecimiento antes de que aparezca en el mapa.');
    }

    public function crearComoAdministrador(Request $request): RedirectResponse
    {
        abort_unless($this->esAdministrador($request), 403);

        $data = $this->validarEstablecimiento($request);
        $registros = $this->leer('establecimientos.json');

        // Alta administrativa: no es una solicitud del usuario administrador
        // ni asigna al administrador como propietario del establecimiento.
        $registros[] = array_merge($data, [
            'id' => (string) Str::uuid(),
            'user_id' => null,
            'estado' => 'aprobado',
            'plan' => 'gratis',
            'creado_en' => now()->toIso8601String(),
        ]);

        $this->guardar('establecimientos.json', $registros);

        return redirect()->route('recorre.admin')
            ->with('status', 'Establecimiento incorporado y aprobado por administración.');
    }

    public function publicarEvento(Request $request): RedirectResponse
    {
        abort_if($this->esAdministrador($request), 403);

        $data = $request->validate([
            'establecimiento_id' => 'required|uuid',
            'titulo' => 'required|string|max:180',
            'descripcion' => 'nullable|string|max:2000',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'nullable|date_format:H:i',
            'enlace' => 'nullable|url|max:500',
        ]);

        $autorizado = collect($this->leer('establecimientos.json'))->contains(
            fn ($e) => $e['id'] === $data['establecimiento_id']
                && ($e['user_id'] ?? null) == $request->user()->id
                && $e['estado'] === 'aprobado'
        );
        abort_unless($autorizado, 403);

        $eventos = $this->leer('eventos.json');
        $eventos[] = array_merge($data, [
            'id' => (string) Str::uuid(),
            'estado' => 'pendiente',
            'creado_en' => now()->toIso8601String(),
        ]);

        $this->guardar('eventos.json', $eventos);

        return redirect()->route('recorre.panel')
            ->with('status', 'Evento enviado para aprobación administrativa.');
    }

    public function moderar(Request $request, string $tipo, string $id): RedirectResponse
    {
        abort_unless($this->esAdministrador($request), 403);
        abort_unless(in_array($tipo, ['establecimientos', 'eventos'], true), 404);

        $data = $request->validate(['estado' => 'required|in:aprobado,rechazado,pendiente']);
        $archivo = $tipo.'.json';
        $registros = $this->leer($archivo);
        $indice = collect($registros)->search(fn ($registro) => ($registro['id'] ?? null) === $id);

        abort_if($indice === false, 404);

        // Defensa adicional por si existieran altas antiguas del propio admin.
        if ($tipo === 'establecimientos') {
            $propietario = $registros[$indice]['user_id'] ?? null;
        } else {
            $establecimientoId = $registros[$indice]['establecimiento_id'] ?? null;
            $establecimiento = collect($this->leer('establecimientos.json'))
                ->firstWhere('id', $establecimientoId);
            $propietario = $establecimiento['user_id'] ?? null;
        }

        abort_if($propietario !== null && (string) $propietario === (string) $request->user()->id, 403);

        $registros[$indice]['estado'] = $data['estado'];
        $this->guardar($archivo, $registros);

        return redirect()->route('recorre.admin')->with('status', 'Estado actualizado.');
    }
}
