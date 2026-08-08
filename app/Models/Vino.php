<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vino extends Model
{
    protected $fillable = [
        'bodega_id',
        'nombre',
        'anio',
        'pais',
        'region',
        'tipo',
        'graduacion_alcoholica',
    ];

    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'graduacion_alcoholica' => 'decimal:1',
        ];
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class);
    }

    public function varietales(): BelongsToMany
    {
        return $this->belongsToMany(Varietal::class, 'vino_varietal')->withPivot('porcentaje')->withTimestamps();
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(UsuarioVino::class);
    }
}
