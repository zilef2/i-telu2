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
			$table->string('version');
            
			$table->text('Portada');
			$table->text('Resumen');
			$table->text('Palabras_Clave', "16000");
			$table->text('Introduccion', "16000");
			$table->text('Revision_de_la_Literatura', "16000");
			$table->text('Metodologia', "16000");
			$table->text('Resultados', "16000");
			$table->text('Discusion', "16000");
			$table->text('Conclusiones', "16000");
			$table->text('Agradecimientos', "16000");
			$table->text('Referencias', "16000");
			$table->text('Anexos_o_Apendices', "16000");

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //restrict | set null
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
        Schema::dropIfExists('articulos');
    }
}
