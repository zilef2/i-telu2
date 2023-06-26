<?php

namespace App\Http\Controllers;

use App\helpers\HelpGPT;
use App\helpers\Myhelp;
use Inertia\Inertia;

use App\Models\tema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\TemaRequest;
use App\Models\Materia;
use GuzzleHttp\Promise\Coroutine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenAI;

class TemasController extends Controller
{
    public $respuestaLimite = 'Limite de tokens';
    public $respuestaLarga = 'La respuesta es demasiado extensa';
    public $MAX_USAGE_RESPUESTA = 550;
    public $MAX_USAGE_TOTAL = 600;

    public function fNombresTabla($numberPermissions) {
        if($numberPermissions < 2) { //estudiante

            $nombresTabla =[//0: como se ven //1 como es la BD //2??
                ["Acciones","#"],
                [],
                [null,null,null]
            ];
            array_push($nombresTabla[0], "nombre", "observaciones");
            //m for money || t for datetime || d date || i for integer || s string || b boolean
            $nombresTabla[1][] = ["s_nombre", "s_descripcion"];
            //se puede ordenar?
            $nombresTabla[2][] = ["s_nombre", "s_descripcion"];
        }
        if($numberPermissions < 3){ //profesor

            array_push($nombresTabla[0], 'materia');
            $nombresTabla[1][] = 'i_materia_id';
            $nombresTabla[2][] = '';
            // $nombresTabla[2][] = [];
        }
        // dd($nombresTabla);
        //coordinador_academico
        // coordinador_de_programa

        return $nombresTabla;
    }
    public function Filtros($request, &$temas) {
        //todo: validar que un estudiante no vea todos los temas de otras universidades
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
    }
    public function losSelect() {
        //todo:solo las materias asociadas
        $MateriasSelect = Materia::all();
        return [
            'MateriasSelect' => $MateriasSelect
        ];
    }


    public function index(Request $request) {
        if(Auth::user()->isAdmin < 1){
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            // log::channel('soloadmin')->info('Vista:' . $nombreC. '|  U:'.Auth::user()->name );
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||tema|| ' );
        }

        $titulo = __('app.label.temas');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $temas = tema::query();
        
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        if($permissions === "estudiante") {

            $nombresTabla = $this->fNombresTabla($numberPermissions);
        }else{ // not estudiante
            //todo: si es profe, solo muestre los temas de sus materias
            $this->Filtros($request,$temas);
            $nombresTabla = $this->fNombresTabla($numberPermissions);
            
        }

        //CalcularClasePrincipal
        
        $temas = $temas->get()->map(function ($tema){
            $tema->hijo = $tema->materia_nombre();
            return $tema;
        });

        $Select = $this->losSelect();
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

            'MateriasSelect'   =>  $Select['MateriasSelect'],
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
                'descripcion' => $request->descripcion,
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
                'descripcion' => $request->descripcion,
                'materia_id' => $request->materia_id,
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
    

    public function temasCreate() {
        return 'Un subtopico de ejemplo';
    }

    public  function temasCreate2(Request $request) {
        $usuario = Auth::user();
        // dd(
        //     $request->id,
        //     $request[0],
        //     $request[1],
        //     $request
        // );

            $materia = materia::find($request->materiaid);
            $materia->TodosObjetivos = $materia->objetivos();

            $limite = $usuario->limite_token_general;
            $tresEjercicios = session('tresEjercicios');
            $restarAlToken = 0;
            $respuesta = 'Probando';
            if($limite > 0) {
                $gpt = $this->gptPart($request,$materia,$usuario);
                $respuesta = preg_replace("/^\n\n/", "", $gpt[0]);
                $restarAlToken = $gpt[1];
            }else{//no le quedan mas tokens
                $respuesta = $this->respuestaLimite;
            }

            return response()->json(['generatedText' => $respuesta]);
    }

    public function gptPart($request,$materia,$usuario, $productio=true ){ //productio is for debugging
        if($productio) {

            $plantillaPracticar = 'Ejercicios para practicar';
            $client = OpenAI::client(env('GTP_SELECT'));
            $result = $client->completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => 'Eres un academico, experto en la asignatura de '.$materia->nombre.' con años de experiencia enseñandola,
                        responda el siguiente ejercicio: '.$request->pregunta
                        .'. La respuesta debe tener el nivel de un estudiante '.$request->nivel
                        .'. Antes de resolver la pregunta, genera un contexto, si es posible, de entre 20 y 40 palabras. Cuando finalices el contexto, deja un renglon vacio.'
                        .'. Al finalizar la respuesta. sujiere 3 ejercicios para preguntarle a una inteligencia artificial(ponle de titulo '.$plantillaPracticar.') y seguir aprendiendo de '.$materia->nombre,
                'max_tokens' => 600 // Adjust the response length as needed
            ]);
            $respuesta = $result['choices'][0]["text"];

            $finishReason = $result['choices'][0];
            $finishingReason = $finishReason["finish_reason"] ?? '';
            // dd($respuesta,$finishReason,$finishingReason,$request->nivel);


            // $respuesta = 'a';
            // $finishingReason ='length';
            // $result['usage']["completionTokens"] = 100;

            if($finishingReason == 'stop'){
                // dd($result['usage']);
                    $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                    $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500
                    
                    $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta,$usageRespuestaTotal);
                    $usuario->update([ 'limite_token_general' => (intval($usuario->limite_token_general)) - $restarAlToken ]);
                    return [$respuesta,$restarAlToken];
            }else{
                if($finishingReason == 'length'){
                    return [$this->respuestaLarga,0];
                }else{
                    return ['El servicio no esta disponible',0];
                }
            }
        }
        return ['GPT desabilitado',0];
        
    }
}
