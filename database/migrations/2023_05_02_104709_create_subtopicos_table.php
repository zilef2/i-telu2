<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSubtopicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subtopicos', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('descripcion')->nullable();
            $table->unsignedBigInteger('tema_id');
             
            $table->foreign('tema_id')
                    ->references('id')
                    ->on('temas')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('subtopicos');
    }
}
