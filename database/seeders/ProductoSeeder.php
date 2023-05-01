<?php

namespace Database\Seeders;

use App\Models\CentroCosto;
use App\Models\CentroTrabajo;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{ public function run() {
    $randomNumber = rand(12,543210);
        Producto::create([
            'nombre' => 'Producto '.$randomNumber,
            'codigo' => $randomNumber,
            'precio' => 1000,
            'cantidad' => 5,
            'observaciones' => '',
            'centrotrabajo_id' => 1,
        ]);
}}