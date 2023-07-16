<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class PersonalImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            $countfilas = session('CountFilas', 0);
            $contar1 = session('contar1', 0);
            $contar2 = session('contar2', -1);
            $contar3 = session('contar3', 0);
            $contar4 = session('contar4', 0);
            $contar5 = session('contar5', 0);
            $contarVacios = session('contarVacios', 0);

            session(['larow' => $row]);

            //# validaciones
            if (!$this->Requeridos($row)) {
                session(['contarVacios' => ++$contarVacios]);
                return null;
            }

            if (User::where('email', $row[1])->exists()) {
                session(['contar1' => ++$contar1]);
                return null;
            }
            if (User::where('identificacion', $row[2])->exists()) {
                session(['contar5' => ++$contar5]);
                return null;
            }

            if (strtolower(trim($row[0])) === 'nombre' || strtolower(trim($row[0])) == '') { //saltar 1 fila
                session(['contar2' => ++$contar2]);
                return null;
            }
            if (!is_numeric($row[2])) {
                session(['contar3' => ++$contar3]);
                return null;
            }
            if (strtolower($row[3]) != 'femenino' && strtolower($row[3]) != 'masculino' && strtolower($row[3]) != 'otro') {
                session(['contar4' => ++$contar4]);
                return null;
            }

            if (strtolower($row[7]) != 'trabajador' && strtolower($row[7]) != 'profesor') {
                session(['contar4' => ++$contar4]);
                return null;
            }
            //# fin validaciones

            //todo: si encuentra otro email, que actualize la info
            //todo: si encuentra con la misma identificacion, que actualize la info
            //todo: Primaria, bachillerato, tecnologia, profesional,especializacion,maestría,doctorado
            // if(strtolower($row[3]) != 'femenino' && strtolower($row[3]) != 'masculino' && strtolower($row[3]) != 'otro'){
            //     session(['contar4' => $contar4++]);
            //     return null;
            // }
            session(['CountFilas' => $countfilas + 1]);
            $fechaNacimiento = HelpExcel::getFechaExcel($row[4])->format('Y-m-d H:i');

            $user = new User([
                'name'     => $row[0],
                'email'    => $row[1],
                'identificacion' => intval($row[2]),
                'sexo' => $row[3],
                'fecha_nacimiento' => $fechaNacimiento,
                'semestre' => $row[5],
                'pgrado' => $row[6],

                'password' => Hash::make($row[2]),
            ]);
            // dd($fechaNacimiento,$row[7], strtolower(trim($row[0])) === 'nombre');
            $user->assignRole($row[7]);

            //todo: enviar correo a cada trabajador, que ha sido registrado

            return $user;
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo dentro de la importacion: ' . $th->getMessage(), false);
        }
    }

    public function Requeridos($theRow)
    {
        foreach ($theRow as $value) {
            if (is_null($value) || $value === '')
                return false;
        }
        if (!is_string($theRow[0])) return false;
        if (!is_int(intval($theRow[2]))) return false;

        return true;
    }
}


//todo: Operacion errada. Nombre del error: alejou error en la fila 0 SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1152194566' for key 'users_identificacion_unique' (SQL: insert into `users` (`name`, `email`, `identificacion`, `sexo`, `fecha_nacimiento`, `semestre`, `pgrado`, `password`, `updated_at`, `created_at`) values (alejou, emaildesde@excel.com, 1152194566, masculino, 2023-06-20 16:37, 10, universitario,