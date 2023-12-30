<?php

namespace Database\Seeders;

use App\Models\Parametro;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $planes = ['Basico', 'Intermedio','Premium'];
        $planesCaducidad = ['Un mes', 'Dos meses','Tres meses'];
        $parametro = Parametro::find(3);
        $parametroValores = [
            $parametro->prompEjercicios,
            $parametro->prompObjetivos,
            $parametro->pMejoraContinua
        ];

        foreach ($planes as $key => $value) {
            Plan::updateOrInsert([
                'nombre' => $value,
                'tipo' => '',
                'valor' => $parametroValores[$key],
                'caducidad' => $planesCaducidad[$key],
                'caducidad_meses' => ($key+1),
                'tokens' => 30 * 6 * ($key+1), //6 preguntas por 30 dias
            ]);
        }

    }//php artisan db:seed --class=PlanSeeder

}
