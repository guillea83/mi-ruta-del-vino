<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\ExperienciaVino;
use App\Models\FotoExperienciaVino;
use App\Models\UsuarioVino;
use App\Models\Varietal;
use App\Models\Vino;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MiBodegaController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));

        $items = UsuarioVino::query()
            ->where('user_id', $request->user()->id)
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->whereHas('vino', function ($vinoQuery) use ($buscar) {
                    $vinoQuery->where(function ($q) use ($buscar) {
                        $q->where('nombre', 'like', "%{$buscar}%")
                            ->orWhereHas('bodega', fn ($bodega) => $bodega->where('nombre', 'like', "%{$buscar}%"))
                            ->orWhereHas('varietales', fn ($varietal) => $varietal->where('nombre', 'like', "%{$buscar}%"));
                    });
                });
            })
            ->with(['vino.bodega', 'vino.varietales', 'experiencias.fotos'])
            ->withCount('experiencias')
            ->withAvg('experiencias as promedio_medias_copas', 'calificacion_medias_copas')
            ->latest('updated_at')
            ->get();

        return view('mi-bodega.index', compact('items', 'buscar'));
    }

    public function create(): View
    {
        $vinos = Vino::query()
            ->with(['bodega', 'varietales'])
            ->orderBy('nombre')
            ->get();

        return view('mi-bodega.create', compact('vinos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validarCarga($request, true);
        $usuarioVino = null;

        DB::transaction(function () use ($request, $data, &$usuarioVino) {
            $vino = $this->resolverVino($data);

            $usuarioVino = UsuarioVino::firstOrCreate([
                'user_id' => $request->user()->id,
                'vino_id' => $vino->id,
            ]);

            $this->guardarExperiencia($request, $usuarioVino, $data);
        });

        return redirect()
            ->route('mi-bodega.show', $usuarioVino)
            ->with('status', 'El vino quedó guardado en tu Bodega Personal.');
    }

    public function show(Request $request, int $usuarioVino): View
    {
        $item = $this->buscarDelUsuario($request, $usuarioVino);

        return view('mi-bodega.show', compact('item'));
    }

    public function toggleFavorito(Request $request, int $usuarioVino): RedirectResponse
    {
        $item = $this->buscarDelUsuario($request, $usuarioVino);
        $item->update(['favorito' => ! $item->favorito]);

        return back()->with(
            'status',
            $item->favorito ? 'Agregado a tus favoritos.' : 'Quitado de tus favoritos.'
        );
    }

    public function storeExperiencia(Request $request, int $usuarioVino): RedirectResponse
    {
        $item = $this->buscarDelUsuario($request, $usuarioVino);
        $data = $this->validarCarga($request, false);

        DB::transaction(function () use ($request, $item, $data) {
            $this->guardarExperiencia($request, $item, $data);
        });

        return redirect()
            ->route('mi-bodega.show', $item)
            ->with('status', 'Nueva experiencia agregada sin modificar las anteriores.');
    }

    public function updateExperiencia(Request $request, int $usuarioVino, int $experiencia): RedirectResponse
    {
        $item = $this->buscarDelUsuario($request, $usuarioVino);
        $experienciaModel = $item->experiencias()->whereKey($experiencia)->firstOrFail();
        $data = $this->validarCarga($request, false);

        DB::transaction(function () use ($request, $item, $experienciaModel, $data) {
            $experienciaModel->update([
                'calificacion_medias_copas' => $data['calificacion_medias_copas'],
                'fecha_consumo' => $data['fecha_consumo'] ?? null,
                'lugar' => $data['lugar'] ?? null,
                'acompanamiento' => $data['acompanamiento'] ?? null,
                'notas_cata' => $data['notas_cata'] ?? null,
                'recuerdo' => $data['recuerdo'] ?? null,
                'volveria_a_tomar' => $data['volveria_a_tomar'] ?? null,
            ]);

            if ($request->hasFile('foto')) {
                $ruta = $request->file('foto')->store('vinos/'.$request->user()->id, 'public');

                FotoExperienciaVino::create([
                    'experiencia_vino_id' => $experienciaModel->id,
                    'ruta' => $ruta,
                    'es_principal' => $experienciaModel->fotos()->doesntExist(),
                ]);
            }

            $item->touch();
        });

        return redirect()
            ->route('mi-bodega.show', $item)
            ->with('status', 'Experiencia actualizada.');
    }

    public function destroyExperiencia(Request $request, int $usuarioVino, int $experiencia): RedirectResponse
    {
        $item = $this->buscarDelUsuario($request, $usuarioVino);
        $experienciaModel = $item->experiencias()->with('fotos')->whereKey($experiencia)->firstOrFail();
        $rutas = $experienciaModel->fotos->pluck('ruta')->all();
        $eraLaUltima = $item->experiencias()->count() === 1;

        DB::transaction(function () use ($item, $experienciaModel, $eraLaUltima) {
            $experienciaModel->delete();

            if ($eraLaUltima) {
                $item->delete();
            } else {
                $item->touch();
            }
        });

        if ($rutas !== []) {
            Storage::disk('public')->delete($rutas);
        }

        if ($eraLaUltima) {
            return redirect()
                ->route('mi-bodega.index')
                ->with('status', 'El vino salió de tu Bodega Personal porque ya no tenía experiencias.');
        }

        return redirect()
            ->route('mi-bodega.show', $item->id)
            ->with('status', 'Experiencia eliminada.');
    }

    private function buscarDelUsuario(Request $request, int $id): UsuarioVino
    {
        return UsuarioVino::query()
            ->where('user_id', $request->user()->id)
            ->with(['vino.bodega', 'vino.varietales', 'experiencias.fotos'])
            ->withAvg('experiencias as promedio_medias_copas', 'calificacion_medias_copas')
            ->findOrFail($id);
    }

    private function validarCarga(Request $request, bool $incluyeVino): array
    {
        $rules = [
            'calificacion_medias_copas' => ['required', 'integer', 'between:0,10'],
            'fecha_consumo' => ['nullable', 'date'],
            'lugar' => ['nullable', 'string', 'max:180'],
            'acompanamiento' => ['nullable', 'string', 'max:255'],
            'notas_cata' => ['nullable', 'string', 'max:3000'],
            'recuerdo' => ['nullable', 'string', 'max:3000'],
            'volveria_a_tomar' => ['nullable', 'boolean'],
            'foto' => ['nullable', 'image', 'max:10240'],
        ];

        if ($incluyeVino) {
            $rules += [
                'vino_id' => ['nullable', 'integer', 'exists:vinos,id'],
                'nombre' => ['nullable', 'string', 'max:180', 'required_without:vino_id'],
                'bodega_nombre' => ['nullable', 'string', 'max:180', 'required_without:vino_id'],
                'varietal_nombre' => ['nullable', 'string', 'max:100'],
                'anio' => ['nullable', 'integer', 'between:1800,2100'],
                'region' => ['nullable', 'string', 'max:150'],
            ];
        }

        return $request->validate($rules);
    }

    private function resolverVino(array $data): Vino
    {
        if (! empty($data['vino_id'])) {
            return Vino::findOrFail($data['vino_id']);
        }

        $bodega = Bodega::firstOrCreate([
            'nombre' => trim($data['bodega_nombre']),
        ]);

        $query = Vino::query()
            ->where('bodega_id', $bodega->id)
            ->where('nombre', trim($data['nombre']));

        if (! empty($data['anio'])) {
            $query->where('anio', (int) $data['anio']);
        } else {
            $query->whereNull('anio');
        }

        $vino = $query->first();

        if (! $vino) {
            $vino = Vino::create([
                'bodega_id' => $bodega->id,
                'nombre' => trim($data['nombre']),
                'anio' => $data['anio'] ?? null,
                'region' => $data['region'] ?? null,
            ]);
        }

        if (! empty($data['varietal_nombre'])) {
            $varietal = Varietal::firstOrCreate([
                'nombre' => trim($data['varietal_nombre']),
            ]);

            $vino->varietales()->syncWithoutDetaching([$varietal->id]);
        }

        return $vino;
    }

    private function guardarExperiencia(Request $request, UsuarioVino $usuarioVino, array $data): ExperienciaVino
    {
        $experiencia = $usuarioVino->experiencias()->create([
            'calificacion_medias_copas' => $data['calificacion_medias_copas'],
            'fecha_consumo' => $data['fecha_consumo'] ?? now()->toDateString(),
            'lugar' => $data['lugar'] ?? null,
            'acompanamiento' => $data['acompanamiento'] ?? null,
            'notas_cata' => $data['notas_cata'] ?? null,
            'recuerdo' => $data['recuerdo'] ?? null,
            'volveria_a_tomar' => $data['volveria_a_tomar'] ?? null,
        ]);

        if ($request->hasFile('foto')) {
            $ruta = $request->file('foto')->store('vinos/'.$request->user()->id, 'public');

            FotoExperienciaVino::create([
                'experiencia_vino_id' => $experiencia->id,
                'ruta' => $ruta,
                'es_principal' => true,
            ]);
        }

        $usuarioVino->touch();

        return $experiencia;
    }
}
