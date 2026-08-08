<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsuarioVino extends Model
{
    protected $table = 'usuario_vinos';

    protected $fillable = [
        'user_id',
        'vino_id',
        'favorito',
        'volveria_a_comprar',
        'notas_generales',
    ];

    protected function casts(): array
    {
        return [
            'favorito' => 'boolean',
            'volveria_a_comprar' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vino(): BelongsTo
    {
        return $this->belongsTo(Vino::class);
    }

    public function experiencias(): HasMany
    {
        return $this->hasMany(ExperienciaVino::class)
            ->orderByDesc('fecha_consumo')
            ->orderByDesc('id');
    }
}
