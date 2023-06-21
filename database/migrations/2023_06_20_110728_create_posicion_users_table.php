<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePosicionUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posicion_users', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->integer('importancia')->default(2);//1 es la menos importante
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('posicion_id');
             
            $table->foreign('posicion_id')
                    ->references('id')
                    ->on('posicion_users')
                    ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posicion_users');
    }
}
