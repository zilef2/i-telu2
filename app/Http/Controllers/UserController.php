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
use App\Models\Role;
use App\Models\Universidad;
use App\Models\User;
use Illuminate\Http\Request;
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

//        $this->middleware('ErrorHandlerMiddleware');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserIndexRequest $request)
    {
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
        }

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $role = auth()->user()->roles->pluck('name')[0];
        $roles = Role::get();
        if ($role != 'superadmin') {
            $users->whereHas('roles', function ($query) {
                return $query->where('name', '<>', 'superadmin');
            });
            $roles = Role::where('name', '<>', 'superadmin')->where('name', '<>', 'admin')->get();
        }

        return Inertia::render('User/Index', [
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
            'title'         => __('app.label.user'),
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'users'         => $users->with('roles')->paginate($perPage),
            'roles'         => $roles,
            'numberPermissions'         => $numberPermissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //public function create()    {    }


    //! STORE - UPDATE - DELETE
    //! STORE functions
    public function updatingDate($date)
    {
        if ($date === null || $date == '1969-12-31') {
            return null;
        }
        return date("Y-m-d", strtotime($date));
    }

    public function store(UserStoreRequest $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'STORE:users');

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
            DB::commit();
            Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' guardado', false);

            return back()->with('success', __('app.label.created_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            if (isset($user)) Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario id:' . $user->id ?? 'x' . ' | ' . $user->name ?? 'x' . ' fallo en el guardado', false);
            else Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario desconocido, fallo en el guardado', false);
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.user')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
    //fin store functions

    public function show($id)
    {
    }
    public function edit($id)
    {
    }
    public function update(UserUpdateRequest $request, $id)
    {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
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

    public function subirexceles()
    { //just  a view
        $permissions = Myhelp::EscribirEnLog($this, ' materia');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        return Inertia::render('User/subirExceles', [
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
            'title'         => __('app.label.user'),
            'numUsuarios'   => count(User::all()) - 1,
            'UniversidadSelect'   => Universidad::all()
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
            foreach ($elError as $key => $value) {

                ${$contares[$key]} = $value . ' ';
                $bandera = $bandera || $value > 0;
            }
        }
        //en ese caso, imprimimos el valor en $mensajesWarnings
        $mensaje = '';
        if ($bandera) {
            foreach ($mensajesWarnings as $key => $value) {
                if (isset(${$contares[$key]}) && count(${$contares[$key]}) > 0) {
                    $mensaje .= $value . ${$contares[$key]} . '. ';
                }
            }
        }
        return $mensaje;
    }

    public function uploadestudiantes(Request $request)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar', false);
        $countfilas = 0;
        $personalImp = new PersonalImport();

        try {
            if ($request->archivo1) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request->archivo1);
                if ($mensageWarning != '') return back()->with('warning', $mensageWarning);

                // $result =
                Excel::import($personalImp, $request->archivo1);
                $misErrores = [
                    $personalImp->contarEmailExistente,
                    $personalImp->contarActualizado,
                    $personalImp->contarNoNumeros,
                    $personalImp->contarSex,
                    $personalImp->contarCargo,
                ];
                $MensajeWarning = self::MensajeWar($misErrores);
                if ($MensajeWarning !== '') {
                    return back()->with('success',
                        'Usuarios nuevos: ' . $personalImp->countfilas.
                        ' Usuarios actualizados: ' . $personalImp->countfilasActualizadas
                    )->with('warning', $MensajeWarning);
                }


                Myhelp::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                DB::commit();
                if ($personalImp->countfilas == 0 && $personalImp->countfilasActualizadas == 0)
                    // return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                    return to_route('user.index')->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                else{
                    //happy path
                    // return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $personalImp->countfilas + $personalImp->countfilasActualizadas . ' filas con exito');
                    return to_route('user.index')->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $personalImp->countfilas + $personalImp->countfilasActualizadas . ' filas con exito');
                }
            } else {
                return back()->with('error', __('app.label.op_not_successfully') . ' archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            DB::rollback();

            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile(), false);
            $larowNull = $personalImp->larow[0] ?? null;
            return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . $larowNull . ' error en la iteracion ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
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
                    $MensajeWarning = '';
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
        try {
            if ($request->archivo2_matricular && $request->universidadID) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request->archivo2_matricular);
                if ($mensageWarning != '') return back()->with('warning', $mensageWarning);

                Excel::import(new PersonalUniversidadImport($request->universidadID), $request->archivo2_matricular);


                // try {
                //     Excel::import(new PersonalUniversidadImport($request->universidadID), $request->archivo1);
                // } catch (ValidationException $e) {
                //     $failures = $e->failures();
                //     $frow = ''; $fattribute = []; $ferrors = []; $fvalues = [];$countF = 0;

                //     foreach ($failures as $failure) {
                //         $frow .= 'Fila: ';
                //         $frow .= $failure->row(); // row that went wrong
                //         // $frow .= '. atributo: ';
                //         // $frow .= $failure->attribute(); // either heading key (if using heading row concern) or column index
                //         // dd($failure->errors());
                //         // dd(
                //         //     $failure->values(),
                //         //     $failure->errors(),
                //         // );
                //         $frow .= $failure->errors()[0]; // Actual error messages from Laravel validator
                //         // $frow .= ' Valor: ';
                //         //array_key_first($failure->values()).' '.
                //         // $frow .= array_values($failure->values())[0]; // The values of the row that has failed.
                //         $frow .= ". \n";
                //         $countF++;

                //         if($countF > (6*3)) break; //3 columnas
                //     }
                //     Myhelp::EscribirEnLog($this, 'IMPORT:uploadUniversida ValidationException', 'ERRORES: '.$countF, false);
                //     return back()->with('warning', 'ERRORES: '.$frow);
                // }


                $countfilas = session('CountFilas', 0);
                $contarVacios = session('contarVacios', 0);
                $contarNoNumeros = session('contarNoNumeros', 0);

                session(['CountFilas' => 0]);
                session(['contarVacios' => 0]);
                session(['contarNoNumeros' => 0]);

                $HuboWarning = $contarVacios > 0 || $contarNoNumeros > 0;
                if ($HuboWarning) {
                    $MensajeWarning = '';
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
            $larow2 = session('larow') ?? '';
            // $theTrace = Myhelp::cortarFrase($th->getTraceAsString(), 8);
            return back()->with('error', __('app.label.op_not_successfully') . 'Usuario del error: ' . $larow2['usuario'] . ' error en la fila ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }


    public function uploadCarreras(Request $request) {
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar Carreras', false);
        $countfilas = 0;
        try {
            if ($request->archivo_componente_carreras && $request->universidadID) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request->archivo_componente_carreras);
                if ($mensageWarning != '') return back()->with('warning', $mensageWarning);

                Excel::import(new CarreraImport($request->universidadID), $request->archivo_componente_carreras);

                $countfilas = session('CountFilas', 0);
                $contarVacios = session('contarVacios', 0);
                $contarNoNumeros = session('contarNoNumeros', 0);

                session(['CountFilas' => 0]);
                session(['contarVacios' => 0]);
                session(['contarNoNumeros' => 0]);

                $HuboWarning = $contarVacios > 0 || $contarNoNumeros > 0;
                if ($HuboWarning) {
                    $MensajeWarning = '';
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
            // } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            //     $failures = $e->failures();

            //     foreach ($failures as $failure) {
            //         $failure->row(); // row that went wrong
            //         $failure->attribute(); // either heading key (if using heading row concern) or column index
            //         $failure->errors(); // Actual error messages from Laravel validator
            //         $failure->values(); // The values of the row that has failed.
            //     }

            //     Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $e->getMessage() . ' L:' . $e->getLine(), false);
            //     $larow2 = session('larow') ?? '';
            //     // $eeTrace = Myhelp::cortarFrase($e->getTraceAsString(), 8);
            //     return back()->with('error', __('app.label.op_not_successfully') . 'codigo del error: ' . $larow2['codigo'] . ' error en la fila ' . $countfilas .' '. $e->getMessage() . ' L:' . $e->getLine());

        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: '
                . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile() . ' Ubi:' . $th->getFile(), false);

            $larow2 = session('larow') ?? '';
            // $theTrace = Myhelp::cortarFrase($th->getTraceAsString(), 8);
            return back()->with('error', __('app.label.op_not_successfully') . 'codigo del error: ' . $larow2['codigo'] . ' error en la fila ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    public function VistaPrincipal()
    {
        $user = Myhelp::AuthU();
        $numberpermissions = Myhelp::getPermissionToNumber();
        if($numberpermissions < 2) return redirect('/materia');

//        if($numberpermissions < 8) return redirect('/SeleccioneAsignatura');

        $textoBotones=[
          "Primer paso",
          "Mis materias",
        ];
        $ExplicacionBotones=[
          "Para empezar a aprender",
          "Listado de tus asignaturas",
        ];
        $linkBotones=[
          "SeleccioneAsignatura",
          "materia.index",
        ];
        return Inertia::render('Dashboard', [
            'users'         => (int) User::count(),
            'roles'         => (int) Role::count(),
            'permissions'   => (int) Permission::count(),
            'textoBotones'  => $textoBotones,
            'linkBotones'   => $linkBotones,
            'ExplicacionBotones'   => $ExplicacionBotones,
            'plan_id'       => (int) $user->plan_id, //si es cero no tiene plan
        ]);
    }

    public function SeleccioneAsignatura()
    {
        $user = Myhelp::AuthU();
        Myhelp::EscribirEnLog($this, ' SeleccioneAsignatura ');
        $numberpermissions = Myhelp::getPermissionToNumber();
        if($numberpermissions < 1.5) return redirect('/materia');

//        if($numberpermissions < 8) return redirect('/SeleccioneAsignatura');

        $IDmateriasDelUser = $user->materias()->pluck('materias.id');
        $materias = Materia::WhereNotIn('id',$IDmateriasDelUser);

        return Inertia::render('User/Autoasignacion/SeleccioneAsignatura', [
            'title' => 1,
            'perPage' => 1,
            'numberPermissions' => 1,
            'materias' => $materias->paginate(10),
        ]);
    }

    public function ComprarAsignatura(Request $request)
    {
        $user = Myhelp::AuthU();
        Myhelp::EscribirEnLog($this, ' ComprarAsignatura ');
        $numberpermissions = Myhelp::getPermissionToNumber();
        if($numberpermissions < 2) return redirect('/materia');

        $lasCarreras = Carrera::WhereIn('id',$request->materias)->get();
        $soloIdCarreras = $lasCarreras->pluck('id');
        $soloIdUniversidades = $lasCarreras->pluck('universidad_id');

        foreach ($soloIdUniversidades as $uniID){
            if(!$user->ExistUniversidad($uniID)){
                $user->universidades()->attach($uniID);
            }
        }
        foreach ($soloIdCarreras as $carID){
            if(!$user->ExistCarrera($carID)){
                $user->carreras()->attach($carID);
            }
        }
        foreach ($request->materias as $matID){
            if(!$user->ExistMateria($matID)){
                $user->materias()->attach($matID);
            }
        }
//        $user->carreras()->attach($soloIdMaterias);
//        $user->materias()->attach($request->materias);

        return redirect()->route('materia.index')->with('success',"Usted ha matriculado". count($request->materias)." materias" );
    }
}
