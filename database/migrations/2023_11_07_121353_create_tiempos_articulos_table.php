<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTiemposArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiempos_articulos', function (Blueprint $table) {
            $table->id();
			$table->datetime('startTime')->nullable();
			$table->datetime('huellaArticulo')->nullable(); //reconocer el articulo de un usuario cuando dicho articulo aun no ha sido creado
			$table->datetime('endTime')->nullable();
			$table->float('tiempoEscritura',16,1)->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //cascade | set null
            $table->unsignedBigInteger('articulo_id')->nullable();
            $table->foreign('articulo_id')
                ->references('id')
                ->on('articulos')
                ->onDelete('restrict'); //cascade | set null
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tiempos_articulos');
    }
}
