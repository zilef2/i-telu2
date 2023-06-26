<?php

namespace App\helpers;

use DateTime;

// use Hamcrest\Type\IsInteger;

class HelpExcel{

    public static function getFechaExcel($lafecha) {
        //the date fix
        if(is_numeric($lafecha)){ //toproof
            $unixDate = ($lafecha - 25568) * 86400;
            // $unixDate = ($lafecha - 25569) * 86400;
            $readableDate = date('Y/m/d', $unixDate);
            $fechaAprobacion = DateTime::createFromFormat('Y/m/d', $readableDate);

            if($fechaAprobacion === false){
                $fechaAprobacion = DateTime::createFromFormat('Y/m/d', $lafecha);
                if ($fechaAprobacion === false) {
                    $fechaAprobacion = DateTime::createFromFormat('d/m/Y', $lafecha);
                    if ($fechaAprobacion === false) {
                        throw new \Exception('Fecha inválida 1');
                        // throw new \Exception('Fecha inválida '.$lafecha. ' --++--');
                        return null;
                    }
                }
            }
        }else{
            $fechaAprobacion = DateTime::createFromFormat('Y/m/d', $lafecha);
            if ($fechaAprobacion === false) {
                $fechaAprobacion = DateTime::createFromFormat('d/m/Y', $lafecha);
                if ($fechaAprobacion === false) {
                    throw new \Exception('Fecha inválida 2'.$lafecha);
                    return null;
                }
            }
        }
    }
}