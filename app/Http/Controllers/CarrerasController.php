<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Controllers\Controller;

use App\Models\Carrera;
use App\Http\Requests\CarreraRequest;
use App\Models\Materia;
use App\Models\Universidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CarrerasController extends Controller
{

    //! funciones del index
        public function MapearClasePP(&$Carreras,$permissions) {

            $Carreras = $Carreras->get()->map(function ($carrera) use($permissions){
                
                if ($permissions == 'coordinador_de_programa' || $permissions == 'estudiante' || $permissions == 'profesor') {
                    $universidadUser = Auth::user()->universidades()->pluck('universidad_id')->toArray();
                    if(!in_array($carrera->universidad_id,$universidadUser)) return null;
                    
                    if ($permissions == 'estudiante' || $permissions == 'profesor') {
                        $carreraUser = Auth::user()->carreras()->pluck('carrera_id')->toArray();
                        if(!in_array($carrera->id,$carreraUser)) return null;
                    }
                }
                //# admin o coordinador_academico no tienen restricciones

                $carrera->hijo = $carrera->universidad_nombre();
                $CarreraUsers = $carrera->users;
                $carrera->tresPrimeros = Myhelp::ArrayInString($CarreraUsers->pluck('name'));
                $carrera->cuantosUs = $CarreraUsers->count();
                return $carrera;
            })->filter();
            
            // $Carreras = $Carreras->reject(function ($value) {
            //     return $value === null;
            // });

            // dd($Carreras);
        }

        //todo:1:Filtros copypaste no se ha tocado
        public function Filtros($request, &$materias,$permissions) {
            if ($request->has('selectedUni') && $request->selectedUni != 0) {
                // dd($request->selectedUni);
                $carrerasid = Carrera::has('materias')->where('universidad_id', $request->selectedUni)->pluck('id')->toArray();
                $materias->whereIn('carrera_id',$carrerasid);
            }
            if($request->selectedUni == 0) $request->selectedcarr = 0;
            
            if ($request->has('search')) {
                $materias->where('descripcion','LIKE', "%".$request->search."%");
                // $materias->whereMonth('descripcion', $request->search);
                // $materias->OrwhereMonth('fecha_fin', $request->search);
                $materias->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            }
            
            if ($request->has(['field', 'order'])) {
                $materias->orderBy($request->field, $request->order);
            }else{
                $materias->orderBy('nombre');
            }
        }
        //todo:1:losSelect copypaste no se ha tocado

        public function losSelect(&$carrerasSelect, &$MateriasRequisitoSelect, &$UniversidadSelect,$request,&$materias) {
            
            if($request->has('selectedUni')) {
                $carrerasSelect = Carrera::where('universidad_id', $request->selectedUni)->get();
            }else{
                $carrerasSelect = Carrera::all();
            }
    
    
            if($request->has('selectedcarr') && $request->selectedcarr != 0) {
                $materias = $materias->whereIn('carrera_id',$request->selectedcarr);
                // dd(
                //     $request->selectedcarr,
                //     $materias
                // );
            }
    
            // $carrerasSelect = Carrera::has('materias')->get();
            //todo: solo las necesarias
            $MateriasRequisitoSelect = Materia::all();
            $UniversidadSelect = Universidad::has('carreras')->get();
        }
    //! fin funciones del index

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this,'carrera');

        
        $titulo = __('app.label.Carreras');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Carreras = Carrera::query();
        $this->Filtros($request,$Carreras,$permissions);
        
        if($permissions === "estudiante") {
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD //2??
                ["Acciones","#"],
                [],
                [null,null,null]
            ];
            $nombresTabla[0][] = ["Centro costo","inicio", "fin", "horas trabajadas", "valido", "observaciones"];
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1][] = ["s_nombre", "s_descripcion"]; 
            //campos ordenables
            $nombresTabla[2][] = ["s_nombre", "s_descripcion"]; 
        }else{ // not estudiante
            $titulo = 'Carrera';
            
            if ($request->has('search')) {
                $Carreras->where('descripcion','LIKE', "%".$request->search."%");
                // $Carreras->whereMonth('descripcion', $request->search);
                // $Carreras->OrwhereMonth('fecha_fin', $request->search);
                $Carreras->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            }
            
            if ($request->has(['field', 'order'])) {
                $Carreras->orderBy(substr($request->field,2), $request->order);
            }else{
                $Carreras->orderBy('nombre');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            //0: como se ven //1 como es la BD //2 orden
            $nombresTabla =[
                ["Acciones","#"],
                [],
                [null,null]
            ];
            $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","descripcion","Universidad","# Inscritos","Inscritos"]);
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1] = array_merge($nombresTabla[1] , ["s_nombre", "s_descripcion","i_universidad_id","i_inscritos","s_inscritos"]);
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2] , ["s_nombre", "s_descripcion","i_universidad_id","",""]);
        }
        $this->MapearClasePP($Carreras,$permissions);
        
        $page = request('page', 1); // Current page number
        $total = $Carreras->count();
        $paginated = new LengthAwarePaginator(
            $Carreras->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $PapaSelect = Universidad::all();

        return Inertia::render('carrera/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [['label' => __('app.label.Carreras'), 
                                    'href' => route('carrera.index')]],
            'nombresTabla'   =>  $nombresTabla,
            'PapaSelect' => $PapaSelect,

        ]);
    }//fin index

    public function AsignarUsers(Request $request, $carreraid){
        $titulo = 'Seleccione los estudiantes a matricular';
        $permissions = Myhelp::EscribirEnLog($this,'carrera');
        $carrera = Carrera::find($carreraid);
        if($permissions === "estudiante") {
            return back()->with('error', __('app.label.no_permission'));
        } else { // not estudiante
            // $nombresTabla = $this->laTabla(0);
        }

        $universidad = Universidad::find($carrera->universidad_id);
        $users = User::query();

        $filtroUser = $this->UsuariosSinLosInscritos($carrera,$universidad);

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

        // dd($carrera->users);
        return Inertia::render('carrera/AsignarUsers', [ //carpeta
            'title'          =>  $titulo,
            'breadcrumbs'    =>  [['label' => __('app.label.carreras'), 'href' => route('carrera.index')]],
            'filters'       => $request->all(['search']),

            'usuariosPorInscribir' =>  $users->get(),
            'carrera' =>  $carrera,
            'universidad' =>  $universidad,
            'inscritos' => $carrera->users,
        ]);
    }

    public function UsuariosSinLosInscritos($modelo,$universidad) {
        $usuariosU = $universidad->users->pluck('id');
        $usuariosDeLaCarrera = $modelo->users->pluck('id');

        return (object) [
            'si'=>$usuariosU,
            'no'=>$usuariosDeLaCarrera
        ];
    }

    //fin index y sus funciones

    public function create() { }

    public function store(CarreraRequest $request){
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this,'carrera');

        try {
            $Carrera = Carrera::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => $request->descripcion,
                'universidad_id' => $request->universidad_id,
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." Guardo Carrera ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $Carrera->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en Guardar Carrera ".$request->nombre." - ".$th->getMessage());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Carrera')]) . $th->getMessage());
        }
    }

    public function SubmitAsignarUsers(Request $request) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this,' carrera');

        try {
            $carrera = carrera::find($request->carreraid);
            // dd($request->selectedId);
            $carrera->users()->attach(
                $request->selectedId
            );

            DB::commit();
            Log::info("U -> ".Auth::user()->name." matriculo a la carrera ".count($request->selectedId)." estudiantes correctamente");

            return redirect()->route('carrera.index')->with('success', __('app.label.created_success'));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en matricular(carrera) - ".$th->getMessage());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage());
        }
    }

    public function show(Carrera $Carrera) { }
    public function edit(Carrera $Carrera) { }
    public function update(Request $request, Carrera $Carrera) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this,'carrera');

        try {
            $Carrera->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'universidad_id' => $request->universidad_id,
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." actualizo Carrera ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Carrera->nombre]));
        } catch (\Throwable $th) {
            
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en actualizar Carrera ".$request->nombre." - ".$th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Carrera->nombre]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carrera  $Carrera
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Carrera $Carrera)
    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this,'carrera');

        DB::beginTransaction();
        try {
            $Carreras = Carrera::findOrFail($id);
            Log::info("U -> ".Auth::user()->name."La carrera id:".$id." y nombre:".$Carreras->nombre." ha sido borrada correctamente");
            $Carreras->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2',['nombre' => $Carreras->nombre]));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar Carrera ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Carreras')]) . $th->getMessage());
        }
    }
}
