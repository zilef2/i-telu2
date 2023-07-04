<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateRespuestaEjerciciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respuesta_ejercicios', function (Blueprint $table) {
            $table->id();
			$table->text('guardar_pregunta');
			$table->text('respuesta');
			$table->string('nivel')->nullable();//bachiller, universitario
			$table->integer('precisa')->nullable();//0 nada precisa 1 un poco precisa 5 muy precisa
			$table->integer('idExistente')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('respuesta_ejercicios');
    }
}
