<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\Parametro;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class PersonalImport implements ToModel, WithValidation, WithHeadingRow, WithUpserts, SkipsEmptyRows, SkipsOnFailure {
    use Importable;

    public $contarEmailExistente, $contarActualizado, $contarNoNumeros, $contarSex,$contarCargo, $larow;
    public $parametro;
    public $CorreoYIdentificacionIgual; //notused
    public $contarVacios; //notused
    public $filaExcel;//numero de filas en excel
    public $countfilas;//numero de filas leidas (create )
    public $countfilasActualizadas;//numero de filas leidas (update)

    public function __construct() {
        $this->larow = [];

        $this->countfilas = 0;
        $this->countfilasActualizadas = 0;
        $this->filaExcel = 0;
        $this->contarEmailExistente = []; //exite correo pero no identificacion igual
        $this->contarActualizado = []; // Usuario actualizado
        $this->contarNoNumeros = [];
        $this->contarSex = []; //sexo distintio a   femenino masculino
        $this->contarCargo = []; 

        $this->parametro = Parametro::first();
    }
    public function chunkSize(): int {
        return 12;
    }

    public function uniqueBy() { return 'correo'; }
    public function rules(): array {
        return [
            'Nombre' => 'required',
            // 'identificacion' => 'required',
            // 'correo' => 'required'
        ];
    }

    public function model(array $row){
        try {
            // foreach ($rows as $key => $row) {
                
                $this->filaExcel++;
                $this->larow = $row;
                $fechaNacimiento = HelpExcel::getFechaExcel($row['fecha_de_nacimiento'])->format('Y-m-d H:i');
                $NumTickesDefecto = $this->parametro->NumeroTicketDefecto;


                //# validaciones
                if (!is_numeric(intval(trim($row['identificacion'])))) {
                    $this->contarNoNumeros[] = $row['identificacion'] . ' fila '.$this->filaExcel;
                    return null;
                }
                if (strtolower($row['sexo']) != 'femenino' && strtolower($row['sexo']) != 'masculino' && strtolower($row['sexo']) != 'otro') {
                    $this->contarSex = $row['sexo'] . ' fila '.$this->filaExcel;
                    return null;
                }

                if (strtolower($row['cargo']) != 'estudiante' && strtolower($row['cargo']) != 'profesor') {
                    $this->contarCargo[] = $row['cargo'] . ' fila '.$this->filaExcel;
                    return null;
                }


                //# si tiene Email y correo existentes, actualizar
                $queryEmail = User::where('email', $row['correo']);
                    if ($queryEmail->exists()) {
                        $contarCorreo = $queryEmail->first();
                        $query = User::where('identificacion', $row['identificacion']);
                        $user = $query->first();
                        if ($query->exists() && $contarCorreo->email == $user->email) {
                            //validar que no exista mas de uno asi
                            $this->CorreoYIdentificacionIgual[] = $row['nombre'] . ', '. $row['correo'];

                            $user->update([
                                'name'     => $row['nombre'],
                                'email'    => $row['correo'],
                                'identificacion' => intval($row['identificacion']),
                                'sexo' => ucfirst($row['sexo']),
                                'fecha_nacimiento' => $fechaNacimiento,
                                'semestre' => $row['semestre'],
                                // 'pgrado' => $row['nivel'],
                                // 'limite_token_general' => $NumTickesDefecto,
                                // 'limite_token_leccion' => $NumTickesDefecto,
                                // 'email_verified_at' => now(),
                            ]);
                            $this->countfilasActualizadas++;

                            return $user;
                        }
                        
                        $this->contarEmailExistente[] = $row['correo']. ' fila '.$this->filaExcel;
                        return null;
                    }


                  
                //# fin validaciones
                // dd($row);

                //todo: si encuentra otro (email con la misma identificacion), que actualize la info
                //todo: si encuentra con la misma identificacion, que actualize la info
                //todo: Primaria, bachillerato, tecnologia, profesional,especializacion,maestría,doctorado
                // if(strtolower($row['Sexo']) != 'femenino' && strtolower($row['Sexo']) != 'masculino' && strtolower($row['Sexo']) != 'otro'){
                //     session(['contarSex' => $contarSex++]);
                //     return null;
                // }
                
                $elUsuario = User::create([
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
                $elUsuario->assignRole('estudiante');
                $this->countfilas++;
                //todo: enviar correo a cada estudiante, que ha sido registrado
            // }

        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo dentro de la importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile(), false);
            return null;
        }
    }
    public function onFailure(Failure ...$failures) {
        foreach ($failures as $key => $value) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Failures: ' .$value->row(). ' '.$value->errors()[0] , false);
        }
    }
}


//todo: Operacion errada. Nombre del error: alejou error en la fila 0 SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1152194566' for key 'users_identificacion_unique' (SQL: insert into `users` (`name`, `email`, `identificacion`, `sexo`, `fecha_nacimiento`, `semestre`, `pgrado`, `password`, `updated_at`, `created_at`) values (alejou, emaildesde@excel.com, 1152194566, masculino, 2023-06-20 16:37, 10, universitario,