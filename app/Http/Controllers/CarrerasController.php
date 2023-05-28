<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Carrera;
use App\Http\Requests\CarreraRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CarrerasController extends Controller
{
    public function index(Request $request)
    {
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        // log::channel('soloadmin')->info('Vista:' . $nombreC. '|  U:'.Auth::user()->name );
        log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||Carrera|| ' );

        $ListaControladoresYnombreClase = (explode('\\',get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        Log::info(' U -> '.Auth::user()->name. ' Accedio a la vista' .$nombreC );


        $titulo = __('app.label.Carreras');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Carreras = Carrera::query();
        
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
            $titulo = 'Carrera';
            
            if ($request->has('search')) {
                $Carreras->whereMonth('descripcion', $request->search);
                // $Carreras->OrwhereMonth('fecha_fin', $request->search);
                $Carreras->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            }
            
            if ($request->has(['field', 'order'])) {
                dd(substr($request->field,2));
                $Carreras->orderBy($request->field, $request->order);
            }else{
                $Carreras->orderBy('nombre');
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

        return Inertia::render('carrera/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Carreras->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Carreras'), 
                                    'href' => route('carrera.index')]],
            'nombresTabla'   =>  $nombresTabla,

        ]);
    }//fin index

    public function create() { }

    public function store(CarreraRequest $request)
    {
        DB::beginTransaction();
                $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
                log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||Carrera|| ' );

        try {
            $Carrera = Carrera::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => '',
                'universidad_id' => 1,
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

    public function show(Carrera $Carrera) { }
    public function edit(Carrera $Carrera) { }
    public function update(Request $request, Carrera $Carrera)
    {
        DB::beginTransaction();
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||Carrera|| ' );

        try {
            $Carrera->update([
                'nombre' => $request->nombre,
                //otrosCampos
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
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);

        DB::beginTransaction();

        try {
            // si se la rechazaron, tendra que hacer uno nuevo
            $Carreras = Carrera::findOrFail($id);
            $Carreras->delete();
            DB::commit();
            Log::info("U -> ".Auth::user()->name." borro Carrera id:".$id." correctamente");
            return back()->with('success', __('app.label.deleted_successfully2'));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar Carrera ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Carreras')]) . $th->getMessage());
        }
    }
}
