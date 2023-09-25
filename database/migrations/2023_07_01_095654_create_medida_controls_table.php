<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMedidaControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medida_controls', function (Blueprint $table) {
            $table->id();
			$table->text('pregunta')->nullable();
			$table->text('respuesta_guardada')->nullable();
			$table->string('tokens_usados')->nullable();
            
			$table->string('RazonNOSubtopico')->nullable();
            
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->unsignedBigInteger('subtopico_id')->nullable();
            $table->foreign('subtopico_id')
                ->references('id')
                ->on('subtopicos')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }
//php artisan migrate --path=/database\migrations\2023_09_12_143715_create_archivos_table.php

    /**b
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medida_controls');
    }
}
