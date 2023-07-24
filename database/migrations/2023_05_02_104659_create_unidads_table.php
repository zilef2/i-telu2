<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUnidadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidads', function (Blueprint $table) {
            $table->id();
			$table->string('codigo')->nullable();
            $table->string('nombre');
			$table->string('enum')->default(1);
			$table->string('descripcion')->nullable();
            $table->unsignedBigInteger('materia_id');
             
            $table->foreign('materia_id')
                    ->references('id')
                    ->on('materias')
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
        Schema::dropIfExists('unidads');
    }
}
