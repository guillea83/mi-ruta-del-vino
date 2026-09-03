<?php

use App\Http\Controllers\MiBodegaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);
    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('storage.public');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/optimize-clear', function () {
        abort_unless(
            auth()->user()?->email === config('app.optimize_clear_email'),
            403
        );

        Artisan::call('optimize:clear');

        return 'Optimize clear ejecutado correctamente.<br><pre>'
            . Artisan::output()
            . '</pre>';
    })->name('optimize-clear');

    Route::get('/mi-bodega', [MiBodegaController::class, 'index'])->name('mi-bodega.index');
    Route::get('/mi-bodega/guardar', [MiBodegaController::class, 'create'])->name('mi-bodega.create');
    Route::post('/mi-bodega', [MiBodegaController::class, 'store'])->name('mi-bodega.store');
    Route::get('/mi-bodega/{usuarioVino}', [MiBodegaController::class, 'show'])->whereNumber('usuarioVino')->name('mi-bodega.show');
    Route::post('/mi-bodega/{usuarioVino}/favorito', [MiBodegaController::class, 'toggleFavorito'])->whereNumber('usuarioVino')->name('mi-bodega.favorito');
    Route::post('/mi-bodega/{usuarioVino}/experiencias', [MiBodegaController::class, 'storeExperiencia'])->whereNumber('usuarioVino')->name('mi-bodega.experiencias.store');
    Route::put('/mi-bodega/{usuarioVino}/experiencias/{experiencia}', [MiBodegaController::class, 'updateExperiencia'])->whereNumber(['usuarioVino', 'experiencia'])->name('mi-bodega.experiencias.update');
    Route::delete('/mi-bodega/{usuarioVino}/experiencias/{experiencia}', [MiBodegaController::class, 'destroyExperiencia'])->whereNumber(['usuarioVino', 'experiencia'])->name('mi-bodega.experiencias.destroy');
});
