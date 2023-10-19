<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('tipo');
			$table->integer('valor');
			$table->String('caducidad');
			$table->String('caducidad_meses');
			$table->integer('tokens');
            $table->timestamps();
        });
    } // php artisan migrate --path=database/migrations/2023_10_15_130740_create_plans_table.php

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
