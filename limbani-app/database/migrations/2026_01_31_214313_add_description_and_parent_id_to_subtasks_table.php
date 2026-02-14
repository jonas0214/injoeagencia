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
        Schema::table('subtasks', function (Blueprint $table) {
            // 1. Agregar campo de descripción (para solucionar tu error 500)
            if (!Schema::hasColumn('subtasks', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            // 2. Agregar parent_id (para que funcionen las subtareas dentro de subtareas)
            if (!Schema::hasColumn('subtasks', 'parent_id')) {
                $table->foreignId('parent_id')
                      ->nullable()
                      ->after('task_id')
                      ->constrained('subtasks')
                      ->onDelete('cascade');
            }
            
            // 3. Agregar campo para la sugerencia de IA (si no lo tienes aún)
            if (!Schema::hasColumn('subtasks', 'ai_suggestion')) {
                $table->text('ai_suggestion')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropColumn(['description', 'parent_id', 'ai_suggestion']);
        });
    }
};
