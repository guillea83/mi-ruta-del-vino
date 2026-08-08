<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bodegas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('pais')->nullable();
            $table->string('provincia')->nullable();
            $table->string('region')->nullable();
            $table->string('sitio_web')->nullable();
            $table->string('instagram')->nullable();
            $table->timestamps();
        });

        Schema::create('varietales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::create('vinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bodega_id')->nullable()->constrained('bodegas')->nullOnDelete();
            $table->string('nombre');
            $table->unsignedSmallInteger('anio')->nullable();
            $table->string('pais')->nullable();
            $table->string('region')->nullable();
            $table->string('tipo')->nullable();
            $table->decimal('graduacion_alcoholica', 4, 1)->nullable();
            $table->timestamps();

            $table->index(['bodega_id', 'nombre', 'anio']);
        });

        Schema::create('vino_varietal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vino_id')->constrained('vinos')->cascadeOnDelete();
            $table->foreignId('varietal_id')->constrained('varietales')->cascadeOnDelete();
            $table->unsignedTinyInteger('porcentaje')->nullable();
            $table->timestamps();

            $table->unique(['vino_id', 'varietal_id']);
        });

        Schema::create('usuario_vinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vino_id')->constrained('vinos')->cascadeOnDelete();
            $table->boolean('favorito')->default(false);
            $table->boolean('volveria_a_comprar')->nullable();
            $table->text('notas_generales')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'vino_id']);
        });

        Schema::create('experiencias_vino', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_vino_id')->constrained('usuario_vinos')->cascadeOnDelete();
            $table->unsignedTinyInteger('calificacion_medias_copas')->nullable();
            $table->date('fecha_consumo')->nullable();
            $table->string('lugar')->nullable();
            $table->string('acompanamiento')->nullable();
            $table->text('notas_cata')->nullable();
            $table->text('recuerdo')->nullable();
            $table->boolean('volveria_a_tomar')->nullable();
            $table->timestamps();

            $table->index(['usuario_vino_id', 'fecha_consumo']);
        });

        Schema::create('fotos_experiencias_vino', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experiencia_vino_id')->constrained('experiencias_vino')->cascadeOnDelete();
            $table->string('ruta');
            $table->boolean('es_principal')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos_experiencias_vino');
        Schema::dropIfExists('experiencias_vino');
        Schema::dropIfExists('usuario_vinos');
        Schema::dropIfExists('vino_varietal');
        Schema::dropIfExists('vinos');
        Schema::dropIfExists('varietales');
        Schema::dropIfExists('bodegas');
    }
};
