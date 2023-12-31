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
    public function up() {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('codigo')->nullable();
			$table->integer('enum')->default(1);
			$table->text('descripcion')->nullable();
            
			$table->integer('activa')->default(1);

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
    public function down() { Schema::dropIfExists('materias'); }
}
