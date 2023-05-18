<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->text('nombre_propietario_dependencia')->nullable()->change();
            $table->text('direccion_propietario_notificaciones')->nullable()->change();
            $table->text('codigo_google_street')->nullable()->change();
            $table->text('nombre_representante_legal')->nullable()->change();
            $table->text('observaciones_representante_legal')->nullable()->change();
            // Inmueble
            $table->text('direccion_inmueble')->nullable()->change();
            $table->text('ejido_inmueble')->nullable()->change();
            $table->text('poblado_inmueble')->nullable()->change();
            $table->text('uso_suelo_inmueble')->nullable()->change();
            //Dictamen legal
            $table->text('convenio_promesa')->nullable()->change();
            $table->text('identificacion_inconsistencia_importante_contenido')->nullable()->change();
            $table->text('accion_legal_regularizar_tierra_contenido')->nullable()->change();
            $table->text('clasificacion_contingencia_detectada')->nullable()->change();
            $table->text('abogado_emitio_dictamen')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            //
        });
    }
};
