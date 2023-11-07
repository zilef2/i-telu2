<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
			$table->integer('numeroDeLaCuota')->nullable();
			$table->integer('numeroDecuotas')->nullable();
			$table->float('valor')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('usuario_pendientes_pago_id');
                        $table->foreign('usuario_pendientes_pago_id')
                            ->references('id')
                            ->on('usuario_pendientes_pagos')
                            ->onDelete('restrict'); //cascade | set null
        });

        Schema::table('usuario_pendientes_pagos', function (Blueprint $table) {$table->softDeletes();});
        Schema::table('cuotas', function (Blueprint $table) {$table->softDeletes();});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuotas');
    }
}
