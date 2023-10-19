<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
			$table->string('nick');
			$table->string('version')->default(1);
            
			$table->text('Portada')->nullable();
			$table->text('Resumen')->nullable();
			$table->integer('Resumen_integer')->default(0);
			$table->text('Resumen_ia')->nullable();
			$table->text('Resumen_final')->nullable();
			$table->text('Palabras_Clave', "32000")->nullable();
			$table->text('Introduccion', "32000")->nullable();
			$table->integer('Introduccion_integer')->default(0);
			$table->text('Introduccion_ia', "32000")->nullable();
			$table->text('Introduccion_final', "32000")->nullable();
			$table->text('Revision_de_la_Literatura', "32000")->nullable();
			$table->text('Metodologia', "32000")->nullable();
			$table->text('Metodologia_ia', "32000")->nullable();
			$table->text('Metodologia_final', "32000")->nullable();
			$table->integer('Metodologia_integer')->default(1);
			$table->text('Resultados', "32000")->nullable();
			$table->text('Discusion', "32000")->nullable();
			$table->integer('Discusion_integer')->default(0);
			$table->text('Discusion_ia', "32000")->nullable();
			$table->text('Discusion_final', "32000")->nullable();
			$table->text('Conclusiones', "32000")->nullable();
			$table->integer('Conclusiones_integer')->default(0);
			$table->text('Conclusiones_ia', "32000")->nullable();
			$table->text('Conclusiones_final', "32000")->nullable();
			$table->text('Agradecimientos', "32000")->nullable();
			$table->text('Referencias', "32000")->nullable();
			$table->text('Anexos_o_Apendices', "32000")->nullable();

			$table->float('Resumen_critica')->nullable();
            $table->float('Introduccion_critica')->nullable();
            $table->float('Discusion_critica')->nullable();
            $table->float('Conclusiones_critica')->nullable();
            $table->float('Metodologia_critica')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //restrict | set null

			$table->unsignedBigInteger('universidad_id');
			$table->foreign('universidad_id')
				->references('id')
				->on('universidads')
				->onDelete('restrict'); //restrict | set null

			$table->unsignedBigInteger('carrera_id');
			$table->foreign('carrera_id')
				->references('id')
				->on('carreras')
				->onDelete('restrict');

			$table->unsignedBigInteger('materia_id');
			$table->foreign('materia_id')
				->references('id')
				->on('materias')
				->onDelete('restrict');

            $table->unsignedBigInteger('libre_id')->nullable();
            $table->string('Modelo_de_libre_id')->nullable();
            $table->timestamps();
        });
    }
	// php artisan migrate --path=database/migrations/2023_10_02_171802_create_articulos_table.php
	//php artisan migrate --path=/database\migrations\2023_10_16_131013_create_calificacions_table.php



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}
