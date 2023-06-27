<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

use App\Models\parametro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\parametroRequest;
use App\Models\Subtopico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class parametrosController extends Controller
{
    
    public $respuestaLimite = 'Limite de tokens';
    public $respuestaLarga = 'La respuesta es demasiado extensa';
    public $MAX_USAGE_RESPUESTA = 550;
    public $MAX_USAGE_TOTAL = 600;

    public function fNombresTabla() {
        $nombresTabla =[//0: como se ven //1 como es la BD //2??
            [],
            // ["#","Acciones"],
            [],
            [null,null,null]
        ];
        array_push($nombresTabla[0], "prompEjercicios", "prompObjetivos","NumeroTicketDefecto");
        //m for money || t for datetime || d date || i for integer || s string || b boolean
        $nombresTabla[1][] = ["s_prompEjercicios", "s_prompObjetivos","i_NumeroTicketDefecto"];
        $nombresTabla[2][] = ["s_prompEjercicios", "s_prompObjetivos","i_NumeroTicketDefecto"];

        return $nombresTabla;
    }

    //todo: seguramente no se usen los filtros

    public function Filtros($request, &$parametros) {
        if ($request->has('search')) {
            // $parametros->where('descripcion','LIKE', "%".$request->search."%");
            // $parametros->whereMonth('descripcion', $request->search);
        }
        
        if ($request->has(['field', 'order'])) {
            $parametros->orderBy($request->field, $request->order);
        }else{
            $parametros->orderBy('prompEjercicios');
        }
    }
    public function losSelect() {
        // $MateriasSelect = Materia::all();
        // return [
        //     'MateriasSelect' => $MateriasSelect
        // ];
    }


    public function index(Request $request) {
        if(Auth::user()->isAdmin < 1){
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||parametro|| ' );
        }

        $titulo = __('app.label.parametros');
        $permissions = auth()->user()->roles->pluck('name')[0];//todo: validar que solo sea admin o super
        

        $nombresTabla = $this->fNombresTabla();

        return Inertia::render('parametro/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  1,
            'fromController' =>  parametro::all(),
            'breadcrumbs'    =>  [['label' => __('app.label.parametros'), 'href' => route('parametro.index')]],
            'nombresTabla'   =>  $nombresTabla,
        ]);
    }//fin index

    public function create() { }
    public function store(Request $request) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this,get_called_class(),'',false);

        try {
            $parametro = parametro::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => $request->descripcion,
                'materia_id' => $request->materia_id,
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." Guardo parametro ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $parametro->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en Guardar parametro ".$request->nombre." - ".$th->getMessage());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.parametro')]) . $th->getMessage());
        }
    }

    public function show(parametro $parametro) { }
    public function edit(parametro $parametro) { }

    public function update(Request $request, $id) {
        $parametro = parametro::find($id);
        DB::beginTransaction();
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||parametro|| ' );
        try {
            // dd($parametro,$request->nombre);
            $parametro->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'materia_id' => $request->materia_id,
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." actualizo parametro ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $parametro->nombre]));
        } catch (\Throwable $th) {
            
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en actualizar parametro ".$request->nombre." - ".$th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $parametro->nombre]) . $th->getMessage());
        }
    }

    // public function destroy(parametro $parametro)
    public function destroy($id) {
        Myhelp::EscribirEnLog($this,get_called_class(),'',false);

        DB::beginTransaction();

        try {
            $parametros = parametro::findOrFail($id);
            Myhelp::EscribirEnLog($this,get_called_class(),"La parametro id:".$id." y nombre:".$parametros->nombre." ha sido borrada correctamente",false);

            
            $parametros->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2',['nombre' => $parametros->nombre]));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar parametro ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.parametros')]) . $th->getMessage());
        }
    }
    

}
