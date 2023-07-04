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
            $fechaReturn = DateTime::createFromFormat('Y/m/d', $readableDate);

            if($fechaReturn === false){
                $fechaReturn = DateTime::createFromFormat('Y/m/d', $lafecha);
                if ($fechaReturn === false) {
                    $fechaReturn = DateTime::createFromFormat('d/m/Y', $lafecha);
                    if ($fechaReturn === false) {
                        throw new \Exception('Fecha inválida 1');
                        // throw new \Exception('Fecha inválida '.$lafecha. ' --++--');
                        return null;
                    }
                }
            }
        }else{
            $fechaReturn = DateTime::createFromFormat('Y/m/d', $lafecha);
            if ($fechaReturn === false) {
                $fechaReturn = DateTime::createFromFormat('d/m/Y', $lafecha);
                if ($fechaReturn === false) {
                    throw new \Exception('Fecha inválida 2'.$lafecha);
                    return null;
                }
            }
        }
        return $fechaReturn;
    }
}