<?php

// JUST THIS PROJECT
// STRING_s
// LARAVEL
// ESCRIBIRLOG
// VARIABLEINVARIABLE
// dates

namespace App\helpers;

use App\Models\Materia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Myhelp {

    //JUST THIS PROJECT
    public static function TokensPorPlan($plan){
        $valores = [
            0 => 100,
            1 => 200,
            2 => 300,
            3 => 400,
        ];
    }
    public static function AuthU()
    {
        $TheUser = Auth::user();
        if($TheUser){
            return $TheUser;
        }
        return redirect()->to('/');
    }

        public static function EstaVacio($elementos) {
            try {
                return $elementos->isEmpty();
            }catch(\Exception $e) {
                try {
                    return empty($elementos);
                } catch (\Exception $e) {
                    return false;
                }
            }
        }


    //END JUST THIS PROJECT

    public static function buscarMaterias($carrera_id_buscar) {
        return Materia::Where('carrera_id', (int)($carrera_id_buscar))
            ->Where('activa',1)
            ->get();
    }


    //STRING_s

        public function terminaEn($elString,$terminaEn) : bool {
            $longitudTermino = strlen($terminaEn);
            $finalDelString = substr($elString, -$longitudTermino);
            return $finalDelString === $terminaEn;
        }

        public function SePuedeConvertirAEntero($texto): int {
            $entero = (int)trim($texto);
            $respuesta = is_int($entero);

            if($respuesta){
                return $entero;
            }else{
                return -1;
            }

        }
        public function EncontrarEnString($frase, $busqueda): array {
            $Resultado = [];
            $NuevaPos = strlen($frase);
            $ResultOnce = strpos($frase, $busqueda);

            while ($ResultOnce !== false && $ResultOnce < $NuevaPos) {
                $Resultado[] = $ResultOnce;
                $NuevaPos = $ResultOnce + strlen($busqueda);
                $ResultOnce = strpos($frase, $busqueda, $NuevaPos);
            }
            return $Resultado;
        }

        static function  cortarFrase($frase, $maxPalabras = 3) {
            $noTerminales = [
                "de", "a", "para",
                "of", "by", "for"
            ];

            $palabras = explode(" ", $frase);
            $numPalabras = count($palabras);
            if ($numPalabras > $maxPalabras) {
                $offset = $maxPalabras - 1;
                while (in_array($palabras[$offset], $noTerminales) && $offset < $numPalabras) {
                    $offset++;
                }
                $ultimaPalabra = $palabras[$offset];
                if (((int)($ultimaPalabra)) != 0) {
                    session(['ultimaPalabra' => $ultimaPalabra]);
                }
                return implode(" ", array_slice($palabras, 0, $offset + 1));
            }
            return $frase;
        }

        /**
         * @param $Elarray
         * @param $limite
         * @return string
         */
        public static function ArrayInString($Elarray, $limite = 3): string
        {
            $Elarray = $Elarray->toArray();
            // dd($Elarray instanceof Collection);
            if (count($Elarray) > $limite) {
                $result = [];
                $result[] = $Elarray[0];
                $result[] = $Elarray[1];
                $result[] = $Elarray[2];

                $returningValue = implode(", ", $result)  . '...';
            } else {
                if (count($Elarray) > 0) {
                    $returningValue = implode(",", $Elarray);
                } else {
                    $returningValue = 'Sin resultados';
                }
            }
            return $returningValue;
        }

        public function ContarPalabras($texto){
            return count(explode(" ", $texto));
        }

    //fin strings






    //PERMISSIONS

    /**
     * @param $permissions
     * @return int
     */
    public static function getPermissionToNumber($permissions = null): int{
        if ($permissions === null)
            $permissions = auth()->user()->roles->pluck('name')[0];

        $ResultValue = 0;

        if ($permissions === 'estudiante') $ResultValue = 1;
        if ($permissions === 'profesor') $ResultValue = 2;
        if ($permissions === 'coordinador_de_programa') $ResultValue = 3;
        if ($permissions === 'coordinador_academico') $ResultValue = 4;
        if ($permissions === 'admin') $ResultValue = 9;
        if ($permissions === 'superadmin') $ResultValue = 10;
        return $ResultValue;
    }

    //ESCRIBIRLOG

    public static function EscribirEnLog($thiis, $clase = '', $mensaje = '', $returnPermission = true, $critico = false) {
        $permissions = $returnPermission ? auth()->user()->roles->pluck('name')[0] : null;
        $ListaControladoresYnombreClase = (explode('\\', get_class($thiis)));
        $nombreC = end($ListaControladoresYnombreClase);
        if (!$critico) {

            $Elpapa = (explode('\\', get_parent_class($thiis)));
            $nombreP = end($Elpapa);

            if ($permissions === 'admin' || $permissions === 'superadmin') {
                $ElMensaje = $mensaje != '' ? ' Mensaje: ' . $mensaje : '';
                Log::channel('soloadmin')->info('Vista:' . $nombreC . ' Padre: ' . $nombreP . '|  U:' . Auth::user()->name .' clase: '. $clase . $ElMensaje);
            } else {
                Log::info('Vista: ' . $nombreC . ' Padre: ' . $nombreP . ' |U:' . Auth::user()->name . ' ||' . $clase . '|| ' . ' Mensaje: ' . $mensaje);
            }
            return $permissions;
        }

        Log::critical('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||' . $clase . '|| ' . ' Mensaje: ' . $mensaje);
        return $permissions;
    }
    public function LogWithTrace($thiis,$throw,$mensaje = '') {
        $ListaControladoresYnombreClase = (explode('\\', get_class($thiis)));
        $nombreC = end($ListaControladoresYnombreClase);

        $problemon = [];
        $counter = 0;
        foreach ($throw->getTrace() as $item) {
            $problemon[]= $item['file'].' -- '.$item['line'];
            if($counter > 14) break;
        }
        if(count($problemon)) Log::critical("U -> " . Auth::user()->name .' Problemon: '. implode(',',$problemon));

        Log::alert("Alerta del problemon, " . $nombreC. ' problema = '. $mensaje);
    }
    public function EscribirEnLogJobs($thiis,$infoAlertError, $mensaje = '') {
        $ListaControladoresYnombreClase = (explode('\\', get_class($thiis)));
        $nombreC = end($ListaControladoresYnombreClase);

        $Elpapa = (explode('\\', get_parent_class($thiis)));
        $nombreP = end($Elpapa);

        if($infoAlertError === 0) Log::info('Vista: ' . $nombreC . ' Padre: ' . $nombreP . '|| ' . ' Mensaje: ' . $mensaje);
        if($infoAlertError === 1) Log::alert('Vista: ' . $nombreC . ' Padre: ' . $nombreP . '|| ' . ' Mensaje: ' . $mensaje);
        if($infoAlertError === 2) Log::error('Vista: ' . $nombreC . ' Padre: ' . $nombreP . '|| ' . ' Mensaje: ' . $mensaje);

    }

    //LARAVEL
        public function redirect($ruta, $seconds = 1) {
            sleep($seconds);
            return redirect()->to($ruta);
        }

        public function erroresExcel($errorFeo) {
            // $fila = session('ultimaPalabra');
            $error1 = "PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect date";
            if ($errorFeo == $error1) {
                return 'Existe una fecha invalida';
            }
            return 'Error desconocido';
        }

        public static function getPropertieAutoIncrement($modelName, $RequestPropertie,$StringPropertie,$stringHijo,$hijo) {
            if ($RequestPropertie) {
                return $RequestPropertie;
            } else {
                $modelInstance = resolve('App\\Models\\' . $modelName);
                $modelo = $modelInstance::Where($stringHijo, $hijo)->latest($StringPropertie)->first();
                if($modelo){
                    $result = (int)($modelo->$StringPropertie) + 1 ;
                    return $result;
                }else{
                    return 1;
                }

            }
        }
    //fin LARAVEL



    // VARIABLEINVARIABLE

    public function Vector_TurnInSelectID_AUTH($collections,$VectorGenero,$VectorNombreVariables = null){
        $aviso = true;
        foreach($collections as $key => $coll){
            $MuchosAMuchos = auth()->user()->{$coll}()->get();
            if($VectorNombreVariables === null){
                $variable = $coll;
            }else{
                $variable = $VectorNombreVariables[$key];
            }
            $aviso = $aviso && $MuchosAMuchos->count();
            $tieneRelacion = Myhelp::NEW_turnInSelectID($MuchosAMuchos, $VectorGenero[$key]);
            $resultado[$variable] = $tieneRelacion;
        }
        $resultado['aviso'] = $aviso;
        return $resultado;
    }
    public static function NEW_turnInSelectID($theArrayofStrings,$selecc,$theName = 'nombre') {
        if(self::EstaVacio($theArrayofStrings))
            return [
                [  'title' => 'No hay registros', 'value' => 0,]
                // 'filtro' => 'General'
            ];

        $result = [
            [
                'title' => 'Selecciona un'.$selecc, //todo: URGENTE, validar que no se vea feo
                'value' => 0,
                // 'filtro' => 'General'
            ]
        ];
        foreach ($theArrayofStrings as $value) {
            $result[] =
                [
                    'title' => $value->{$theName},
                    'value' => $value->id,
                    // 'filtro' => $value->teoricaOpractica
                ];
        }
        return $result;
    }



    public function GuardarInputSiTermina(Request $request): array{
        $input = $request->all();
        $finalInput = [];
        foreach ($input as $key => $value) {
            if(isset($value[0]) && $this->terminaEn($key,'correcciones'))
                $finalInput[$key] = $value[0];
        }
        return $finalInput;
    }

    public function GuardarInput(Request $request, $OwnUser,$vectorResumen = null): array{
        $input = $request->all();
        $finalInput = [];
        foreach ($input as $key => $value) {
            if(!isset($value[0])){
                if(is_string($value))
                    $finalInput[$key] = $value;
            }else{
                $finalInput[$key] = $value[0];
                if(isset($value[1])){
                    $finalInput[$key.'_ia'] = $value[1][0];
                    $finalInput[$key.'_final'] = $value[2];
                }
            }
        }
        if(isset($input['universidadid'])) { //es articulo

            $finalInput['universidad_id'] = $input['universidadid']['value'];
            $finalInput['carrera_id'] = $input['carreraid']['value'];
            $finalInput['materia_id'] = $input['materiaid']['value'];
            $finalInput['tipo'] = 'Articulo';
        }else{

            $finalInput['universidad_id'] = $vectorResumen[0];
            $finalInput['carrera_id'] =     $vectorResumen[1];
            $finalInput['materia_id'] =     $vectorResumen[2];
            $finalInput['tipo'] = 'SoloesunResumen';
        }
        $finalInput['user_id'] = $OwnUser->id;
        $finalInput['version'] = 1;

        return $finalInput;
    }

    //END VARIABLEINVARIABLE

    //the dates bro
        public function ValidarFecha($laFecha) {
            if (strtotime($laFecha)) {
                return $laFecha;
            }
            return '';
        }
    //fin the dates bro

}
