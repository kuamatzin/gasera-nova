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
            $table->foreignId('user_id')->references('id')->on('users');
            // Propietario
            $table->string('numero_expediente')->unique();
            $table->string('nombre_propietario_dependencia')->nullable();
            $table->string('telefono_recados')->nullable();
            $table->string('correo_electronico')->nullable();
            $table->string('calificacion_propietario')->nullable();
            $table->string('direccion_propietario_notificaciones')->nullable();
            $table->string('codigo_google_street')->nullable();
            $table->boolean('representante_legal')->nullable();
            $table->string('nombre_representante_legal')->nullable();
            $table->string('telefono_recados_representante_legal')->nullable();
            $table->string('correo_electronico_representante_legal')->nullable();
            $table->string('observaciones_representante_legal')->nullable();
            // Inmueble
            $table->string('direccion_inmueble')->nullable();
            $table->string('ejido_inmueble')->nullable();
            $table->string('poblado_inmueble')->nullable();
            $table->string('municipio_inmueble')->nullable();
            $table->string('estado_inmueble')->nullable();
            $table->string('regimen_propiedad_inmueble')->nullable();
            $table->string('uso_suelo_inmueble')->nullable();
            //Superficies a contratar
            $table->string('tipo_afectacion_superficie')->nullable();
            $table->string('superficie_contratada_m2_superficie')->nullable();
            $table->string('superficia_m2_franja_uso_temporal_superficie')->nullable();
            $table->string('superficie_m2_fute_superficie')->nullable();
            $table->string('superficie_total_contratada_m2_superficie')->nullable();
            $table->string('km_inicial_superficie')->nullable();
            $table->string('km_final_superficie')->nullable();
            $table->string('longitud_afectacion_superficie')->nullable();
            $table->string('coordenada_e_superficie')->nullable();
            $table->string('coordenada_n_superficie')->nullable();
            //Mapa afectacion
            $table->string('mapa_afectacion_path')->nullable();
            //Documentacion
            $table->text('documentacion')->nullable();
            $table->string('status')->default('progress');
            //Dictamen legal
            $table->boolean('documentación_completa_firmar_contrato')->nullable();
            $table->boolean('clg_cvd')->nullable();
            $table->text('dictamen_legal')->nullable();
            $table->string('convenio_promesa')->nullable();
            $table->boolean('contrato_definitivo')->nullable();
            $table->boolean('convenio_sujeto_condicion')->nullable();
            $table->boolean('identificacion_inconsistencia_importante')->nullable();
            $table->string('identificacion_inconsistencia_importante_contenido')->nullable();
            $table->boolean('accion_legal_regularizar_tierra')->nullable();
            $table->string('accion_legal_regularizar_tierra_contenido')->nullable();
            $table->string('clasificacion_contingencia_detectada')->nullable();
            $table->float('meses_regularizar_contingencia')->nullable();
            $table->float('monto_aproximado')->nullable();
            $table->text('terminos_condiciones_celebracion_contrato')->nullable();
            $table->string('abogado_emitio_dictamen')->nullable();
            $table->date('fecha_dictamen')->nullable();
            $table->text('dictamen_legal_fase_uno')->nullable();
            $table->text('dictamen_legal_fase_dos')->nullable();
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
