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
            

            $table->string('sexo')->nullable();
            $table->string('fecha_nacimiento')->nullable();
            $table->integer('semestre')->default(1);
            $table->integer('semestre_mas_bajo')->default(1);
            $table->string('limite_token_general')->default(3);
            $table->string('limite_token_leccion')->default(3);
            
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
