<?php

namespace App\helpers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// use Hamcrest\Type\IsInteger;

//copis: 
// operario
// fecha
// orden de trabajo
// Centro de trabajo
// actividad
// disponibilidad
// hora inicial
// hora final
// cantidad
// Tiempo

class Myhelp{
    public static function CargosYModelos(){
        //otros cargos NO_ADMIN
        $nombresDeCargos = [
            'trabajador',
        ];
        $isSome = [];
        foreach ($nombresDeCargos as $key => $value) {
            $isSome[] = 'is'.$value;
        }
        //arrrays for easyway
        $Models = [
            'user',
            'role',
            'permission',
        ];
        return [
            'nombresDeCargos' => $nombresDeCargos,
            'Models' => $Models,
            'isSome' => $isSome,
        ];
    }

    public function redirect($ruta,$seconds =14)
    {
        sleep($seconds);
        return redirect()->to($ruta);
    }

    function cortarFrase($frase, $maxPalabras = 3) {
        $noTerminales = [
            "de","a","para",
            "of","by","for"
        ];

        $palabras = explode(" ", $frase);
        $numPalabras = count($palabras);
        if ($numPalabras > $maxPalabras) {
            $offset = $maxPalabras - 1;
            while (in_array($palabras[$offset], $noTerminales) && $offset < $numPalabras) {
                $offset++; 
            }
            $ultimaPalabra = $palabras[$offset];
            if((intval($ultimaPalabra)) != 0){
                session(['ultimaPalabra' => $ultimaPalabra]);
            }
            return implode(" ", array_slice($palabras, 0, $offset + 1));
        }
        return $frase;
    }
    public function erroresExcel($errorFeo){
        // $fila = session('ultimaPalabra');
        $error1 ="PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect date";
        if($errorFeo == $error1){
            return 'Existe una fecha invalida';
        }
        return 'Error desconocido';
    }
    public function ValidarFecha($laFecha){
        if(strtotime($laFecha)){
            return $laFecha;
        }
        return '';
    }

    public static function EscribirEnLog( $thiis, $clase = '',$mensaje='',$returnPermission = true,$critico = false) {
        $permissions = $returnPermission ? auth()->user()->roles->pluck('name')[0] : null;
        $ListaControladoresYnombreClase = (explode('\\', get_class($thiis))); $nombreC = end($ListaControladoresYnombreClase);
        if(!$critico){

            $Elpapa = (explode('\\', get_parent_class($thiis))); $nombreP = end($Elpapa);

            if($permissions == 'admin' || $permissions == 'superadmin') {
                $ElMensaje = $mensaje != '' ? ' Mensaje: '.$mensaje : '';
                Log::channel('soloadmin')->info('Vista:' . $nombreC.' Padre: '.$nombreP. '|  U:'.Auth::user()->name.$ElMensaje);
            } else {
                Log::info('Vista: ' . $nombreC.' Padre: '.$nombreP. 'U:'.Auth::user()->name. ' ||'.$clase.'|| '.' Mensaje: '.$mensaje); 
            }
            return $permissions;
        }else{
            Log::critical('Vista: ' . $nombreC.'U:'.Auth::user()->name. ' ||'.$clase.'|| '.' Mensaje: '.$mensaje); 
        }
    }

    public static function ArrayInString($Elarray,$limite = 3) {
        $Elarray = $Elarray->toArray();
        // dd($Elarray instanceof Collection);
        if(count($Elarray) > $limite){
            $result= [];
            $result[] = $Elarray[0];
            $result[] = $Elarray[1];
            $result[] = $Elarray[2];

            return implode(", ",$result)  . '...';
        }else{
            if(count($Elarray) > 0) {
                return implode(",",$Elarray);
            }else{
                return 'Sin resultados';
            }
        }
    }

    public static function getPermissionToNumber($permissions) {

        if($permissions == 'trabajador') return 1;
        if($permissions == 'profesor') return 2;
        if($permissions == 'coordinador_academico') return 3;
        if($permissions == 'coordinador_de_programa') return 4;
        if($permissions == 'admin') return 5;
        if($permissions == 'superadmin') return 6;
        return 0;
    }

}
