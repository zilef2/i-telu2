<?php

namespace App\Imports;

use App\helpers\Myhelp;
use App\Models\Universidad;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class PersonalUniversidadImport implements ToModel
{
    private $UniversidadId;
    private $Universidad,$countfilas,$contarVacios,$contarNoNumeros,$contarRepetidos;

    public function __construct($UniversidadId)
    {
        $this->UniversidadId = $UniversidadId;
        $this->Universidad = Universidad::findOrFail($this->UniversidadId);

        $this->countfilas = session('CountFilas',0);
        $this->contarVacios = session('contarVacios',0);
        $this->contarNoNumeros = session('contarNoNumeros',0);
        $this->contarRepetidos = session('contarRepetidos',0);
    }

    public function model(array $row) {
        try{
            session(['larow' => $row]);//saber en que fila sale un error fatal

            //# validaciones
            if(!$this->Requeridos($row)){
                session(['contarVacios' => $this->contarVacios++]);
                return null;
            } 
            
            if (strtolower(trim($row[0])) === 'identificacion' ) return null; //fila principal
            if (strtolower(trim($row[0])) == '') {//saltar 1 fila
                session(['contarVacios' => $this->contarVacios++]);
                return null;
            }

            if(!is_numeric($row[0])) {
                session(['contarNoNumeros' => $this->contarNoNumeros++]);
            }
               
            
            $user = User::Where('identificacion', $row[0])->first();
            if($user){
                $inscripcion = $user->universidades()->wherePivot('user_id',$user->id)->first();

                if(!$inscripcion){
                    session(['CountFilas' => $this->countfilas+1]);
                    
                    $response = $this->Universidad->users()->attach( $user );
                    return $response;
                }else{
                    session(['contarRepetidos' => $this->contarRepetidos++]);
                }

            } else {
                session(['contarVacios' => $this->contarVacios++]);
                return null;
            }

        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', " Fallo dentro de la importacion a universidad id: ".
             $this->Universidad->id ." => ". $this->Universidad->nombre ." : " . $th->getMessage(), false);
        }
    }

    public function Requeridos($theRow){
        foreach ($theRow as $value) {
            if(is_null($value) || $value === '')
                return false;
        }
        if(!is_int(intval($theRow[0]))) return false;

        return true;
    }
}
