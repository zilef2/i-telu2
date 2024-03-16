<?php

namespace App\Imports;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Parametro;
use App\Models\Universidad;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

//, SkipsEmptyRows
class PersonalImport implements ToModel, WithHeadingRow, WithUpsertColumns {
    use Importable;
    use RemembersRowNumber;

    public $contarEmailExistente, $contarActualizado, $contarNoNumeros, $contarSex,$contarCargo, $larow;
    public $parametro,$myHelp;

    public $countfilas;//numero de filas leidas (create)
    public $countfilasActualizadas;//numero de filas leidas (update)
    public $countfilasFallidas;

    public $universidadModel; //

    public $VectorUsuariosInscribirU, $VectorUsuariosInscribirCarrera, $VectorUsuariosInscribirMateria;
    public $CarrerasDelaUniversidad, $MateriasDelaUniversidad;

    public function __construct($universidadID) {
        $this->myHelp = new Myhelp();

        $this->universidadModel = Universidad::find($universidadID);
        $this->larow = [];

        $this->countfilas = 0;
        $this->countfilasActualizadas = 0;

        //validaciones
        $this->contarEmailExistente = []; //exite correo pero no identificacion igual
        $this->contarActualizado = []; // Usuario actualizado

        //validationForUser
        $this->contarNoNumeros = []; //identificacion no numerica
        $this->contarCargo = [];
        //not necesary
        $this->contarSex = []; //sexo distintio a   femenino masculino
        $this->contarSemestre = [];

        $this->parametro = Parametro::first();
        $this->VectorUsuariosInscribirU = [];
        $this->VectorUsuariosInscribirCarrera = [];
        $this->VectorUsuariosInscribirMateria = [];

        $this->CarrerasDelaUniversidad = $this->universidadModel->carreras()->pluck('id')->toArray();
        $this->MateriasDelaUniversidad = $this->universidadModel->materiasInscritas()->pluck('materias.id')->toArray();
    }
    public function upsertColumns(){return ['correo', 'identificacion'];}//WithUpserts

    private function validationForMajor($row,&$Programa,&$Asignatura){
        $pasoLaValidacionAsignatura = true;
        $Programa = trim($row['programa']);
        $Asignatura = trim($row['asignatura']);
        $ModelPrograma = Carrera::Where('codigo',$Programa);
        $pasoLaValidacionCarrera = $ModelPrograma->exists();
        //todo: contar los usuarios que tengan carrera o materia mal escrita.

        if($pasoLaValidacionCarrera){
            $ModelPrograma = $ModelPrograma->first();
            $ModelAsignatura = Materia::Where('codigo',$Asignatura);
            if (!$ModelAsignatura->exists()) {
                $pasoLaValidacionAsignatura = false;
            }
            $ModelAsignatura = $ModelAsignatura->first();
        }

        return[
            'ModelPrograma' => $ModelPrograma,
            'ModelAsignatura' => $ModelAsignatura ?? null,
            'pasoLaValidacionCarrera' => $pasoLaValidacionCarrera,
            'pasoLaValidacionAsignatura' => $pasoLaValidacionAsignatura,
        ];
    }
    private function validationForUser($row,&$identificacio, &$rowSemestre,&$rowSexo,&$elcargo){
        $Validacionidentificacion = true;
        $ValidacionCargo = true;

        $identificacio = trim($row['identificacion']);
        if (!is_numeric($identificacio)) {
            $this->contarNoNumeros[] = $identificacio . '. Fila '.$this->getRowNumber();

            $Validacionidentificacion = false;
        }
        $elcargo = strtolower(trim($row['cargo']));
        if ($elcargo !== 'estudiante' && $elcargo !== 'profesor') {
            $this->contarCargo[] = 'Error en la fila '.$this->getRowNumber().': '.$elcargo . ':(cargo)';
            $ValidacionCargo = false;
        }

        //validation below, are not necesary
        $rowSexo = strtolower(trim($row['sexo']));
        if ($rowSexo !== 'femenino' && $rowSexo !== 'masculino' && $rowSexo !== 'otro') {
            //            $this->contarSex[] = $row['sexo'] . 'No es valido (feminino, masculino, otro). Fila '.$this->getRowNumber();
            //            $pasoLaValidacion = false;
            $rowSexo = '';
            $this->contarSex[] = 'Error en la fila '.$this->getRowNumber().': '.$rowSexo.':(sexo)';
        }else{
            $rowSexo = ucfirst($rowSexo);
        }
        $isnumericSemestre = is_numeric((int)(trim($row['semestre'])));
        if($row['semestre'] == '' || $row['semestre'] == null || !$isnumericSemestre){
            $rowSemestre = 1;
        }else{
            if($isnumericSemestre) $rowSemestre = (int)(trim($row['semestre']));
        }

        return [
            'Validacionidentificacion' => $Validacionidentificacion,
            'ValidacionCargo' => $ValidacionCargo
        ];
    }


