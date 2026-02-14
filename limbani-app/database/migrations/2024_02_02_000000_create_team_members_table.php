<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre Completo
            $table->string('cedula')->unique(); // Cédula
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable(); // Teléfono
            $table->string('position'); // Cargo que desempeña
            $table->decimal('salary', 12, 2); // Asignación Salarial
            $table->text('bank_details')->nullable(); // Información Bancaria (Banco, Cuenta, Tipo)
            $table->string('status')->default('active'); // Activo / Inactivo
            $table->date('hire_date')->nullable(); // Fecha de ingreso
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_members');
    }
};