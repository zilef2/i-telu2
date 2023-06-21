<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMateriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('descripcion')->nullable();

			$table->text('objetivo1')->nullable();
			$table->text('objetivo2')->nullable();
			$table->text('objetivo3')->nullable();

            $table->unsignedBigInteger('req1_materia_id')->nullable();
            $table->foreign('req1_materia_id')
                    ->references('id')
                    ->on('materias')
                    ->onDelete('cascade');
            $table->unsignedBigInteger('req2_materia_id')->nullable();
            $table->foreign('req2_materia_id')
                    ->references('id')
                    ->on('materias')
                    ->onDelete('cascade');
            $table->unsignedBigInteger('req3_materia_id')->nullable();
            $table->foreign('req3_materia_id')
                    ->references('id')
                    ->on('materias')
                    ->onDelete('cascade');


            $table->unsignedBigInteger('carrera_id');
            $table->foreign('carrera_id')
                    ->references('id')
                    ->on('carreras')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('materias');
    }
}
