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
            $table->boolean('conyuge_bienes_mancomunados')->nullable();
            $table->text('conyuge_bienes_mancomunados_documentacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn('conyuge_bienes_mancomunados');
            $table->dropColumn('conyuge_bienes_mancomunados_documentacion');
        });
    }
};
