<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use Inertia\Inertia;

use App\Models\Subtopico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubtopicoRequest;
use App\Models\Tema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class subtopicosController extends Controller
{

    // - MapearClasePP, Filtros
        public function Filtros($request, &$subtopicos) {
            if ($request->has('search')) {
                $subtopicos->where('descripcion','LIKE', "%".$request->search."%");
                // $subtopicos->whereMonth('descripcion', $request->search);
                // $subtopicos->OrwhereMonth('fecha_fin', $request->search);
                $subtopicos->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            }
            
            if ($request->has(['field', 'order'])) {
                $subtopicos->orderBy($request->field, $request->order);
            }else{
                $subtopicos->orderBy('nombre');
            }
        }
        public function MapearClasePP(&$subtopicos) {
            $subtopicos = $subtopicos->get()->map(function ($subtopico){
                $subtopico->hijo = $subtopico->tema_nombre();
                return $subtopico;
            });
        }
    // -fin : MapearClasePP, Filtros
    
    public function index(Request $request) {
        Myhelp::EscribirEnLog($this,'subtopico');

        $titulo = __('app.label.subtopicos');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $subtopicos = Subtopico::query();
        
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        if($permissions === "estudiante") {

        }else{ // not estudiante
            $this->Filtros($request, $subtopicos);

        }
        $this->MapearClasePP($subtopicos);
        // dd($subtopicos);
        $page = request('page', 1); // Current page number
        $total = $subtopicos->count();
        $paginated = new LengthAwarePaginator(
            $subtopicos->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('subtopico/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [['label' => __('app.label.subtopicos'), 
                                    'href' => route('subtopico.index')]],
            'temasSelect'   =>  Tema::all(),

        ]);
    }//fin index

    public function create() { }

    public function store(SubtopicoRequest $request) {
        DB::beginTransaction();
                $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
                log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||subtopico|| ' );

        try {
            $subtopico = Subtopico::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                //otrosCampos
                'tema_id' => $request->tema_id,
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." Guardo subtopico ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $subtopico->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en Guardar subtopico ".$request->nombre." - ".$th->getMessage());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.subtopico')]) . $th->getMessage());
        }
    }

    public function show(subtopico $subtopico) { }
    public function edit(subtopico $subtopico) { }

    public function update(Request $request, $id) {
        $subtopico = Subtopico::find($id);
        DB::beginTransaction();
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||subtopico|| ' );
        try {
            // dd($subtopico,$request->nombre);
            $subtopico->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tema_id' => $request->tema_id,
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." actualizo subtopico ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $subtopico->nombre]));
        } catch (\Throwable $th) {
            
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en actualizar subtopico ".$request->nombre." - ".$th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $subtopico->nombre]) . $th->getMessage());
        }
    }

    // public function destroy(subtopico $subtopico)
    public function destroy($id) {
        Myhelp::EscribirEnLog($this,get_called_class(),'',false);

        DB::beginTransaction();
        try {
            $subtopicos = Subtopico::findOrFail($id);
            $subtopicos->delete();
            Myhelp::EscribirEnLog($this,get_called_class(),"La subtopico id:".$id." y nombre:".$subtopicos->nombre." ha sido borrada correctamente",false);
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2',['nombre' => $subtopicos->nombre]));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar subtopico ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.subtopicos')]) . $th->getMessage());
        }
    }
}
