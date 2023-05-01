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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            // Propietario
            $table->string('nombre_propietario_dependencia');
            $table->string('telefono_recados');
            $table->string('correo_electronico');
            $table->string('calificacion_propietario');
            $table->string('direccion_propietario_notificaciones');
            $table->string('codigo_google_street');
            $table->boolean('representante_legal');
            $table->string('nombre_representante_legal')->nullable();
            $table->string('telefono_recados_representante_legal')->nullable();
            $table->string('correo_electronico_representante_legal')->nullable();
            $table->string('observaciones_representante_legal')->nullable();
            // Inmueble
            $table->string('direccion_inmueble');
            $table->string('poblado_inmueble');
            $table->string('municipio_inmueble');
            $table->string('estado_inmueble');
            $table->string('regimen_propiedad_inmueble');
            $table->string('uso_suelo_inmueble');
            //Superficies a contratar
            $table->string('tipo_afectacion_superficie');
            $table->string('superficie_contratada_m2_superficie');
            $table->string('superficia_m2_franja_uso_temporal_superficie');
            $table->string('superficie_m2_fute_superficie');
            $table->string('superficie_total_contratada_m2_superficie');
            $table->string('km_inicial_superficie');
            $table->string('km_final_superficie');
            $table->string('longitud_afectacion_superficie');
            $table->string('coordenada_e_superficie');
            $table->string('coordenada_n_superficie');
            //Mapa afectacion
            $table->string('mapa_afectacion_path');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
