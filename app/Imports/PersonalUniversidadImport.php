<?php

namespace App\Imports;

use App\helpers\Myhelp;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Universidad;
use App\Models\User;

use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithUpserts;

//no usar nunca!!!
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PersonalUniversidadImport implements
    ToModel,
    WithUpserts,
    WithMapping
{
    use Importable;
    use RemembersRowNumber;

    private $UniversidadId;
    private $Universidad, $countfilas, $contarVacios, $contarNoNumeros, $contarRepetidos;

    public function __construct($UniversidadId){
        $this->UniversidadId = $UniversidadId;
        $this->Universidad = Universidad::findOrFail($this->UniversidadId);

        $this->countfilas = 0;


        $this->contarVacios = 0;
        $this->contarNoNumeros = 0;
        $this->contarRepetidos = 0;
    }

    /**
     * @return void
     */
    public function getCountfilas():int {return $this->countfilas;}
    public function getcontarVacios():int {return $this->contarVacios;}
    public function getcontarNoNumeros():int {return $this->contarNoNumeros;}
    public function getcontarRepetidos():int {return $this->contarRepetidos;}

    // public function headingRow(): int { return 2; } //no usar esta cosa

    public function uniqueBy(){ //WithUpserts
        return 'usuario';
    }

     public function onFailure(Failure ...$failures) {
         foreach ($failures->toArray() as $index => $error) {
             Myhelp::EscribirEnLog($this,'PersonalUniversidadImport', $error,false,true);
        }
     }

    public function map($row): array {
        return [
            'usuario' => $row[0],
            'carrera' => $row[1],
            'materia' => $row[2],
        ];
    }

    /**
     * @param  array  $row
     * @return User|null
     */
    public function model(array $row): User|null {
        try {
//            dd($row);
            session(['larow' => $row]); //saber en que fila sale un error fatal
            $user = null;
            $currentRowNumber = $this->getRowNumber();


            $noPasoValidacion1 = false;
            $noPasoValidacion2 = false;
            $noPasoValidacion3 = false;
            $noPasoValidacion4 = false;
            $noPasoValidacion5 = false;

            //<editor-fold desc="Validaciones">
            $noPasoValidacion = false;
            if (!$this->Requeridos($row)) {
                $this->contarVacios++;
                $noPasoValidacion = true;
                $noPasoValidacion1 = true;
            }
            $trimUser = strtolower(trim($row['usuario']));
            $valoresFilaInicial = ['usuario','identificacion','estudiante'];
            $valoresIncorrectos = ['  ',' ',''];
            if(!$noPasoValidacion && in_array($trimUser,$valoresFilaInicial)){
                $noPasoValidacion = true;
                $noPasoValidacion2 = true;
            }
            if(!$noPasoValidacion && in_array($trimUser,$valoresIncorrectos)){
                $this->contarVacios++;
                $noPasoValidacion = true;
                $noPasoValidacion3 = true;
            }
            if (!$noPasoValidacion && !is_numeric($row['usuario'])) {
                $this->contarNoNumeros++;
                $noPasoValidacion = true;
                $noPasoValidacion4 = true;
            }

            if(!$noPasoValidacion) {
                $user = User::Where('identificacion', $trimUser)->first();
                if (!$user) {
                    $this->contarVacios++;//todo: no es vacio, el usuario no existe
                    $noPasoValidacion = true;
                    $noPasoValidacion5 = true;
                }else{
                    $inscripcionUni = $user->universidades()->wherePivot('user_id', $user->id)->first();
                    if (!$inscripcionUni) {
                        if ($inscripcionUni->id == $this->Universidad->id)
                            $this->contarRepetidos++;
                    }
                }
            }

//            if($currentRowNumber > 1)
//                dd($row,
//                    $currentRowNumber,
//
//                $noPasoValidacion,
//                        $noPasoValidacion1,
//                $noPasoValidacion2,
//                $noPasoValidacion3,
//                $noPasoValidacion4,
//                    $noPasoValidacion5
//                );
            if($noPasoValidacion) return null;
            //hasta aqui las validaciones
            //</editor-fold>

            $this->Universidad->users()->attach($user);
            $inscripcionCarrera = $user->carreras()->wherePivot('user_id', $user->id)->first();
            $carrera = Carrera::Where('codigo', $row['carrera'])->first();
            if (!$inscripcionCarrera) {
                $carrera->users()->attach($user);

                $inscripcionMateria = $user->materias()->wherePivot('user_id', $user->id)->first();
                $materia = Materia::Where('codigo', $row['materia'])->first();
                if (!$inscripcionMateria) {
                    session(['CountFilas' => $this->countfilas + 1]);
                    $materia->users()->attach($user);
                    //fin
                } else {
                    if ($inscripcionMateria->id == $materia->id) $this->contarRepetidos++;
                }
            } else {
                if ($inscripcionCarrera->id == $carrera->id) $this->contarRepetidos++;
            }

        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', " Fallo dentro de la importacion a universidad id: " .
                $this->Universidad->id . " => " . $this->Universidad->nombre . " : " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile(), false);
        }
        return $user; //#fin

    }

    public function Requeridos($theRow){
        $returning = true;
        foreach ($theRow as $value) {
            if (is_null($value) || $value === ''){
                $returning = false;
                break;
            }
        }
        if ($returning && !is_int((int)($theRow['usuario']))) $returning = false;

        return $returning;
    }
}
