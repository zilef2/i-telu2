<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateObjetivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objetivos', function (Blueprint $table) {
            $table->id();
			$table->text('nombre');
			$table->string('descripcion')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('materia_id');
            $table->foreign('materia_id')
                    ->references('id')
                    ->on('materias')
                    ->onDelete('cascade');
        });

        // Schema::table('materias', function (Blueprint $table) {
            
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objetivos');
    }
}
