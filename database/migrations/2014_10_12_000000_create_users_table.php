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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->string ('identificacion')->unique();
            $table->string ('sexo')->nullable();
            $table->dateTime('fecha_nacimiento')->nullable();
            $table->string ('pgrado')->nullable();
            $table->string ('semestre')->nullable();
            $table->string ('semestre_mas_bajo')->nullable();
            $table->string ('limite_token_general')->nullable();
            $table->string ('limite_token_leccion')->nullable();
            $table->integer('plan')->default(0);
            
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
