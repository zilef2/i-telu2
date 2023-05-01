<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('nombre');
            $table->string('codigo');
            $table->integer('precio')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('observaciones')->nullable();

            $table->unsignedBigInteger('centrotrabajo_id');
            $table->foreign('centrotrabajo_id')
                ->references('id')
                ->on('centro_trabajos')
                ->onDelete('restrict'); //restrict cascade
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
