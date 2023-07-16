<?php

namespace App\helpers;

use DateTime;

// use Hamcrest\Type\IsInteger;

class HelpExcel{

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

        if ($inDate) {
            return $fechaReturn->format('Y-m-d');
        }else return $fechaReturn;
    }

    public function validarArchivoExcel($request){
        $exten = $request->archivo1->getClientOriginalExtension();
        // Validar que el archivo es de Excel
        if ($exten != 'xlsx' && $exten != 'xls') {
            return 'El archivo debe ser de Excel';
        }
        $pesoKilobyte = ((int)($request->archivo1->getSize())) / (1024);
        if ($pesoKilobyte > (12*1024)) { //debe pesar menos de 12MB
            return 'El archivo debe pesar menos de 12MB';
        }
        return '';
    }
}