<?php

namespace App\helpers;

use DateTime;

// use Hamcrest\Type\IsInteger;

class HelpExcel{

    public static function GuardClauses_uploadestudiantes($lafecha,$inDate = false) {

    }
    public static function getFechaExcel($lafecha,$inDate = false) {
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
                    }
                }
            }
        }else{
            $fechaReturn = DateTime::createFromFormat('Y/m/d', $lafecha);
            if ($fechaReturn === false) {
                $fechaReturn = DateTime::createFromFormat('d/m/Y', $lafecha);
                if ($fechaReturn === false) {
                    throw new \Exception('Fecha inválida 2'.$lafecha);
                }
            }
        }

        if ($inDate) $returnar = $fechaReturn->format('Y-m-d');
        else $returnar = $fechaReturn;
        return $returnar;
    }

    public function validarArchivoExcel($elArchivo){
        $exten = $elArchivo->getClientOriginalExtension();
        $mensaje = '';
        // Validar que el archivo es de Excel
        if ($exten !== 'xlsx' && $exten !== 'xls') {
            $mensaje = 'El archivo debe ser de Excel';
            return $mensaje;
        }
        $pesoKilobyte = ((int)($elArchivo->getSize())) / (1024);
        if ($pesoKilobyte > (12*1024)) { //debe pesar menos de 12MB
            $mensaje = 'El archivo debe pesar menos de 12MB';
        }
        return $mensaje;
    }
}
