<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiencias_vino', function (Blueprint $table) {
            $table->string('modalidad', 30)->nullable()->after('lugar');
            $table->decimal('precio_pagado', 12, 2)->nullable()->after('modalidad');
            $table->string('moneda', 3)->nullable()->after('precio_pagado');
            $table->string('lugar_compra', 180)->nullable()->after('moneda');
        });
    }

    public function down(): void
    {
        Schema::table('experiencias_vino', function (Blueprint $table) {
            $table->dropColumn([
                'modalidad',
                'precio_pagado',
                'moneda',
                'lugar_compra',
            ]);
        });
    }
};
