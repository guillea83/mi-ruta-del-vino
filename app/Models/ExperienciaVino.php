<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperienciaVino extends Model
{
    protected $table = 'experiencias_vino';

    protected $fillable = [
        'usuario_vino_id',
        'calificacion_medias_copas',
        'fecha_consumo',
        'lugar',
        'acompanamiento',
        'notas_cata',
        'recuerdo',
        'volveria_a_tomar',
    ];

    protected function casts(): array
    {
        return [
            'fecha_consumo' => 'date',
            'calificacion_medias_copas' => 'integer',
            'volveria_a_tomar' => 'boolean',
        ];
    }

    public function usuarioVino(): BelongsTo
    {
        return $this->belongsTo(UsuarioVino::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoExperienciaVino::class, 'experiencia_vino_id');
    }
}
