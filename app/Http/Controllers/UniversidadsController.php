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

    public function CalcularClasePrincipal(&$universidads) {
        $universidads = $universidads->get()->map(function ($universidad){

            $universidad->tresPrimeros = Myhelp::ArrayInString($universidad->users->pluck('name'));

            $universidad->cuantosUs = $universidad->users->count();
        
            return $universidad;
        });
        // dd($universidads);
    }


    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this,'INDEX:universidad');

        $titulo = __('app.label.Universidads');
        $Universidads = Universidad::query();
        
        if($permissions === "estudiante") {
            $perPage = $request->has('perPage') ? $request->perPage : 10;

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
        }else{ // not estudiante
            $titulo = 'Universidad';
            
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
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD //2 orden
                ["Acciones","#"],
                [],
                [null,null]
            ];
            $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","# Inscritos","Inscritos"]);
            
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1] = array_merge($nombresTabla[1] , ["s_nombre","i_inscritos","s_inscritos"]);
            
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2] , ["s_nombre","",""]);
        }

        $this->CalcularClasePrincipal($Universidads);
        
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
        if(count($filtroUser->si) > 0){
            $users->whereNotIn('users.id',$filtroUser->no)
                ->whereIn('users.id',$filtroUser->si);

            if ($request->has('search')) {
                $users->Where(function($query) use ($request){
                    $query->where('name', 'LIKE', "%" . $request->search . "%");
                    $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                });
            }
        }else{
            $users->where('id',0);
        }

       

        return Inertia::render('universidad/AsignarUsers', [ //carpeta
            'title'          =>  $titulo,
            'breadcrumbs'    =>  [['label' => __('app.label.universidad'), 'href' => route('universidad.index')]],
            'filters'       => $request->all(['search']),

            'usuariosPorInscribir' =>  $users->get(),
            'inscritos' => $universidad->users,
            'universidad' =>  $universidad,
            // 'UniversidadSelect' => $UniversidadSelect,
        ]);
    }

    public function UsuariosSinLosInscritos($modelo,$users) {
        $estudiantes = $users->whereHas('roles', function ($query) {
            return $query->where('name', 'estudiante');
        })->pluck('id');
        $usuariosDeLaU = $modelo->users;

        return (object) [
            'si'=>$estudiantes,
            'no'=>$usuariosDeLaU->pluck('id'),
            'siNames'=>$usuariosDeLaU->pluck('name'),
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