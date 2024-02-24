<?php

namespace App\Http\Controllers;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Imports\CarreraImport;
use App\Imports\PersonalImport;
use App\Imports\PersonalUniversidadImport;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Permission;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Universidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Middleware\ErrorHandlerMiddleware;
use Maatwebsite\Excel\Validators\ValidationException;

class UserController extends Controller
{

    public function __construct(){
        $this->middleware('permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('permission:read user', ['only' => ['index', 'show']]);
        $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy', 'destroyBulk']]);
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(UserIndexRequest $request){
        $permissions = Myhelp::EscribirEnLog($this, ' users');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $users = User::query();
        if ($request->has('search')) {
            $users->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('email', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                    ;
            })
            ->where('name', '!=', 'admin')
            ->where('name', '!=', 'Superadmin');
            // $users->where('name', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $users = $users->orderBy($request->field, $request->order);
        }else{
            $users = $users->orderBy('updated_at', 'Desc');
        }

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $role = auth()->user()->roles->pluck('name')[0];
        $roles = Role::get();
        if ($role !== 'superadmin') {
            $users->whereHas('roles', function ($query) {
                return $query->where('name', '<>', 'superadmin');
            });
            $roles = Role::where('name', '<>', 'superadmin')->where('name', '<>', 'admin')->get();
        }

        //        $planes = Myhelp::NEW_turnInSelectID(Plan::all(),'plan');
        return Inertia::render('User/Index', [
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
            'title'         => __('app.label.user'),
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'users'         => $users->with('roles')->paginate($perPage),
            'roles'         => $roles,
            'planes'        => Plan::all(),
            'numberPermissions' => $numberPermissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    //public function create()    {    }


    //! STORE - UPDATE - DELETE
    //! STORE functions
    public function updatingDate($date)
    {
        if ($date === null || $date === '1969-12-31') {
            return null;
        }
        return date("Y-m-d", strtotime($date));
    }

    public function store(UserStoreRequest $request){
        Myhelp::EscribirEnLog($this, 'STORE:users');
        $typeReturn = 'success';

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'identificacion' => $request->identificacion,
                'sexo' => $request->sexo == 0 ? 'Masculino' : 'Femenino',
                'fecha_nacimiento' => $this->updatingDate($request->fecha_nacimiento),
                'semestre' => $request->semestre,
                'semestre_mas_bajo' => $request->semestre_mas_bajo,
                'limite_token_general' => 3,
                'limite_token_leccion' => $request->limite_token_leccion,
                'pgrado' => $request->pgrado,
            ]);
            $user->assignRole($request->role);
            $finalMessage = __('app.label.created_successfully', ['name' => $user->name]);
            if($request->role === 'estudiante_independiente'){
                $ugenerica = Universidad::Where('nombre','like','Intelu')->first();//hardcode intelu
                if($ugenerica){
                    $user->universidades()->attach($ugenerica->id);
                    $ArrayCarrerasGen = Carrera::where('universidad_id',$ugenerica->id)->get();
                    foreach ($ArrayCarrerasGen as $index => $carrera) {
                        $user->carreras()->attach($carrera->id);
                    }
                }else{
                    $typeReturn = 'error';
                    $finalMessage = __('app.label.created_error', ['name' => __('app.label.user')]) . ' La universidad intelu no existe';
                }
            }

            if($typeReturn !== 'error'){
                DB::commit();
                Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' guardado', false);
            }else{
                Myhelp::EscribirEnLog($this, 'Error:stor:users |'.$finalMessage, false);
                DB::rollback();
            }

            return back()->with($typeReturn, $finalMessage);
        } catch (\Throwable $th) {
            DB::rollback();
            if (isset($user)) Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario id:' . $user->id ?? 'x' . ' | ' . $user->name ?? 'x' . ' fallo en el guardado', false);
            else Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario desconocido, fallo en el guardado', false);
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.user')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
    //fin store functions
    public function show($id){}public function edit($id){}

    public function update(UserUpdateRequest $request, $id){
        Myhelp::EscribirEnLog($this, 'UP:users', '', false);
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                // 'password'  => $request->password ? Hash::make($request->password) : $user->password,

                'identificacion' => $request->identificacion,
                'sexo' => $request->sexo,
                // 'sexo' => $request->sexo == 0 ? 'Masculino' : 'Femenino',
                'fecha_nacimiento' => $this->updatingDate($request->fecha_nacimiento),
                'semestre' => $request->semestre,
                'semestre_mas_bajo' => $request->semestre_mas_bajo,
                'limite_token_general' => $request->limite_token_general,
                'limite_token_leccion' => $request->limite_token_leccion,
            ]);
            $user->syncRoles($request->role);
            DB::commit();
            Myhelp::EscribirEnLog($this, 'UPDATE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' actualizado', false);

            return back()->with('success', __('app.label.updated_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'UPDATE:users', 'usuario id:' . $user->id . ' | ' . $user->name . '  fallo en el actualizado', false);
            return back()->with('error', __('app.label.updated_error', ['name' => $user->name]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function updatePlan(Request $request, $userid){
        Myhelp::EscribirEnLog($this, 'UpdatePlan:users', '', false);
        DB::beginTransaction();
        try {
            $user = User::findOrFail($userid);
            $elplan = Plan::find($request->plan_id);
            $vence = Myhelp::TextoDateTime($elplan->caducidad_meses);
            $nuevosTokens = $user->limite_token_leccion + $elplan->tokens;
            $user->update([
                'limite_token_leccion' => $nuevosTokens,
                'plan_id' => $request->plan_id,
                'planVence' => $vence,
            ]);

            DB::commit();
            Myhelp::EscribirEnLog($this, 'UpdatePlan:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' actualizado', false);

            return back()->with('success', __('app.label.updated_successfully_plan_user',
                ['name' => $user->name,'tokens' => $elplan->tokens])
            );

        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'UPDATE:users', 'usuario id:' . $user->id . ' | ' . $user->name . '  fallo en el actualizado', false);
            return back()->with('error', __('app.label.updated_error', ['name' => $user->name]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function destroy(User $user){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:users');
        try {
            $user->delete();
            Myhelp::EscribirEnLog($this, 'DELETE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' borrado', false);
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'DELETE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' fallo en el borrado:: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile(), false);
            return back()->with('error', __('app.label.deleted_error', ['name' => $user->name]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $user = User::whereIn('id', $request->id);
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
    //FIN : STORE - UPDATE - DELETE

    public function subirexceles(){
        $permissions = Myhelp::EscribirEnLog($this, ' materia');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        return Inertia::render('User/subirExceles', [
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
            'title'         => __('app.label.user'),
            'numUsuarios'   => count(User::all()) - 1,
            'UniversidadSelect'   => Universidad::all(),
            'numberPermissions'   => $numberPermissions
            // 'users'         => $users->with('roles')->paginate($perPage),
        ]);
    }


    private function MensajeWar($errores) {
        $bandera = false;
        $contares = [
            'contarEmailExistente',
            'contarActualizado',
            'contarNoNumeros',
            'contarSex',
            'contarCargo'
        ];
        $mensajesWarnings = [
            '# Correos Existentes: ',
            'Usuarios actualizados: ',
            '# Cedulas no numericas: ',
            '# Generos mal escrito: ',
            '# Cargo mal escrito: ',
        ];

        //! errores y contares tienen que tener la misma longuitud
        foreach ($errores as $key => $elError) {
            foreach ($elError as $value) {

                if(isset(${$contares[$key]})){
                    ${$contares[$key]} .= $value . '.<br> ';
                }else{
                    ${$contares[$key]} = '<br>'.$value . '.<br> ';
                }
                $bandera = $bandera || $value > 0;
            }
        }
        //en ese caso, imprimimos el valor en $mensajesWarnings
        $mensaje = '';
        if ($bandera) {
            foreach ($mensajesWarnings as $key => $value) {
                if (isset(${$contares[$key]})) {
                    $mensaje .= $value . ${$contares[$key]} . '. ';
                }
            }
        }
        return $mensaje;
    }
    private function inscribir($universidadID,
                               $VectorUsuariosInscribirU,
                               $VectorUsuariosInscribirCarrera,
                               $VectorUsuariosInscribirMateria
    ){
        $ModelUniversidad = Universidad::find($universidadID);


        foreach ($VectorUsuariosInscribirU as $userid) {
            $ModelUniversidad->users()->sync($userid);
        }

        foreach ($VectorUsuariosInscribirCarrera as $Carrera_Userid) {
            $ModelCarrera = Carrera::find($Carrera_Userid[0]);
            $ModelCarrera->users()->sync($Carrera_Userid[1]);
        }

        foreach ($VectorUsuariosInscribirMateria as $Materia_Userid) {
            $ModelMateria = Materia::find($Materia_Userid[0]);
            $ModelMateria->users()->sync($Materia_Userid[1]);
        }
    }
    public function uploadestudiantes(Request $request): \Illuminate\Http\RedirectResponse{
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar', false);
        $countfilas = 0;
        $typeBack = 'success';

        try {
            if ($request->archivo1 && $request->universidadID_Estudiantes) {
                $universidadID = (int)$request->universidadID_Estudiantes;
                $personalImp = new PersonalImport($universidadID);

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request->archivo1);
                if ($mensageWarning != ''){
                    $typeBack = 'warning';
                    $FinalMessage = $mensageWarning;
                }else{

                    // $result =
                    Excel::import($personalImp, $request->archivo1);
    //                dd($personalImp->VectorUsuariosInscribirU);
                    $this->inscribir($universidadID,
                        $personalImp->VectorUsuariosInscribirU, //inscribir en u, solo viene el id del user
                        $personalImp->VectorUsuariosInscribirCarrera, //Carrera => userid
                        $personalImp->VectorUsuariosInscribirMateria //materia => userid
                    );
                    $misErrores = [
                        $personalImp->contarEmailExistente,
                        $personalImp->contarActualizado,
                        $personalImp->contarNoNumeros,
                        $personalImp->contarSex,
                        $personalImp->contarCargo,
                    ];
                    $MensajeWarning = $this->MensajeWar($misErrores);
                    if ($MensajeWarning === '') {
                        $FinalMessage = 'Usuarios nuevos: ' . $personalImp->countfilas."<br>"
                            .' Usuarios actualizados: ' . $personalImp->countfilasActualizadas."<br>"
                            .' '.$MensajeWarning;
    //                        ->with('warning', $MensajeWarning);
                    }else{
                        $typeBack = 'warning';
                        $FinalMessage = 'Usuarios nuevos: ' . $personalImp->countfilas."<br>"
                            .' Usuarios actualizados: ' . $personalImp->countfilasActualizadas."<br>"
                            .' '.$MensajeWarning;

                    }

                    Myhelp::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                    DB::commit();

                }
            } else {
                $typeBack = 'error';
                $FinalMessage = __('app.label.op_not_successfully') . ' archivo no seleccionado';
            }

            return back()->with($typeBack, $FinalMessage);
        } catch (\Throwable $th) {
            DB::rollback();
            $mensajeError= $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            Myhelp::EscribirEnLog($this, 'IMPORT:users', $mensajeError, false);

            $larowNull = $personalImp->getRowNumber() ?? 'Sin info de la fila';
            $larowNull = 'Fila: '.$larowNull;
            return back()->with('error', __('app.label.op_not_successfully')
                . $larowNull
                . $mensajeError);
        }
    }


    //mio
    public function uploadUniversidad_notusing(Request $request) {
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar alumnos de universidades', false);
        $countfilas = 0;
        try {
            if ($request->archivo1 && $request->universidadID) {
                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request->archivo1);
                if ($mensageWarning != '') return back()->with('warning', $mensageWarning);

                Excel::import(new PersonalUniversidadImport($request->universidadID), $request->archivo1);
                $countfilas = session('CountFilas', 0);
                $contarVacios = session('contarVacios', 0);
                $contarNoNumeros = session('contarNoNumeros', 0);

                session(['CountFilas' => 0]);
                session(['contarVacios' => 0]);
                session(['contarNoNumeros' => 0]);

                $HuboWarning = $contarVacios > 0 || $contarNoNumeros > 0;
                if ($HuboWarning) {
                    $men1 = $contarNoNumeros > 0 ? '#filas con identifiaciones no validas ' . $contarNoNumeros : '';
                    $men5 = $contarVacios > 0 ? '#filas con celdas vacias ' . $contarVacios : '';
                    $MensajeWarning = $men1 . $men5;
                    return back()
                        ->with('success', 'Usuarios nuevos: ' . $countfilas)
                        ->with('warning', $MensajeWarning);
                }

                Myhelp::EscribirEnLog($this, 'IMPORT:uploadUniversida', ' finalizo con exito', false);
                if ($countfilas == 0)
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                else
                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
            } else {
                return back()->with('error', __('app.label.op_not_successfully') . ' archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile(), false);
            return back()->with('error', __('app.label.op_not_successfully') . 'Nombre del error: ' . session('larow')[0] . ' error en la fila ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }


    public function uploadUniversidad(Request $request) {
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar alumnos de universidades', false);
        $countfilas = 0;
        $typeReturn = 'success';
        try {
            if ($request->archivo2_matricular && $request->universidadID) {
                $helpExcel = new HelpExcel();
                $mensageReturn = $helpExcel->validarArchivoExcel($request->archivo2_matricular);

                if ($mensageReturn != '') {
                    $typeReturn = 'warning';
                }else {
                    $importarUsuarios = new PersonalUniversidadImport($request->universidadID);
                    Excel::import($importarUsuarios, $request->archivo2_matricular);
                    $countfilas = $importarUsuarios->getCountfilas();
                    $contarVacios = $importarUsuarios->getcontarVacios();
                    $contarNoNumeros = $importarUsuarios->getcontarNoNumeros();
                    $contarRepetidos = $importarUsuarios->getcontarRepetidos();//todo: mejorar el mensaje
                    $HuboWarning = $contarVacios > 0 || $contarNoNumeros > 0 || $contarRepetidos > 0;

                    if ($HuboWarning) {
                        $menSuccess = 'Usuarios nuevos: ' . $countfilas.'.';
                        $men1 = $contarNoNumeros > 0 ? ' #filas con identifiaciones no validas ' . $contarNoNumeros : '';
                        $men6 = $contarRepetidos > 0 ? ' #filas con usuarios no inscritos a universidades o carreras ' . $contarRepetidos : '';
                        $men5 = $contarVacios > 0 ? ' #filas con celdas vacias ' . $contarVacios : '';

                        $typeReturn = 'warning';
                        $mensageReturn = $menSuccess . $men1 . $men5 . $men6;
//                        return back()->with('warning', $MensajeWarning);
                    }else{
                        Myhelp::EscribirEnLog($this, 'IMPORT:uploadUniversida', ' finalizo con exito', false);
                        if ($countfilas == 0){
                            $mensageReturn = __('app.label.op_successfully') . ' No hubo cambios';
                        } else{
                            $mensageReturn = __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito';
                        }
                    }
                }
            } else {
                $typeReturn = 'error';
                $mensageReturn = __('app.label.op_not_successfully') . ' Archivo no seleccionado';
            }
        } catch (\Throwable $th) {
            $mensajeth = ' | Mensaje: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion de uploadUniversidad: ' . $mensajeth, false);
//            return back()->with('error', )
            $typeReturn = 'error';
            $mensageReturn = __('app.label.op_not_successfully'. ' error en la fila ' . $countfilas . $mensajeth);
        }
        return back()->with($typeReturn, $mensageReturn);
    }


    public function uploadCarreras(Request $request) {
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar Carreras', false);
        $countfilas = 0;
        try {
            if ($request->archivo_componente_carreras && $request->universidadID) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request->archivo_componente_carreras);
                if ($mensageWarning != ''){
                    $ElWith = 'warning';
                    $MensajeFinal = $mensageWarning;
                } else{

                    Excel::import(new CarreraImport($request->universidadID), $request->archivo_componente_carreras);

                    $countfilas = session('CountFilas', 0);
                    $contarVacios = session('contarVacios', 0);
                    $contarNoNumeros = session('contarNoNumeros', 0);

                    session(['CountFilas' => 0]);
                    session(['contarVacios' => 0]);
                    session(['contarNoNumeros' => 0]);

                    $HuboWarning = $contarVacios > 0 || $contarNoNumeros > 0;
                    if ($HuboWarning) {
                        $men1 = $contarNoNumeros > 0 ? '#filas con identifiaciones no validas ' . $contarNoNumeros : '';
                        $men5 = $contarVacios > 0 ? '#filas con celdas vacias ' . $contarVacios : '';
                        $MensajeFinal = 'Usuarios nuevos: ' . $countfilas. '. '.$men1 . $men5;
                        $ElWith = 'warning';
                    }else{
                        Myhelp::EscribirEnLog($this, 'IMPORT:uploadUniversida', ' finalizo con exito', false);
                        if ($countfilas == 0){
                            $MensajeFinal = ' No hubo cambios';
                        }else{
                            $MensajeFinal = ' Se leyeron ' . $countfilas . ' filas con exito';
                        }
                        $ElWith = 'success';
                    }
                }

            } else {
                $ElWith = 'error';
                $MensajeFinal = 'Archivo no seleccionado';
            }
            return back()->with($ElWith, $MensajeFinal);

        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile() . ' Ubi:' . $th->getFile(), false);

            $larow2 = session('larow') ?? '';
            // $theTrace = Myhelp::cortarFrase($th->getTraceAsString(), 8);
            return back()->with('error', __('app.label.op_not_successfully') . 'codigo del error: ' . $larow2['codigo'] . ' error en la fila ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }



}
