<?php

namespace App\Http\Controllers;

use App\helpers\HelpGPT;
use App\helpers\JustChatFunctionGPT;
use App\helpers\ModelFunctions;
use App\helpers\Myhelp;
use Inertia\Inertia;

use App\Models\Unidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnidadRequest;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Subtopico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenAI;

class UnidadsController extends Controller {
    public $respuestaLimite = 'Limite de tokens';
    public $respuestaLarga = 'La respuesta es demasiado extensa';
    public $MAX_USAGE_RESPUESTA = 550;
    public $MAX_USAGE_TOTAL = 600;
    private $modelName = 'Unidad';


    //! index functions () los filtros van primero ome
    public function Filtros($request, &$unidads, $numberPermissions) {
        if ($numberPermissions < (int)(env('PERMISS_VER_FILTROS_SELEC'))) { //coorPrograma,profe,estudiante
            $MateriasSelect = Auth::user()->materias->pluck('id');

            $unidads->WhereIn('materia_id', $MateriasSelect);
        }

        $showCarrera = null;
        if ($request->has('selectedMatID') && $request->selectedMatID != 0) {
            $showCarrera = Carrera::find(Materia::find($request->selectedMatID)->carrera_id);
            // dd($request->selectedUni);
            $materiasid = Materia::has('unidads')->where('id', $request->selectedMatID)->pluck('id')->toArray();
            $unidads->whereIn('materia_id', $materiasid);
        }
        if ($request->has('search')) {
            $unidads->where('descripcion', 'LIKE', "%" . $request->search . "%");
            // $unidads->whereMonth('descripcion', $request->search);
            // $unidads->OrwhereMonth('fecha_fin', $request->search);
            $unidads->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            // dd($request->field);
            $unidads->orderBy($request->field, $request->order);
        } else {
            $unidads->orderBy('materia_id')->orderBy('enum')->orderBy('nombre');
        }

        return $showCarrera;
    }

    public function MapearClasePP(&$unidads, $numberPermissions) {
        $MateriasUser = Auth::user()->materias()->pluck('materias.id')->toArray();
        $unidads = $unidads->get()->map(function ($Unidad) use ($numberPermissions, $MateriasUser) {
            if ($numberPermissions < 2) {
                if (!in_array($Unidad->materia_id, $MateriasUser)) return null;
            }
            $Unidad->hijo = $Unidad->materia_nombre();
            $Unidad->cuantosTemas = $Unidad->subtopicos()->count();
            return $Unidad;
        })->filter();
    }

    public function fNombresTabla($numberPermissions) {
        if ($numberPermissions > 1)
            $nombresTabla = [ //0: como se ven //1 como es la BD //2orden
                ["Acciones"],
                [],
                [null]
            ];
        else {
            $nombresTabla = [ //0: como se ven //1 como es la BD //2orden
                [],
                [],
                []
            ];
        }
        $nombresTabla[2] = array_merge($nombresTabla[2], ["enum", "nombre", "materia_id"]);
        $nombresTabla[0] = array_merge($nombresTabla[0], ["#", "nombre", "materia"]);
        return $nombresTabla;
    }


