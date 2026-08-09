<?php

use App\Http\Controllers\MiBodegaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/mi-bodega', [MiBodegaController::class, 'index'])->name('mi-bodega.index');
    Route::get('/mi-bodega/guardar', [MiBodegaController::class, 'create'])->name('mi-bodega.create');
    Route::post('/mi-bodega', [MiBodegaController::class, 'store'])->name('mi-bodega.store');
    Route::get('/mi-bodega/{usuarioVino}', [MiBodegaController::class, 'show'])->whereNumber('usuarioVino')->name('mi-bodega.show');
    Route::post('/mi-bodega/{usuarioVino}/favorito', [MiBodegaController::class, 'toggleFavorito'])->whereNumber('usuarioVino')->name('mi-bodega.favorito');
    Route::post('/mi-bodega/{usuarioVino}/experiencias', [MiBodegaController::class, 'storeExperiencia'])->whereNumber('usuarioVino')->name('mi-bodega.experiencias.store');
    Route::put('/mi-bodega/{usuarioVino}/experiencias/{experiencia}', [MiBodegaController::class, 'updateExperiencia'])->whereNumber(['usuarioVino', 'experiencia'])->name('mi-bodega.experiencias.update');
    Route::delete('/mi-bodega/{usuarioVino}/experiencias/{experiencia}', [MiBodegaController::class, 'destroyExperiencia'])->whereNumber(['usuarioVino', 'experiencia'])->name('mi-bodega.experiencias.destroy');
});
