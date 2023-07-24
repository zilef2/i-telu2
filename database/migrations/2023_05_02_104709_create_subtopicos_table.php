<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSubtopicosTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('subtopicos', function (Blueprint $table) {
            $table->id();
			$table->string('codigo')->nullable();
            $table->string('nombre');
			$table->string('enum')->default(1);
            $table->string('descripcion')->nullable();
            $table->string('resultado_aprendizaje')->nullable();
            $table->unsignedBigInteger('unidad_id');

            $table->foreign('unidad_id')
                ->references('id')
                ->on('unidads')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('subtopicos');
    }
}
