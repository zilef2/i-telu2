<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateGruposTable extends Migration
{
    //php artisan migrate --path=/database/migrations/2024_01_14_235714_create_grupos_table.php
    //aun no se corre en produccion 15 ene 2023

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('codigo');
			$table->integer('enum');
			$table->text('descripcion');
            $table->unsignedBigInteger('materia_id');
            $table->foreign('materia_id')
                ->references('id')
                ->on('materias')
                ->onDelete('restrict'); //cascade | set null | restrict

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('materias', function (Blueprint $table) {
            $table->integer('horas_de_trabajo_independiente')->nullable();
            $table->integer('horas_de_trabajo_presencial')->nullable();
            $table->integer('numero_de_clases')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupos');
    }
}
