<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCalificacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('calificacions');

        Schema::create('calificacions', function (Blueprint $table) {
            $table->id();
			$table->string('TipoPrueba')->nullable();
			$table->text('prompUsado')->nullable();
			$table->float('valor')->nullable();
			$table->float('valorIA')->nullable();

            $table->float('valor_Resumen')->nullable();
            $table->float('valor_Introduccion')->nullable();
            $table->float('valor_Discusion')->nullable();
            $table->float('valor_Conclusiones')->nullable();
            $table->float('valor_Metodologia')->nullable();

			$table->integer('tokens')->nullable();

			$table->bigInteger('libre_id')->nullable();
			$table->string('Modelo_de_libre')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();//quien la realizo el articulo u otro
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //restrict | set null

            $table->unsignedBigInteger('QuienCalifico')->nullable();
            $table->foreign('QuienCalifico')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //restrict | set null
            $table->unsignedBigInteger('UniCarreraMateria')->nullable();
            $table->unsignedBigInteger('UniCarreraMateriaID')->nullable();

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
        Schema::dropIfExists('calificacions');
    }
}
// php artisan migrate --path=database/migrations/2023_10_02_171802_create_articulos_table.php
//php artisan migrate --path=database/migrations/2023_10_16_131013_create_calificacions_table.php
//php artisan migrate --path=database/migrations/2023_10_15_130740_create_plans_table.php
//php artisan db:seed --class=PlanSeeder
