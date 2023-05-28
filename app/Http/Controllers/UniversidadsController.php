<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Universidad;
use App\Http\Requests\UniversidadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UniversidadsController extends Controller
{
    public function index(Request $request)
    {
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        // log::channel('soloadmin')->info('Vista:' . $nombreC. '|  U:'.Auth::user()->name );
        log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' |UNIVERSIDAD| ' );

        $ListaControladoresYnombreClase = (explode('\\',get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        Log::info(' U -> '.Auth::user()->name. ' Accedio a la vista' .$nombreC );


        $titulo = __('app.label.Universidads');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Universidads = Universidad::query();
        
        if($permissions === "operator") {
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
        }else{ // not operator
            $titulo = 'Universidad admin';
            
            if ($request->has('search')) {
                $Universidads->whereMonth('descripcion', $request->search);
                // $Universidads->OrwhereMonth('fecha_fin', $request->search);
                $Universidads->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            }
            
            if ($request->has(['field', 'order'])) {
                dd(substr($request->field,2));
                $Universidads->orderBy($request->field, $request->order);
            }else{
                $Universidads->orderBy('nombre');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD //2 orden
                ["Acciones","#"],
                [],
                [null,null]
            ];
            $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","descripcion"]);
            
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1] = array_merge($nombresTabla[1] , ["s_nombre", "s_descripcion"]);
            
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2] , ["s_nombre", "s_descripcion"]);

        }

        return Inertia::render('universidad/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Universidads->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Universidads'), 
                                    'href' => route('universidad.index')]],
            'nombresTabla'   =>  $nombresTabla,

        ]);
    }//fin index

    public function create() { }

    public function store(UniversidadRequest $request)
    {
        DB::beginTransaction();
                $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
                log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' |UNIVERSIDAD| ' );

        try {
            $Universidad = Universidad::create([
                'nombre' => $request->nombre,
                //otrosCampos
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." Guardo Universidad ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.created_successfully', ['nombre' => $Universidad->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en Guardar Universidad ".$request->nombre." - $th->getMessage()");

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage());
        }
    }

    public function show(Universidad $Universidad) { }
    public function edit(Universidad $Universidad) { }
    public function update(Request $request, Universidad $Universidad)
    {
        DB::beginTransaction();
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' |UNIVERSIDAD| ' );

        try {
            $Universidad->update([
                'nombre' => $request->nombre,
                //otrosCampos
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." actualizo Universidad ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.updated_successfully', ['nombre' => $Universidad->nombre]));
        } catch (\Throwable $th) {
            
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en actualizar Universidad ".$request->nombre." - $th->getMessage()");
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
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);

        DB::beginTransaction();

        try {
            // si se la rechazaron, tendra que hacer uno nuevo
            $Universidads = Universidad::findOrFail($id);
            $Universidads->delete();
            DB::commit();
            Log::info("U -> ".Auth::user()->name." borro Universidad id:".$id." correctamente");
            return back()->with('success', __('app.label.deleted_successfully'));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar Universidad ".$id." - $th->getMessage()");
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Universidads')]) . $th->getMessage());
        }
    }
}
