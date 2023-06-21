<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use App\Models\tema;
use App\Http\Requests\MateriumRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\TemaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TemasController extends Controller
{
    public function index(Request $request) {
        if(Auth::user()->isAdmin < 1){
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            // log::channel('soloadmin')->info('Vista:' . $nombreC. '|  U:'.Auth::user()->name );
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||tema|| ' );
        }

        $titulo = __('app.label.temas');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $temas = tema::query();
        
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
            $titulo = 'tema';
            
            if ($request->has('search')) {
                $temas->where('descripcion','LIKE', "%".$request->search."%");
                // $temas->whereMonth('descripcion', $request->search);
                // $temas->OrwhereMonth('fecha_fin', $request->search);
                $temas->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            }
            
            if ($request->has(['field', 'order'])) {
                $temas->orderBy($request->field, $request->order);
            }else{
                $temas->orderBy('nombre');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            //0: como se ven //1 como es la BD //2 orden
            $nombresTabla =[
                ["Acciones","#"],
                [],
                [null,null]
            ];
            $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","descripcion","materia"]);
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1] = array_merge($nombresTabla[1] , ["s_nombre", "s_descripcion","i_materia_id"]);
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2] , ["nombre", "descripcion",""]);
        }
        $temas = $temas->get()->map(function ($tema){
            $tema->hijo = $tema->materia_nombre();
            return $tema;
        });
        // dd($temas);
        $page = request('page', 1); // Current page number
        $total = $temas->count();
        $paginated = new LengthAwarePaginator(
            $temas->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('tema/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [['label' => __('app.label.temas'), 
                                    'href' => route('tema.index')]],
            'nombresTabla'   =>  $nombresTabla,

        ]);
    }//fin index

    public function create() { }

    public function store(TemaRequest $request) {
        DB::beginTransaction();
                $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
                log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||tema|| ' );

        try {
            $tema = tema::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => 'Descripcion generica',
                'materia_id' => $request->materia_id,
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." Guardo tema ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $tema->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en Guardar tema ".$request->nombre." - ".$th->getMessage());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.tema')]) . $th->getMessage());
        }
    }

    public function show(tema $tema) { }
    public function edit(tema $tema) { }

    public function update(Request $request, $id) {
        $tema = tema::find($id);
        DB::beginTransaction();
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||tema|| ' );
        try {
            // dd($tema,$request->nombre);
            $tema->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->nombre,//todo: temp
                //otrosCampos
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." actualizo tema ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $tema->nombre]));
        } catch (\Throwable $th) {
            
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en actualizar tema ".$request->nombre." - ".$th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $tema->nombre]) . $th->getMessage());
        }
    }

    // public function destroy(tema $tema)
    public function destroy($id) {
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||tema|| ' );

        DB::beginTransaction();

        try {
            $temas = tema::findOrFail($id);
            Log::info($nombreC." U -> ".Auth::user()->name."La tema id:".$id." y nombre:".$temas->nombre." ha sido borrada correctamente");
            $temas->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2',['nombre' => $temas->nombre]));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar tema ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.temas')]) . $th->getMessage());
        }
    }
}