    public function model(array $row){
        try {
            // foreach ($rows as $key => $row) {
            $this->larow = $row;
            $fechaNacimiento = HelpExcel::getFechaExcel($row['fecha_de_nacimiento'])->format('Y-m-d H:i');
            $NumTickesDefecto = $this->parametro->NumeroTicketDefecto;
            $elUsuario = null;

            //validaciones
            $VectorPasoLaValidacion = $this->validationForUser($row,$identificacio,$rowSemestre,$rowSexo,$elcargo);
            $VectorValidacionCarMat = $this->validationForMajor($row,$Programa,$Asignatura);
            $ModelPrograma = $VectorValidacionCarMat['ModelPrograma'];
            $ModelAsignatura = $VectorValidacionCarMat['ModelAsignatura'];
            //fin validaciones

            $ValCarrera = $VectorValidacionCarMat['pasoLaValidacionCarrera'];
            $ValAsignatura = $VectorValidacionCarMat['pasoLaValidacionAsignatura'];
            $Validacionidentificacion = $VectorPasoLaValidacion["Validacionidentificacion"];
            $ValidacionCargo = $VectorPasoLaValidacion["ValidacionCargo"];
//            dd(
                //                  $ValCarrera,
                    //$ValAsignatura,
                    //$Validacionidentificacion,
                    //$ValidacionCargo,
//            );//quitar cuando muestre mensaje para estas validaciones

            //todo: falta actualizar nivel y cargo
            //todo: nivel -> Primaria, bachillerato, tecnologia, profesional,especializacion,maestría,doctorado
            //todo: enviar correo a cada estudiante, que ha sido registrado
            if($ValCarrera && $ValAsignatura && $Validacionidentificacion && $ValidacionCargo){
                $elCorreo = trim($row['correo']);
                $queryIdentificacion = User::where('identificacion', $identificacio);
                $queryEmail = User::where('email', $elCorreo);


                //4 opciones
                if(!$queryIdentificacion->exists() && $queryEmail->exists()){
                    //se borra al user fue error humano
                    $elUsuario = $queryEmail->first();
                    $elUsuario->forceDelete();
                }

                if($queryIdentificacion->exists()) {
                    if(!$queryEmail->exists()){
                        //se borra al user fue error humano
                        $elUsuario = $queryIdentificacion->first();
                        $elUsuario->forceDelete();
                    }

                    $elUsuario = $queryIdentificacion->first();
                    $elUsuario->update([
                        'name'     => $row['nombre'],
                        'email'    => $elCorreo,
                        'sexo' => $rowSexo,
                        'fecha_nacimiento' => $fechaNacimiento,
                        'semestre' => $rowSemestre,
                    ]);
                    $this->countfilasActualizadas++;
                }
                if(!$queryIdentificacion->exists() && !$queryEmail->exists()){
                    $elUsuario = User::Create([
                        'email'    => $elCorreo,
                        'identificacion' => $identificacio,
                        'name'     => $row['nombre'],
                        'sexo' => ucfirst($row['sexo']),
                        'fecha_nacimiento' => $fechaNacimiento,
                        'semestre' => $row['semestre'],
                        'pgrado' => $row['nivel'],
                        'limite_token_general' => $NumTickesDefecto,
                        'limite_token_leccion' => $NumTickesDefecto,
                        'password' => Hash::make($row['identificacion'].'++'),
                        //'email_verified_at' => now(),
                    ]);

                    $elUsuario->assignRole($elcargo);
                    $this->countfilas++;
                    $this->universidadModel->users()->attach(
                        $elUsuario->id
                    );
                }

                if($elUsuario && $ModelPrograma && $ModelAsignatura){
                    $this->VectorUsuariosInscribirU[] = $elUsuario->id;
                    $this->VectorUsuariosInscribirCarrera[] = [
                        $ModelPrograma->id , $elUsuario->id,
                    ];
                    $this->VectorUsuariosInscribirMateria[] = [
                        $ModelAsignatura->id , $elUsuario->id
                    ];
                }
            }

            return null;
        }catch (QueryException $e) {
            $this->countfilasFallidas++;
            if($this->countfilasFallidas >= 20){
                throw new Exception('Demasiadas excepciones, mas de 100 usuarios con correo repetido');
            }

            $mensajeError = $this->myHelp->mensajesErrorBD($e,' PersonalImport ',0);
            Myhelp::EscribirEnLog($this, 'depends: '. $mensajeError);

        } catch (\Throwable $th) {
            $this->countfilasFallidas++;

            $Message = $th->getMessage();    $Line = $th->getLine();    $File = $th->getFile();    $Code = $th->getCode();
            $mensajeMiddle = "zilef_error: $Message. | Línea: $Line. | Archivo: $File. | Codigo: $Code";
            Myhelp::EscribirEnLog($this, ' notknown ', $mensajeMiddle, false,1);
        }
        return $elUsuario ?? null;
    }

    public function onFailure(Failure ...$failures) {
        foreach ($failures as $key => $value) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Failures: ' .$value->row(). ' '.$value->errors()[0] , false);
        }
    }
}
