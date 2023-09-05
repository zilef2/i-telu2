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
        Schema::create('los_promps', function (Blueprint $table) {
            $table->id();
            $table->text('principal');
            $table->string('clasificacion');
            $table->string('teoricaOpractica');
            $table->integer('tokensAproximados');

            $table->timestamps();
        });

        //! tablas pivote
        Schema::create('los_promps_subtopico', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('subtopico_id');
            $table->unsignedBigInteger('los_promps_id');
            $table->foreign('subtopico_id')
                    ->references('id')
                    ->on('subtopicos')
                    ->onDelete('cascade');//cascade or restrict
            $table->foreign('los_promps_id')
                    ->references('id')
                    ->on('los_promps')
                    ->onDelete('cascade'); //cascade or restrict
        });

        Schema::create('los_promps_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('los_promps_id');
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('restrict'); //cascade or restrict

            $table->foreign('los_promps_id')
                    ->references('id')
                    ->on('los_promps')
                    ->onDelete('restrict'); //cascade or restrict
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('los_promps');
        Schema::dropIfExists('los_promps_subtopico');
        Schema::dropIfExists('subtopico_user');
    }
};
