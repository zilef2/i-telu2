<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioPendientesPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_pendientes_pagos', function (Blueprint $table) {
            $table->id();
			$table->timestamp('fecha_peticion')->nullable();
			$table->timestamp('fecha_aprovacion')->nullable();
			$table->decimal('valorTotal',15,1)->nullable();
			$table->integer('tokensComprados')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('user_id');
                        $table->foreign('user_id')
                            ->references('id')
                            ->on('users')
                            ->onDelete('restrict'); //cascade | set null
            $table->unsignedBigInteger('plan_id');
                        $table->foreign('plan_id')
                            ->references('id')
                            ->on('plans')
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
        Schema::dropIfExists('usuario_pendientes_pagos');
    }
}
