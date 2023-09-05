<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\Parametro;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Spatie\Permission\Traits\HasRoles;

class PersonalImport implements
ToModel,
WithValidation,
WithHeadingRow,
WithUpserts,
SkipsEmptyRows
{
    use Importable,HasRoles;

    public $countfilas, $contarEmailExistente, $contar2, $contarNoNumeros, $contar4, $contar5, $contarVacios, $larow;
    public $parametro;

    public function __construct() {
        $this->countfilas = 0;
        $this->contarEmailExistente = 0;
        $this->contar2 = -1;
        $this->contarNoNumeros = 0;
        $this->contar4 = 0;
        $this->contar5 = 0;
        $this->contarVacios = 0;
        $this->parametro = Parametro::first();
    }

    public function uniqueBy() { return 'Correo'; }
    public function rules(): array
    {
        return [
            'nombre' => 'required',
        ];
    }
    public function model(array $row) {
        try {
            $this->larow = $row;
            //# validaciones
                if (User::where('email', $row['correo'])->exists()) {
                    $this->contarEmailExistente++;
                    return null;
                }

                if (User::where('identificacion', $row['identificacion'])->exists()) {
                    $this->contar5++;
                    return null;
                }

                if (strtolower(trim($row['nombre'])) === 'nombre' || strtolower(trim($row['nombre'])) == '') { //saltar 1 fila
                    $this->contar2++;
                    return null;
                }
                if (!is_numeric($row['identificacion'])) {
                    $this->contarNoNumeros++;
                    return null;
                }
                if (strtolower($row['sexo']) != 'femenino' && strtolower($row['sexo']) != 'masculino' && strtolower($row['sexo']) != 'otro') {
                    $this->contar4++;
                    return null;
                }

                if (strtolower($row['cargo']) != 'estudiante' && strtolower($row['cargo']) != 'profesor') {
                    $this->contar4++;
                    return null;
                }
            //# fin validaciones

            //todo: si encuentra otro (email con la misma identificacion), que actualize la info
            //todo: si encuentra con la misma identificacion, que actualize la info
            //todo: Primaria, bachillerato, tecnologia, profesional,especializacion,maestría,doctorado
            // if(strtolower($row['Sexo']) != 'femenino' && strtolower($row['Sexo']) != 'masculino' && strtolower($row['Sexo']) != 'otro'){
            //     session(['contar4' => $contar4++]);
            //     return null;
            // }
            $fechaNacimiento = HelpExcel::getFechaExcel($row['fecha_de_nacimiento'])->format('Y-m-d H:i');
            $NumTickesDefecto = $this->parametro->NumeroTicketDefecto;
            
            $elUsuario = new User([
                'name'     => $row['nombre'],
                'email'    => $row['correo'],
                'identificacion' => intval($row['identificacion']),
                'sexo' => ucfirst($row['sexo']),
                'fecha_nacimiento' => $fechaNacimiento,
                'semestre' => $row['semestre'],
                'pgrado' => $row['nivel'],
                'limite_token_general' => $NumTickesDefecto,
                'limite_token_leccion' => $NumTickesDefecto,
                'password' => Hash::make($row['identificacion']),
                'email_verified_at' => now(),
            ]);
            // $user->assignRole('estudiante');
            $role = Role::where('name', 'estudiante')->first();
            // $elUsuario->assignRole($role);
            // $elUsuario->assignRole([$role->id]);
            // $elUsuario->syncRoles($role);
            $elUsuario->assignRole([$role->id]);
            // dd(
            //     $role,
            //     $elUsuario->getPermissionNames(),
            //     $elUsuario->permissions,
            // );
            $this->countfilas++;
            //todo: enviar correo a cada estudiante, que ha sido registrado

            return $elUsuario;
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo dentro de la importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile(), false);
            return null;
        }
    }
}


//todo: Operacion errada. Nombre del error: alejou error en la fila 0 SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1152194566' for key 'users_identificacion_unique' (SQL: insert into `users` (`name`, `email`, `identificacion`, `sexo`, `fecha_nacimiento`, `semestre`, `pgrado`, `password`, `updated_at`, `created_at`) values (alejou, emaildesde@excel.com, 1152194566, masculino, 2023-06-20 16:37, 10, universitario,