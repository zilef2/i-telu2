<?php

namespace App\Http\Controllers;

use App\helpers\HelpGPT;
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

class UnidadsController extends Controller
{
    public $respuestaLimite = 'Limite de tokens';
    public $respuestaLarga = 'La respuesta es demasiado extensa';
    public $MAX_USAGE_RESPUESTA = 550;
    public $MAX_USAGE_TOTAL = 600;

    public function MapearClasePP(&$unidads, $numberPermissions) {
        $unidads = $unidads->get()->map(function ($Unidad) use ($numberPermissions) {
            if ($numberPermissions < 2) {
                $MateriasUser = Auth::user()->materias()->pluck('materias.id')->toArray();
                if (!in_array($Unidad->materia_id, $MateriasUser)) return null;
            }
            $Unidad->hijo = $Unidad->materia_nombre();
            return $Unidad;
        })->filter();
    }

    public function fNombresTabla($numberPermissions) {
        $nombresTabla = [ //0: como se ven //1 como es la BD //2??
            ["Acciones", "#"],
            [],
            [null, null, null]
        ];
        // if($numberPermissions < 2) { //estudiante
        //     array_push($nombresTabla[0], "nombre","materia", "descripcion");
        //     $nombresTabla[2][] = ["s_nombre","s_materia", "s_descripcion"];
        // }

        array_push($nombresTabla[0], "nombre", "materia", "descripcion");
        $nombresTabla[2][] = ["s_nombre", "s_materia", "s_descripcion"];

        // if($numberPermissions < 3){ //profesor
        // }
        //coordinador_academico
        // coordinador_de_programa

        return $nombresTabla;
    }
    public function Filtros($request, &$unidads, $numberPermissions) {
        $showCarrera = null;
        if ($request->has('selectedMatID') && $request->selectedMatID != 0) {
            $showCarrera = Carrera::find(Materia::find($request->selectedMatID)->carrera_id);
            // dd($request->selectedUni);
            $materiasid = Materia::has('unidads')->where('id', $request->selectedMatID)->pluck('id')->toArray();
            $unidads->whereIn('materia_id', $materiasid);
        }
        //todo: validar que un estudiante no vea todos las unidades de otras universidades
        if ($request->has('search')) {
            $unidads->where('descripcion', 'LIKE', "%" . $request->search . "%");
            // $unidads->whereMonth('descripcion', $request->search);
            // $unidads->OrwhereMonth('fecha_fin', $request->search);
            $unidads->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $unidads->orderBy($request->field, $request->order);
        } else {
            $unidads->orderBy('nombre');
        }

        return $showCarrera;
    }
    public function losSelect($numberPermissions) {
        if($numberPermissions < intval(env('PERMISS_VER_FILTROS_SELEC'))){ //coorPrograma,profe,estudiante
            $MateriasSelect = Auth::user()->materias;
        }else{
            $MateriasSelect = Materia::all();
        }
        return [
            'MateriasSelect' => $MateriasSelect
        ];
    }


    public function index(Request $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, ' unidads', '');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.unidads');
        $unidads = Unidad::query();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $showCarrera = null;
        if ($permissions === "estudiante") {

            $nombresTabla = $this->fNombresTabla($numberPermissions);
        } else { // not estudiante
            //todo: si es profe, solo muestre los unidads de sus materias
            $showCarrera = $this->Filtros($request, $unidads, $numberPermissions);
            $nombresTabla = $this->fNombresTabla($numberPermissions);
        }
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

        try {
            $Unidad = Unidad::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => $request->descripcion,
                'materia_id' => $request->materia_id,
            ]);

            if ($request->nsubtemas) {
                foreach ($request->subtema as $key => $value) {
                    if ($value) Subtopico::create([
                        'nombre' => $value,
                        'unidad_id' => $Unidad->id,
                        'resultado_aprendizaje' => $request->resultAprendizaje[$key],
                    ]);
                };
            }

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo Unidad " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $Unidad->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar Unidad " . $request->nombre . " - " . $th->getMessage());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Unidad')]) . $th->getMessage());
        }
    }

    public function show(Unidad $Unidad)
    {
    }
    public function edit(Unidad $Unidad)
    {
    }

    public function update(Request $request, $id)
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
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo Unidad " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Unidad->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar Unidad " . $request->nombre . " - " . $th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Unidad->nombre]) . $th->getMessage());
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
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar Unidad " . $id . " - " . $th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.unidads')]) . $th->getMessage());
        }
    }

    //todo: borrar y dejar el que es
    public function temasCreate()
    {
        return 'Un subtopico de ejemplo';
    }

    public  function temasCreate2(Request $request)
    {
        $usuario = Auth::user();
        // dd(
        //     $request->id,
        //     $request[0],
        //     $request[1],
        //     $request
        // );

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

    public function gptPart($request, $materia, $usuario, $productio = true)
    { //productio is for debugging
        if ($productio) {

            $plantillaPracticar = 'Ejercicios para practicar';
            $client = OpenAI::client(env('GTP_SELECT'));
            $result = $client->completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => 'Eres un academico, experto en la asignatura de ' . $materia->nombre . ' con años de experiencia enseñandola,
                        responda el siguiente ejercicio: ' . $request->pregunta
                    . '. La respuesta debe tener el nivel de un estudiante ' . $request->nivel
                    . '. Antes de resolver la pregunta, genera un contexto, si es posible, de entre 20 y 40 palabras. Cuando finalices el contexto, deja un renglon vacio.'
                    . '. Al finalizar la respuesta. sujiere 3 ejercicios para preguntarle a una inteligencia artificial(ponle de titulo ' . $plantillaPracticar . ') y seguir aprendiendo de ' . $materia->nombre,
                'max_tokens' => 600 // Adjust the response length as needed
            ]);
            $respuesta = $result['choices'][0]["text"];

            $finishReason = $result['choices'][0];
            $finishingReason = $finishReason["finish_reason"] ?? '';
            // dd($respuesta,$finishReason,$finishingReason,$request->nivel);
            if ($finishingReason == 'stop') {
                // dd($result['usage']);
                $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500

                $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);
                $usuario->update(['limite_token_general' => (intval($usuario->limite_token_general)) - $restarAlToken]);
                return [$respuesta, $restarAlToken];
            } else {
                if ($finishingReason == 'length') {
                    return [$this->respuestaLarga, 0];
                } else {
                    return ['El servicio no esta disponible', 0];
                }
            }
        }
        return ['GPT desabilitado', 0];
    }
}
