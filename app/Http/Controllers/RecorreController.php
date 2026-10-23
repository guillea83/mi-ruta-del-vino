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
        return json_decode(Storage::disk('local')->get('recorre/'.$archivo), true) ?: [];
    }

    private function guardar(string $archivo, array $registros): void
    {
        Storage::disk('local')->put('recorre/'.$archivo, json_encode(array_values($registros), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    private function admin(Request $request): bool
    {
        $email = config('recorre.admin_email');
        return $email && $request->user() && strcasecmp($request->user()->email, $email) === 0;
    }

    public function index(): View
    {
        $establecimientos = collect($this->leer('establecimientos.json'))->where('estado', 'aprobado')->values();
        $ids = $establecimientos->pluck('id')->all();
        $eventos = collect($this->leer('eventos.json'))
            ->where('estado', 'aprobado')
            ->filter(fn ($evento) => in_array($evento['establecimiento_id'], $ids, true) && $evento['fecha'] >= now()->toDateString())
            ->sortBy('fecha')->values();

        return view('recorre.index', compact('establecimientos', 'eventos'));
    }

    public function panel(Request $request): View
    {
        $admin = $this->admin($request);
        $establecimientos = collect($this->leer('establecimientos.json'))
            ->filter(fn ($e) => $admin || $e['user_id'] === $request->user()->id)->values();
        $eventos = collect($this->leer('eventos.json'))
            ->filter(fn ($e) => $admin || $establecimientos->contains('id', $e['establecimiento_id']))->values();
        return view('recorre.panel', compact('establecimientos', 'eventos', 'admin'));
    }

    public function registrar(Request $request): RedirectResponse
    {
        $data = $request->validate([
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
        ]);
        $registros = $this->leer('establecimientos.json');
        $registros[] = array_merge($data, [
            'id' => (string) Str::uuid(),
            'user_id' => $request->user()->id,
            'estado' => 'pendiente',
            'plan' => 'gratis',
            'creado_en' => now()->toIso8601String(),
        ]);
        $this->guardar('establecimientos.json', $registros);
        return back()->with('status', 'Solicitud enviada. Aparecerás en Recorré cuando sea aprobada.');
    }

    public function publicarEvento(Request $request): RedirectResponse
    {
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
                && $e['user_id'] === $request->user()->id
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
        return back()->with('status', 'Evento enviado para aprobación.');
    }

    public function moderar(Request $request, string $tipo, string $id): RedirectResponse
    {
        abort_unless($this->admin($request), 403);
        abort_unless(in_array($tipo, ['establecimientos', 'eventos'], true), 404);
        $data = $request->validate(['estado' => 'required|in:aprobado,rechazado,pendiente']);
        $archivo = $tipo.'.json';
        $registros = $this->leer($archivo);
        $encontrado = false;
        foreach ($registros as &$registro) {
            if ($registro['id'] === $id) {
                $registro['estado'] = $data['estado'];
                $encontrado = true;
                break;
            }
        }
        unset($registro);
        abort_unless($encontrado, 404);
        $this->guardar($archivo, $registros);
        return back()->with('status', 'Estado actualizado.');
    }
}
