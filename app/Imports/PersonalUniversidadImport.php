<?php

namespace App\Imports;

use App\helpers\Myhelp;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Universidad;
use App\Models\User;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class PersonalUniversidadImport implements
    ToModel,
    WithValidation,
    WithHeadingRow,
    WithUpserts
{
    use Importable;

    private $UniversidadId;
    private $Universidad, $countfilas, $contarVacios, $contarNoNumeros, $contarRepetidos;

    // public function headingRow(): int { return 2; } //no usar esta cosa
    public function rules(): array
    {
        // usuario	universidad	carrera	materia
        return [
            'usuario' => ['required'],
            // 'universidad' => [ 'required', 'string'],
            'carrera' => ['required'],
            'materia' => ['required']
        ];
    }
    public function uniqueBy()  //WithUpserts
    {
        return 'usuario';
    }

    public function __construct($UniversidadId)
    {
        $this->UniversidadId = $UniversidadId;
        $this->Universidad = Universidad::findOrFail($this->UniversidadId);

        $this->countfilas = session('CountFilas', 0);
        $this->contarVacios = session('contarVacios', 0);
        $this->contarNoNumeros = session('contarNoNumeros', 0);
        $this->contarRepetidos = session('contarRepetidos', 0);
    }

    // public function onFailure(Failure ...$failures) {
    //     //todo: log
    // }

    public function model(array $row)
    {
        try {
            session(['larow' => $row]); //saber en que fila sale un error fatal

            //# validaciones
            if (!$this->Requeridos($row)) {
                session(['contarVacios' => $this->contarVacios++]);
                return null;
            }
            if (strtolower(trim($row['usuario'])) === 'identificacion') return null; //fila principal
            if (strtolower(trim($row['usuario'])) == '') { //saltar 1 fila
                session(['contarVacios' => $this->contarVacios++]);
                return null;
            }
            if (!is_numeric($row['usuario'])) {
                session(['contarNoNumeros' => $this->contarNoNumeros++]);
            }
            $user = User::Where('identificacion', $row['usuario'])->first();
            // dd($user);
            if ($user) {
                $inscripcionUni = $user->universidades()->wherePivot('user_id', $user->id)->first();
                if (!$inscripcionUni) {
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
                            return $user; //#fin
                        } else {
                            if ($inscripcionMateria->id == $materia->id) session(['contarRepetidos' => $this->contarRepetidos++]);
                        }
                    } else {
                        if ($inscripcionCarrera->id == $carrera->id) session(['contarRepetidos' => $this->contarRepetidos++]);
                    }
                } else {
                    if ($inscripcionUni->id == $this->Universidad->id) session(['contarRepetidos' => $this->contarRepetidos++]);
                }
            } else {
                //usuario no existe
                session(['contarVacios' => $this->contarVacios++]);
                return null;
            }
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', " Fallo dentro de la importacion a universidad id: " .
                $this->Universidad->id . " => " . $this->Universidad->nombre . " : " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile(), false);
        }
    }

    public function Requeridos($theRow)
    {
        foreach ($theRow as $value) {
            if (is_null($value) || $value === '')
                return false;
        }
        if (!is_int(intval($theRow['usuario']))) return false;

        return true;
    }
}
