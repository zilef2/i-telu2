<?php

namespace App\Imports;

use App\helpers\Myhelp;
use App\Models\Carrera;
use App\Models\Universidad;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class CarreraImport implements
    ToModel,
    WithValidation,
    WithHeadingRow,
    WithUpserts,
    SkipsEmptyRows
{
    use Importable;

    private $UniversidadId;
    private $Universidad, $countfilas, $contarVacios, $contarNoNumeros, $contarRepetidos;

    public function uniqueBy()
    {
        return 'codigo';
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


    public function model(array $row)
    {
        try {
            session(['larow' => $row]); //saber en que fila sale un error fatal
            //# validaciones
            $enum = session('CountFilas') + 1;
            $carrera = Carrera::create([
                'universidad_id' => $this->UniversidadId,
                'nombre' => $row['carrera'],
                'codigo' => $row['codigo'],
                'enum' => $enum,

                'descripcion' => ''
            ]);
            return $carrera; //#fin
        } catch (\Throwable $th) {
            // Myhelp::EscribirEnLog($this, 'IMPORT:users', " Fallo dentro de la importacion de carreras: Universidad seleccionada" .
            //     $this->Universidad->id . " => " . $this->Universidad->nombre . " : Mesaje Error: " . $th->getMessage() . ' L:' . $th->getLine() .' Ubi:'.$th->getFile(), false);
        }
    }

    // public function headingRow(): int { return 2; } //no usar esta cosa
    public function rules(): array
    {
        return [
            'carrera' => [
                // 'required','sometimes',
                'string'
            ],
            'codigo' => [
                // 'required','sometimes',
                'string'
            ],
        ];
    }

    public function Requeridos($theRow)
    {
        foreach ($theRow as $value) {
            if (is_null($value) || $value === '')
                return false;
        }
        if (!is_string($theRow['codigo'])) return false;

        return true;
    }
}
