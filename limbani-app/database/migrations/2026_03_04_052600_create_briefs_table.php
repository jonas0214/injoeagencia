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
        Schema::create('briefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->text('objectives')->nullable()->comment('Objetivos del mes');
            $table->text('target_audience')->nullable()->comment('Audiencia objetivo');
            $table->text('key_dates')->nullable()->comment('Fechas especiales importantes');
            $table->decimal('budget', 12, 2)->nullable()->comment('Presupuesto asignado');
            $table->text('special_requirements')->nullable()->comment('Requerimientos especiales');
            $table->text('key_messages')->nullable()->comment('Mensajes clave a comunicar');
            $table->text('success_metrics')->nullable()->comment('Métricas de éxito');
            $table->text('competitor_analysis')->nullable()->comment('Análisis de competencia');
            $table->text('brand_guidelines')->nullable()->comment('Guías de marca');
            $table->text('content_preferences')->nullable()->comment('Preferencias de contenido');
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('project_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('briefs');
    }
};