    public function losSelect($numberPermissions) {
        // coordinador_academico = 4 | coorPrograma = 3 , profe ,estudiante
        if ($numberPermissions < (int)(env('PERMISS_VER_FILTROS_SELEC'))) { //5
            $MateriasSelect = Auth::user()->materias;
        } else {
            $MateriasSelect = Materia::all();
        }
        return [
            'MateriasSelect' => $MateriasSelect
        ];
    }


    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, ' unidads', '');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.unidads');
        $unidads = Unidad::query();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $showCarrera = null;
        if ($permissions !== "estudiante") {
            $showCarrera = $this->Filtros($request, $unidads, $numberPermissions);
        }
        $nombresTabla = $this->fNombresTabla($numberPermissions);
        $this->MapearClasePP($unidads, $numberPermissions);

        $Select = $this->losSelect($numberPermissions);
        // dd($unidads);
        $page = request('page', 1); // Current page number
        $total = $unidads->count();
        $paginated = new LengthAwarePaginator(
            $unidads->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('Unidad/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [['label' => __('app.label.unidads'), 'href' => route('Unidad.index')]],
            'nombresTabla'   =>  $nombresTabla,
            'selectedMatID'   =>  $request->selectedMatID,
            'MateriasSelect'   =>  $Select['MateriasSelect'],
            'numberPermissions'   =>  $numberPermissions,
            'showCarrera'   =>  $showCarrera,
        ]);
    } //fin index

    public function create()
    {
    }
    public function store(UnidadRequest $request)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        if ($request->enum) {
            $enum = $request->enum;
        } else {
            $modelInstance = resolve('App\\Models\\' . $this->modelName);
            $enum = (int)($modelInstance::latest('enum')->first()->enum) + 1 ?? 1;
        }

        try {
            $Unidad = Unidad::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => $request->descripcion,
                'materia_id' => $request->materia_id,
                'enum' => $enum,
                'codigo' => $request->codigo
            ]);

            if ($request->nsubtemas) {
                $enum = Myhelp::getPropertieAutoIncrement('Subtopico', null, 'enum', 'unidad_id', $Unidad->id);
                foreach ($request->subtema as $key => $value) {
                    if ($value) {
                        Subtopico::create([
                            'enum' => $enum,
                            'nombre' => $value,
                            'unidad_id' => $Unidad->id,
                            'resultado_aprendizaje' => $request->resultAprendizaje[$key],
                        ]);
                        $enum++;
                    }
                };
            }

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo Unidad " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $Unidad->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar Unidad " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Unidad')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function show(Unidad $Unidad)
    {
    }
    public function edit(Unidad $Unidad)
    {
    }

    public function update(UnidadRequest $request, $id)
    {
        $Unidad = Unidad::find($id);
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||Unidad|| ');

        try {
            // dd($Unidad,$request->nombre);
            $Unidad->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'materia_id' => $request->materia_id,
                'enum' => $request->enum,
                'codigo' => $request->codigo
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo Unidad " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Unidad->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar Unidad " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Unidad->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    // public function destroy(Unidad $Unidad)
    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();

        try {
            $unidads = Unidad::findOrFail($id);
            Myhelp::EscribirEnLog($this, get_called_class(), "La Unidad id:" . $id . " y nombre:" . $unidads->nombre . " ha sido borrada correctamente", false);


            $unidads->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $unidads->nombre]));
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                Log::info("U -> " . Auth::user()->name . " fallo en borrar Unidad " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
                DB::rollback();
                return back()->with('warning', 'Debe borrar los subtopicos asociados a esta unidad antes de proceder. ' . $th->getMessage());
            } else {
                Log::alert("U -> " . Auth::user()->name . " fallo en borrar Unidad " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
                DB::rollback();
                return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.unidads')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            }
        }
    }
    public function destroyBulk(Request $request) {
        $arrayMensaje = ModelFunctions::destroyBulkHelper($this->modelName,$request->id);
        return back()->with($arrayMensaje[0], $arrayMensaje[1]);
    }

    //todo: borrar y dejar el que es
    public function temasCreate()
    {
        return 'Un subtopico de ejemplo';
    }

    public  function temasCreate2(Request $request)
    {
        $usuario = Auth::user();
        $materia = Materia::find($request->materiaid);
        $materia->TodosObjetivos = $materia->objetivos();

        $limite = $usuario->limite_token_general;
        $tresEjercicios = session('tresEjercicios');
        $restarAlToken = 0;
        $respuesta = 'Probando';
        if ($limite > 0) {
            $gpt = $this->gptPart($request, $materia, $usuario);
            $respuesta = preg_replace("/^\n\n/", "", $gpt[0]);
            $restarAlToken = $gpt[1];
        } else { //no le quedan mas tokens
            $respuesta = $this->respuestaLimite;
        }

        return response()->json(['generatedText' => $respuesta]);
    }

    public function gptPart($request, $materia, $usuario, $productio = true){ //productio is for debugging
        if ($productio) {

            $plantillaPracticar = 'Ejercicios para practicar';
            $elprompt = 'Eres un academico, experto en la asignatura de ' . $materia->nombre . ' con años de experiencia enseñandola,
                responda el siguiente ejercicio: ' . $request->pregunta
                . '. La respuesta debe tener el nivel de un estudiante ' . $request->nivel
                . '. Antes de resolver la pregunta, genera un contexto, si es posible, de entre 20 y 40 palabras. Cuando finalices el contexto, deja un renglon vacio.'
                . '. Al finalizar la respuesta. sujiere 3 ejercicios para preguntarle a una inteligencia artificial(ponle de titulo ' . $plantillaPracticar . ') y seguir aprendiendo de '
                . $materia->nombre;
            $result = JustChatFunctionGPT::Chat4($elprompt);
            $respuesta = $result[1];
            $finishingReason = $result[2];
            dd($result);
            if ($finishingReason === 'stop') {
                $usageRespuesta = (int)($result['usage']["completion_tokens"]); //~ 260
                $usageRespuestaTotal = (int)($result['usage']["total_tokens"]); //~ 500

                $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);
                $usuario->update(['limite_token_general' => ((int)($usuario->limite_token_general)) - $restarAlToken]);
                return [$respuesta, $restarAlToken];
            } else {
                if ($finishingReason === 'length') {
                    return [$this->respuestaLarga, 0];
                } else {
                    return ['El servicio no esta disponible', 0];
                }
            }
        }
        return ['GPT desabilitado', 0];
    }
}
