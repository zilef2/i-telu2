<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Controllers\Controller;

use App\Models\Universidad;
use App\Http\Requests\UniversidadRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UniversidadsController extends Controller
{

    public function MapearClasePP(&$universidads) {
        $universidads = $universidads->get()->map(function ($universidad){

            $universidad->tresPrimeros = Myhelp::ArrayInString($universidad->users->pluck('name'));

            $universidad->cuantosUs = $universidad->users->count();
        
            return $universidad;
        });
        // dd($universidads);
    }

    public function fNombresTabla($numberPermissions) {
        if($numberPermissions < 2) { //estudiante
            //todo: esto ni se muestra a los estudiantes

            $nombresTabla =[//0: como se ven //1 como es la BD //2??
                ["Acciones","#"],
                [],
                [null,null,null]
            ];
            $nombresTabla[0][] = ["nombre", "observaciones"];
            
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1][] = ["s_nombre", "s_descripcion"]; 
            
            //campos ordenables
            $nombresTabla[2][] = ["s_nombre", "s_descripcion"]; 
        }

        if($numberPermissions < 3){ //profesor

            $nombresTabla =[//0: como se ven //1 como es la BD //2 orden
                ["Acciones","#"],
                [],
                [null,null]
            ];
            $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","# Inscritos","Inscritos"]);
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2] , ["s_nombre","",""]);
        }
        return $nombresTabla;
    }
    public function Filtros($request, &$Universidads) {
        if ($request->has('search')) {
            // $Universidads->whereMonth('descripcion', $request->search);
            // $Universidads->OrwhereMonth('fecha_fin', $request->search);
            $Universidads->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }
        
        if ($request->has(['field', 'order'])) {
            $Universidads->orderBy(substr($request->field,2), $request->order);
        }else{
            $Universidads->orderBy('nombre');
        }
    }

    // public function losSelect() {}

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this,'INDEX:universidad');

        $titulo = __('app.label.Universidads');
        $Universidads = Universidad::query();
        
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        if($permissions === "estudiante") {
            $nombresTabla = $this->fNombresTabla($numberPermissions);

        }else{ // not estudiante
            $nombresTabla = $this->fNombresTabla($numberPermissions);
            $this->Filtros($request, $Universidads);
        }

        $this->MapearClasePP($Universidads);

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $Universidads->forPage($page, $perPage),
            $Universidads->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('universidad/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [['label' => __('app.label.Universidads'), 
                                    'href' => route('universidad.index')]],
            'nombresTabla'   =>  $nombresTabla,
        ]);
    }//fin index

    public function AsignarUsers(Request $request, $universidadid){ //get
        $titulo = 'Seleccione los estudiantes a matricular';
        $permissions = Myhelp::EscribirEnLog($this,'universidad');
        if($permissions === "estudiante") {
            Myhelp::EscribirEnLog($this,'Criticou');

            return back()->with('error', __('app.label.no_permission'));
        } else { // not estudiante
            // $nombresTabla = $this->laTabla(0);
        }


        $universidad = universidad::find($universidadid);
        $users = User::query();
        $filtroUser = $this->UsuariosSinLosInscritos($universidad,$users);
        // if(count($filtroUser->si) > 0){
        //     $users->whereNotIn('users.id',$filtroUser->no)
        //         ->whereIn('users.id',$filtroUser->si);

        //     if ($request->has('search')) {
        //         $users->Where(function($query) use ($request){
        //             $query->where('name', 'LIKE', "%" . $request->search . "%");
        //             $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
        //             $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
        //         });
        //     }
        // }else{
        //     $users->where('id',0);
        // }

        return Inertia::render('universidad/AsignarUsers', [ //carpeta
            'title'          =>  $titulo,
            'breadcrumbs'    =>  [['label' => __('app.label.universidad'), 'href' => route('universidad.index')]],
            'filters'       => $request->all(['search']),

            'usuariosPorInscribir' =>  $filtroUser->no->get(),
            'inscritos' => $filtroUser->si->get(),

            'profesinscritos' =>  $filtroUser->profesors->get(),
            'profesPorInscribir' => $filtroUser->noprofesors->get(),

            'universidad' =>  $universidad,
            // 'UniversidadSelect' => $UniversidadSelect,
        ]);
    }
    public function UsuariosSinLosInscritos($modelo,$users) {
        // $estudiantes = $users->whereHas('roles', function ($query) {
        //     $query->where('name', 'estudiante');
        //     return $query;

        // })->pluck('id');

        $estudiantesDeLaU = $modelo->estudiantes($modelo->id,true,'estudiante');//->pluck('users.id');
        // $estudiantesDeOtraU = $modelo->estudiantes($modelo->id,false,'estudiante');//->pluck('users.id');

        $estudiantesDeOtraU = User::whereNotIn('id',$estudiantesDeLaU->pluck('users.id'))
            ->WhereHas('roles',function ($query){
                $query->where('name', 'estudiante');
        });
        
        $profDeLaU = $modelo->estudiantes($modelo->id,true,'profesor');
        // $profDeOtraU = $modelo->estudiantes($modelo->id,false,'profesor');
        $profDeOtraU = User::whereNotIn('id',$profDeLaU->pluck('users.id'))
            ->WhereHas('roles',function ($query){
                $query->where('name', 'profesor');
        });

        // dd($profDeOtraU);
        return (object) [
            'si'=>$estudiantesDeLaU,
            'no'=>$estudiantesDeOtraU,
            // 'siNames'=>$estudiantesDeLaU->pluck('name'),

            'profesors'=>$profDeLaU,
            'noprofesors'=>$profDeOtraU,
        ];
    }

    public function create() { }

    public function store(UniversidadRequest $request)
    {
        DB::beginTransaction();
                $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
                log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||Universidad|| ' );

        try {
            $Universidad = Universidad::create([
                'nombre' => $request->nombre,
                //otrosCampos
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." Guardo Universidad ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $Universidad->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en Guardar Universidad ".$request->nombre." - ".$th->getMessage());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage());
        }
    }

    public function SubmitAsignarUsers(Request $request) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this,' universidad');

        try {
            $Universidad = Universidad::find($request->universidadid);
            // dd($request->selectedId);
            $Universidad->users()->attach(
                $request->selectedId
            );

            DB::commit();
            Log::info("U -> ".Auth::user()->name." matriculo a la universidad ".count($request->selectedId)." estudiantes correctamente");

            // return back()->with('success', __('app.label.created_success'));
            return redirect()->route('universidad.index')->with('success', __('app.label.created_success'));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en matricular(universidad) - ".$th->getMessage());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage());
        }
    }
    public function toEraseId(Request $request) {
        DB::beginTransaction();
        $permission = Myhelp::EscribirEnLog($this,' universidad','Inicio de quitar estudiante de universidad');
        
        try {
            if($permission == 'coordinador_academico' || $permission == 'coordinador_academico'){

                $Universidad = Universidad::find($request->universidadid);
                // dd($request->selectedId);
                $Universidad->users()->deatach(
                    $request->toEraseId
                );

                DB::commit();
                Log::info("U -> ".Auth::user()->name." desmatriculo a la universidad ".count($request->selectedId)." estudiantes correctamente");
                
                // return back()->with('success', __('app.label.created_success'));
                return redirect()->route('universidad.index')->with('success', 'Usuarios desmatriculados con exito');
            }
            Log::critical("U -> ".Auth::user()->name." desmatriculo a la universidad ".count($request->selectedId)." estudiantes. Fallo en seguridad.");

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en matricular(universidad) - ".$th->getMessage());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage());
        }
    }

    public function show(Universidad $Universidad) { }
    public function edit(Universidad $Universidad) { }
    public function update(Request $request, Universidad $Universidad)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this,' UPDATE:universidad ','',false);

        try {
            $Universidad->update([
                'nombre' => $request->nombre,

                //otrosCampos
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." actualizo Universidad ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Universidad->nombre]));
        } catch (\Throwable $th) {
            
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en actualizar Universidad ".$request->nombre." - ".$th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Universidad->nombre]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Universidad  $Universidad
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Universidad $Universidad)
    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this,'DELETE:universidad','',false);

        DB::beginTransaction();

        try {
            // si se la rechazaron, tendra que hacer uno nuevo
            $Universidads = Universidad::findOrFail($id);
            $Universidads->delete();
            DB::commit();
            Myhelp::EscribirEnLog($this,'universidad','borro Universidad id:'.$id.' correctamente',false);
            return back()->with('success', __('app.label.deleted_successfully2'));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar Universidad ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Universidads')]) . $th->getMessage());
        }
    }
}
