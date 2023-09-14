<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('NombreOriginal');
			$table->integer('peso');
			$table->string('type');

            $table->unsignedBigInteger('materia_id')->nullable();
            $table->foreign('materia_id')
                ->references('id')
                ->on('materias')
                ->onDelete('restrict');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
                // ->onDelete('set null');


            $table->timestamps();
        });
    }
//php artisan migrate --path=/database\migrations\2023_09_12_143715_create_archivos_table.php
    //database\migrations\2023_09_12_143715_create_archivos_table.php
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archivos');
    }
}
