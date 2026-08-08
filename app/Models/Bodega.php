<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bodega extends Model
{
    protected $fillable = ['nombre', 'pais', 'provincia', 'region', 'sitio_web', 'instagram'];

    public function vinos(): HasMany
    {
        return $this->hasMany(Vino::class);
    }
}
