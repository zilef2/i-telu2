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
        Schema::dropIfExists('archivos');

        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('NombreOriginal');
			$table->integer('peso');
			$table->string('type');

			$table->Text('Resumen1')->nullable();
			$table->Text('Resumen2')->nullable();
			$table->Text('Resumen3')->nullable();
			$table->Text('Resumen4')->nullable();
            //scispace
			$table->Text('resumen_2_lineas')->nullable();
			$table->Text('aportes')->nullable();
			$table->Text('articulosRelacionados')->nullable();
			$table->Text('implicacionPracticas')->nullable();
			$table->string('StringcampoAbierto1')->nullable();
			$table->Text('campoAbierto1')->nullable();
			$table->string('StringcampoAbierto2')->nullable();
			$table->Text('campoAbierto2')->nullable();

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
//php artisan migrate:rollback --path=/database/migrations/2023_09_12_143715_create_archivos_table.php

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
