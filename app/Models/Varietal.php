<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Varietal extends Model
{
    protected $table = 'varietales';

    protected $fillable = ['nombre'];

    public function vinos(): BelongsToMany
    {
        return $this->belongsToMany(Vino::class, 'vino_varietal')->withPivot('porcentaje')->withTimestamps();
    }
}
