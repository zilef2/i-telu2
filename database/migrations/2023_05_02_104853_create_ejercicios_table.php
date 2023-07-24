<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateEjerciciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ejercicios', function (Blueprint $table) {
            $table->id();
			$table->string('enum')->nullable();
			$table->string('nombre');
			$table->string('descripcion')->nullable();
            $table->unsignedBigInteger('subtopico_id');
             
            $table->foreign('subtopico_id')
                    ->references('id')
                    ->on('subtopicos')
                    ->onDelete('cascade');

			$table->integer('seHaPreguntado')->default(0);
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
        Schema::dropIfExists('ejercicios');
    }
}
