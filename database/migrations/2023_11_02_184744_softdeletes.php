<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate --path=/database/migrations/2023_11_02_184744_softdeletes.php
     */
//        Schema::table('respuesta_p_dfs', function (Blueprint $table) {$table->softDeletes();});
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {$table->softDeletes();});
        Schema::table('respuesta_ejercicios', function (Blueprint $table) {$table->softDeletes();});
        Schema::table('universidads', function (Blueprint $table) {$table->softDeletes();});
        Schema::table('archivos', function (Blueprint $table) {$table->softDeletes();});
        Schema::table('articulos', function (Blueprint $table) {$table->softDeletes();});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
