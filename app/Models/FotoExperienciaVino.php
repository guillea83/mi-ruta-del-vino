<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoExperienciaVino extends Model
{
    protected $table = 'fotos_experiencias_vino';

    protected $fillable = [
        'experiencia_vino_id',
        'ruta',
        'es_principal',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
        ];
    }

    public function experiencia(): BelongsTo
    {
        return $this->belongsTo(ExperienciaVino::class, 'experiencia_vino_id');
    }
}
