<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Imports\PersonalImport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    public function __construct() {
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
    public function index(UserIndexRequest $request) {

        $users = User::query();
        if ($request->has('search')) {
            $users->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%")
                ->orWhere('email', 'LIKE', "%" . $request->search . "%")
                ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ->orWhere('pgrado', 'LIKE', "%" . $request->search . "%")
                ->orWhere('semestre', 'LIKE', "%" . $request->search . "%")
                ;
                
            })
            ->where('name', '!=', 'admin')
            ->where('name', '!=', 'Superadmin');
                    
            // $users->where('name', 'LIKE', "%" . $request->search . "%");
        }else{
            Myhelp::EscribirEnLog($this,'INDEX:users','index',false);
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
            'title'         => __('app.label.user'),
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'users'         => $users->with('roles')->paginate($perPage),
            'roles'         => $roles,
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    //! STORE functions
        public function updatingDate($date) {
            if($date === null || $date == '1969-12-31'){
                return null;
            }
            return date("Y-m-d",strtotime($date));
        }

        public function store(UserStoreRequest $request) {
            $permissions = Myhelp::EscribirEnLog($this,'STORE:users');

            DB::beginTransaction();
            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),

                    'identificacion' => $request->identificacion,
                    'sexo' => $request->sexo,
                    'fecha_nacimiento' => $this->updatingDate($request->fecha_nacimiento),
                    'semestre' => $request->semestre,
                    'semestre_mas_bajo' => $request->semestre_mas_bajo,
                    'limite_token_general' => 3,
                    'limite_token_leccion' => $request->limite_token_leccion,
                    'pgrado' => $request->pgrado,
                ]);
                $user->assignRole($request->role);
                DB::commit();
                Myhelp::EscribirEnLog($this,'STORE:users', 'usuario id:'.$user->id.' | '.$user->name.' guardado',false);
                
                return back()->with('success', __('app.label.created_successfully', ['name' => $user->name]));
            } catch (\Throwable $th) {
                DB::rollback();
                Myhelp::EscribirEnLog($this,'STORE:users', 'usuario id:'.$user->id.' | '.$user->name.' fallo en el guardado',false);
                return back()->with('error', __('app.label.created_error', ['name' => __('app.label.user')]) . $th->getMessage());
            }
        
        }
    //fin store functions

    public function show($id) { }
    public function edit($id) { }
    public function update(UserUpdateRequest $request, $id) {
        Myhelp::EscribirEnLog($this,'UPDATE:users','',false);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $request->password ? Hash::make($request->password) : $user->password,

                'identificacion' => $request->identificacion,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'semestre' => $request->semestre,
                'semestre_mas_bajo' => $request->semestre_mas_bajo,
                'limite_token_general' => $request->limite_token_general,
                'limite_token_leccion' => $request->limite_token_leccion,
            ]);
            $user->syncRoles($request->role);
            DB::commit();
            Myhelp::EscribirEnLog($this,'UPDATE:users', 'usuario id:'.$user->id.' | '.$user->name.' actualizado',false);
            
            return back()->with('success', __('app.label.updated_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this,'UPDATE:users', 'usuario id:'.$user->id.' | '.$user->name.'  fallo en el actualizado',false);
            return back()->with('error', __('app.label.updated_error', ['name' => $user->name]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        $permissions = Myhelp::EscribirEnLog($this,'DELETE:users');

        try {
            $user->delete();
            Myhelp::EscribirEnLog($this,'DELETE:users', 'usuario id:'.$user->id.' | '.$user->name.' borrado',false);
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this,'DELETE:users', 'usuario id:'.$user->id.' | '.$user->name.' fallo en el borrado:: '.$th->getMessage(),false);
            return back()->with('error', __('app.label.deleted_error', ['name' => $user->name]) . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request) {
        try {
            $user = User::whereIn('id', $request->id);
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $th->getMessage());
        }
    }

    public function ControllerPersonalImport(Request $request){
        Myhelp::EscribirEnLog($this,get_called_class(),'Empezo a importar',false);
        $countfilas = 0;
        try {
            if($request->archivo1) {

                $exten = $request->archivo1->getClientOriginalExtension();
                // Validar que el archivo es de Excel
                if ($exten != 'xlsx' && $exten != 'xls') {
                    return back()->with('warning', 'El archivo debe ser de Excel');
                }
                $pesoKilobyte = ((int)($request->archivo1->getSize()))/(1024);
                if ($pesoKilobyte > 256) { //debe pesar menos de 256KB
                    return back()->with('warning', 'El archivo debe pesar menos de 256KB');
                }

                Excel::import(new PersonalImport(), $request->archivo1);
                $countfilas = session('CountFilas', 0);
                session(['CountFilas' => 0]);


                Myhelp::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                return back()->with('success', __('app.label.op_successfully'). 'se leyeron '.$countfilas.' filas con exito');
            }else{
                return back()->with('error', __('app.label.op_not_successfully').' archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this,'IMPORT:users',' Fallo importacion: '.$th->getMessage(),false);
            return back()->with('error', __('app.label.op_not_successfully').' error en la fila '.$countfilas.' ' . $th->getMessage());
        }
        
    }

}
