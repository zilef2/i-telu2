<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use App\Models\subtopico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubtopicoRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class subtopicosController extends Controller
{
    public function index(Request $request) {
        if(Auth::user()->isAdmin < 1){
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            // log::channel('soloadmin')->info('Vista:' . $nombreC. '|  U:'.Auth::user()->name );
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||subtopico|| ' );
        }

        $titulo = __('app.label.subtopicos');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $subtopicos = subtopico::query();
        
        if($permissions === "operator") {
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
        }else{ // not operator
            $titulo = 'subtopico';
            
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
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            //0: como se ven //1 como es la BD //2 orden
            $nombresTabla =[
                ["Acciones","#"],
                [],
                [null,null]
            ];
            $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","descripcion","tema"]);
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1] = array_merge($nombresTabla[1] , ["s_nombre", "s_descripcion","i_tema_id"]);
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2] , ["nombre", "descripcion",""]);
        }
        $subtopicos = $subtopicos->get()->map(function ($subtopico){
            $subtopico->hijo = $subtopico->tema_nombre();
            return $subtopico;
        });
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
            'nombresTabla'   =>  $nombresTabla,

        ]);
    }//fin index

    public function create() { }

    public function store(SubtopicoRequest $request) {
        DB::beginTransaction();
                $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
                log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||subtopico|| ' );

        try {
            $subtopico = subtopico::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => 'Descripcion generica',
                'tema_id' => 1,//todo: temp
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
        $subtopico = subtopico::find($id);
        DB::beginTransaction();
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||subtopico|| ' );
        try {
            // dd($subtopico,$request->nombre);
            $subtopico->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->nombre,//todo: temp
                //otrosCampos
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
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||subtopico|| ' );

        DB::beginTransaction();

        try {
            $subtopicos = subtopico::findOrFail($id);
            Log::info($nombreC." U -> ".Auth::user()->name."La subtopico id:".$id." y nombre:".$subtopicos->nombre." ha sido borrada correctamente");
            $subtopicos->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2',['nombre' => $subtopicos->nombre]));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar subtopico ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.subtopicos')]) . $th->getMessage());
        }
    }
}
