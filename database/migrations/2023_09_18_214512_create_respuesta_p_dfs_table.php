<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateRespuestaPDfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respuesta_p_dfs', function (Blueprint $table) {
            $table->id();
			$table->longText('guardar_pdf');
			$table->longText('resumen');
			$table->string('nivel')->nullable();
			$table->string('precisa')->nullable();
			$table->string('idExistente')->nullable();
            $table->timestamps();
        });
    }
    
    /**
     * php artisan migrate --path=/database/migrations/newSep2023/2023_09_18_214512_create_respuesta_p_dfs_table.php
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('respuesta_p_dfs');
    }
}
