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
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Nombre del proyecto (ej: Campaña Ads)
                $table->text('description')->nullable();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quién lo creó
                $table->enum('status', ['activo', 'completado', 'pausado'])->default('activo');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
