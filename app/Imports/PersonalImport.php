<?php

namespace App\Imports;

use App\helpers\HelpExcel;
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
    public function model(array $row) {
        // if (User::where('email', $row['correo'])->exists()) {
        $countfilas = session('CountFilas',0);
        if (User::where('email', $row[1])->exists()) {
            return null;
        }

        if (strtolower(trim($row[0])) == 'nombre') {
            return null;
        }
        if(!is_numeric($row[2])){
            return null;
        }
        if(strtolower($row[3]) != 'femenino' && strtolower($row[3]) != 'masculino'){
            return null;
        }

        //todo:poner contador de errores e imprimilos en la notificacion
        session(['CountFilas' => $countfilas+1]);
        $fechaNacimiento = HelpExcel::getFechaExcel($row[4]);

        return new User([
            'name'     => $row[0],
            'email'    => $row[1], 
            'identificacion' => $row[2],
            'sexo' => $row[3],
            'fecha_nacimiento' => $fechaNacimiento,
            'semestre' => $row[5],
            'pgrado' => $row[6],
    
            'password' => Hash::make($row[2]),
        ]);
    }
}
