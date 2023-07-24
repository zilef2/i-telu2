<?php

namespace App\Http\Controllers;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Imports\PersonalImport;
use App\Imports\PersonalUniversidadImport;
use App\Models\Role;
use App\Models\Universidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('permission:read user', ['only' => ['index', 'show']]);
        $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy', 'destroyBulk']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
<<<<<<< HEAD
    public function index(UserIndexRequest $request) {
=======
    public function index(UserIndexRequest $request)
    {
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
        $permissions = Myhelp::EscribirEnLog($this, ' users');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $users = User::query();
        if ($request->has('search')) {
            $users->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('email', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('pgrado', 'LIKE', "%" . $request->search . "%");
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
    public function create()
    {
    }


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
            if(isset($user)) Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario id:' . $user->id ?? 'x' . ' | ' . $user->name ?? 'x' . ' fallo en el guardado', false);
            else Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario desconocido, fallo en el guardado', false);
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.user')]) . $th->getMessage());
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
            return back()->with('error', __('app.label.updated_error', ['name' => $user->name]) . $th->getMessage());
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
            Myhelp::EscribirEnLog($this, 'DELETE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' fallo en el borrado:: ' . $th->getMessage(), false);
            return back()->with('error', __('app.label.deleted_error', ['name' => $user->name]) . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $user = User::whereIn('id', $request->id);
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $th->getMessage());
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


    // Duplicate entry '1152194566' for key 'users_identificacion_unique'
    private function MensajeWar()
    {
        $bandera = false;
        $contares = [
            'contar1',
            'contar2',
            'contar3',
            'contar4',
            'contar5',
            'contarVacios',
        ];
        $mensajesWarnings = [
            '#correos Existentes: ',
            'Novedad, error interno: ',
            '#cedulas no numericas: ',
            '#generos distintos(M,F,otro): ',
            '#identificaciones repetidas: ',
            '#filas con celdas vacias: ',
        ];

        foreach ($contares as $key => $value) {
<<<<<<< HEAD
            $$value = session($value,0);
=======
            $$value = session($value, 0);
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
            session([$value => 0]);
            $bandera = $bandera || $$value > 0;
        }
        session(['contar2' => -1]);

        $mensaje = '';
        if ($bandera) {
            foreach ($mensajesWarnings as $key => $value) {
<<<<<<< HEAD
                if(${$contares[$key]} > 0){
                    $mensaje .= $value.${$contares[$key]}.'. ';
=======
                if (${$contares[$key]} > 0) {
                    $mensaje .= $value . ${$contares[$key]} . '. ';
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
                }
            }
        }

        return $mensaje;
    }

    public function uploadtrabajadors(Request $request)
    {
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar', false);
        $countfilas = 0;
        try {
            if ($request->archivo1) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request);
                if ($mensageWarning != '') return back()->with('warning', $mensageWarning);

                Excel::import(new PersonalImport(), $request->archivo1);

<<<<<<< HEAD
                $countfilas = session('CountFilas', 0);     session(['CountFilas'=> 0]);
                
=======
                $countfilas = session('CountFilas', 0);
                session(['CountFilas' => 0]);

>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
                $MensajeWarning = self::MensajeWar();
                if ($MensajeWarning !== '') {
                    return back()->with('success', 'Usuarios nuevos: ' . $countfilas)
                        ->with('warning', $MensajeWarning);
                }

                Myhelp::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                if ($countfilas == 0)
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                else
                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
            } else {
                return back()->with('error', __('app.label.op_not_successfully') . ' archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $th->getMessage(), false);
            return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . session('larow')[0] . ' error en la iteracion ' . $countfilas . ' ' . $th->getMessage());
        }
    }
    public function uploadUniversidad(Request $request)
    {
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar alumnos de universidades', false);
        $countfilas = 0;
        try {
            if ($request->archivo1 && $request->universidadID) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request);
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

                Myhelp::EscribirEnLog($this, 'IMPORT:uploadUniversidad', ' finalizo con exito', false);
                if ($countfilas == 0)
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                else
                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
            } else {
                return back()->with('error', __('app.label.op_not_successfully') . ' archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $th->getMessage(), false);
            return back()->with('error', __('app.label.op_not_successfully') . 'Nombre del error: ' . session('larow')[0] . ' error en la fila ' . $countfilas . ' ' . $th->getMessage());
        }
    }
}
