<?php

namespace App\Http\Controllers;

use App\helpers\HelpGPT;
use OpenAI;
use App\Models\User;

use Inertia\Inertia;
use App\helpers\Myhelp;
use App\helpers\WolframAlphaService;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Universidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\MateriumRequest;
use App\Models\Ejercicio;
use App\Models\Subtopico;
use App\Models\Tema;
use Illuminate\Pagination\LengthAwarePaginator;
// use OpenAI\Exceptions\ErrorException;

class MateriasController extends Controller {

    public $respuestaLimite = 'Limite de tokens';
    public $respuestaLarga = 'La respuesta es demasiado extensa';
    public $MAX_USAGE_RESPUESTA = 550;
    public $MAX_USAGE_TOTAL = 600;


    //! index functions ()

        public function NombresTabla($estudiante) {
                
            $nombresTabla =[//0: como se ven //1 como es la BD //2orden
                ["Acciones","#"],
                [],
                [null,null,null]
            ];

            if($estudiante){
                //todo: $nombresTabla[0][] = ["nombre", "observaciones"];
                $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","carrera","Temas","usuarios","Requisitos","Objetivos","descripcion"]);
                $nombresTabla[2] = array_merge($nombresTabla[2] , ["nombre","carrera_id","", "","","descripcion"]);


            }else{//not estudiante
                //todo: funcion order not working
                $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","carrera","Temas","usuarios","Requisitos","Objetivos","descripcion"]);
                //campos ordenables
                $nombresTabla[2] = array_merge($nombresTabla[2] , ["nombre","carrera_id","", "","","descripcion"]);
            }

            return $nombresTabla;
        }
        public function MapearClasePP(&$materias) {
            $materias = $materias->get()->map(function ($materia){
                $materia->papa = $materia->carrera_nombre();
                $materia->cuantoshijos = count($materia->temas);

                $materia->muchos = $materia->users_nombres();

                $materia->r1 = $materia->requisito1_nombre();
                $materia->r2 = $materia->requisito2_nombre();
                $materia->r3 = $materia->requisito3_nombre();
                $exister1 = $materia->r1 == '' ? 0 : 1;
                $exister2 = $materia->r2 == '' ? 0 : 1;
                $exister3 = $materia->r3 == '' ? 0 : 1;
                $sumaRequisitos = $exister1 + $exister2 + $exister3;
                $materia->numRequisitos = $sumaRequisitos == 0 ? 'Sin requisitos' : $sumaRequisitos;

                $materia->objetivs = $materia->objetivos();
                return $materia;
            });
            // dd($materias);
        }
        public function Filtros($request, &$materias) {
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
       


    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this,' materia');
        
        $titulo = __('app.label.materias');
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $materias = Materia::query();
        if($permissions === "estudiante") {
            $nombresTabla = $this->NombresTabla(1);
            
        }else{ // not estudiante

            $titulo = 'materia';
            $this->Filtros($request,$materias);
            $perPage = $request->has('perPage') ? $request->perPage : 10;
            $nombresTabla = $this->NombresTabla(0);
        }

        //hijos materias 
        $this->MapearClasePP($materias);

        $carrerasSelect = $MateriasRequisitoSelect = $UniversidadSelect = null;
        $this->losSelect($carrerasSelect, $MateriasRequisitoSelect, $UniversidadSelect,$request,$materias);
        
        $page = request('page', 1); // Current page number
        $total = $materias->count();
        $paginated = new LengthAwarePaginator(
            $materias->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $errorMessage = '';
        return Inertia::render('materia/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order','selectedUni','selectedcarr']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
            'nombresTabla'   =>  $nombresTabla,
            'errorMessage' => $errorMessage,
            'carrerasSelect' => $carrerasSelect,
            'MateriasRequisitoSelect' => $MateriasRequisitoSelect,
            'UniversidadSelect' => $UniversidadSelect,
        ]);
    }//fin index


    //! STORE & SHOW & UPDATE & DESTTROY
        public function store(MateriumRequest $request) {
            DB::beginTransaction();
            Myhelp::EscribirEnLog($this,get_called_class(),'',false);


            try {
                if($request->cuantosReq != 0){
                    $req1 = $request->cuantosReq > 0 && $request->requisito1 != '' ? intval($request->requisito1) : null;
                    $req2 = $request->cuantosReq > 1 && $request->requisito2 != '' ? intval($request->requisito2) : null;
                    $req3 = $request->cuantosReq > 2 && $request->requisito3 != '' ? intval($request->requisito3) : null;
                }
                $materia = Materia::create([
                    'nombre' => $request->nombre,
                    //otrosCampos
                    'descripcion' => $request->descripcion,
                    'carrera_id' =>  $request->carrera_id,
                    'req1_materia_id' => $req1 ?? null,
                    'req2_materia_id' => $req2 ?? null,
                    'req3_materia_id' => $req3 ?? null,
                    'objetivo1' => $request->objetivo[0] ?? null,
                    'objetivo2' => $request->objetivo[1] ?? null,
                    'objetivo3' => $request->objetivo[2] ?? null,
                ]);
                DB::commit();
                Log::info("U -> ".Auth::user()->name." Guardo materia ".$request->nombre." correctamente");
                return back()->with('success', __('app.label.created_successfully2', ['nombre' => $materia->nombre]));
            } catch (\Throwable $th) {
                DB::rollback();
                Log::alert("U -> ".Auth::user()->name." fallo en Guardar materia ".$request->nombre." - ".$th->getMessage());

                return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.materia')]) . $th->getMessage());
            }
        }

        public function show($id) { 
            $materia = Materia::find($id);
            $temas = $materia->temas;

            foreach ($temas as $temaKey => $tema) {
                $tema->sub = $tema->subtopicos;
                $ArrayEjercicios = [];
                foreach ($tema->sub as $key => $subtopico) {
                    $ArrayEjercicios[$key] = $subtopico->ejercicios->pluck('id','nombre')->toArray();
                }
                $tema->sub->ejercis = $ArrayEjercicios;
            }

            return Inertia::render('materia/show', [ //carpeta
                'title'          =>  'Ver materia',
                // 'filters'        =>  $request->all(['search', 'field', 'order','selectedUni','selectedcarr']),
                'fromController' =>  $materia,
                'temas' =>  $temas,
                'breadcrumbs'    =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
            ]);
        }
        public function edit(materia $materia) { }

        public function update(Request $request, $id) {
            // dd($request);
            $materia = Materia::find($id);
            DB::beginTransaction();
            Myhelp::EscribirEnLog($this,get_called_class(),'',false);
            if($request->cuantosReq != 0){
                $req1 = $request->cuantosReq > 0 && $request->requisito1 != '' ? intval($request->requisito1) : null;
                $req2 = $request->cuantosReq > 1 && $request->requisito2 != '' ? intval($request->requisito2) : null;
                $req3 = $request->cuantosReq > 2 && $request->requisito3 != '' ? intval($request->requisito3) : null;
            }
            try {
                $materia->update([
                    'nombre' => $request->nombre,
                    //otrosCampos
                    'descripcion' => $request->descripcion,
                    'carrera_id' =>  $request->carrera_id,
                    'req1_materia_id' => $req1 ?? null,
                    'req2_materia_id' => $req2 ?? null,
                    'req3_materia_id' => $req3 ?? null,
                    'objetivo1' => $request->objetivo[0] ?? null,
                    'objetivo2' => $request->objetivo[1] ?? null,
                    'objetivo3' => $request->objetivo[2] ?? null,
                ]);

                DB::commit();
                Log::info("U -> ".Auth::user()->name." actualizo materia ".$request->nombre." correctamente");

                return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $materia->nombre]));
            } catch (\Throwable $th) {
                
                DB::rollback();
                Log::alert("U -> ".Auth::user()->name." fallo en actualizar materia ".$request->nombre." - ".$th->getMessage());
                return back()->with('error', __('app.label.updated_error', ['nombre' => $materia->nombre]) . $th->getMessage());
            }
        }

        
        public function destroy($id) {
            Myhelp::EscribirEnLog($this,get_called_class(),'',false);

            DB::beginTransaction();

            try {
                $materias = Materia::findOrFail($id);
                Myhelp::EscribirEnLog($this,get_called_class(),
                    "La materia id:".$id." y nombre:".$materias->nombre." ha sido borrada correctamente",false);
                
                
                $materias->delete();
                DB::commit();
                return back()->with('success', __('app.label.deleted_successfully2',['nombre' => $materias->nombre]));
                
            } catch (\Throwable $th) {
                DB::rollback();
                Log::alert("U -> ".Auth::user()->name." fallo en borrar materia ".$id." - ".$th->getMessage());
                return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.materias')]) . $th->getMessage());
            }
        }
    //STORE & UPDATE & DESTROY


    
    public function AsignarUsers(Request $request, $materiaid){
        $titulo = 'Seleccione los estudiantes a matricular';
        $permissions = Myhelp::EscribirEnLog($this,'carrera');
        if($permissions === "estudiante") {
            return back()->with('error', __('app.label.no_permission'));
        } else { // not estudiante
            // $nombresTabla = $this->NombresTabla(0);
        }

        $materia = Materia::find($materiaid);
        $carrera = Carrera::find($materia->carrera_id);
        $universidad = Universidad::find($carrera->universidad_id);

        $users = User::query();

        $filtroUser = $this->UsuariosSinLosInscritos($materia,$carrera);

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

        return Inertia::render('materia/AsignarUsers', [ //carpeta
            'title'          =>  $titulo,
            'breadcrumbs'    =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
            'filters'       => $request->all(['search']),

            'usuariosPorInscribir' =>  $users->get(),

            'universidad' =>  $universidad,
            'carrera' =>  $carrera,
            'materia' => $materia,
            'inscritos' => $materia->users,
        ]);
    }

    public function UsuariosSinLosInscritos($modelo,$carrera) {
        $usuariosU = $carrera->users->pluck('id');
        $usuariosDeLaMateria = $modelo->users->pluck('id');

        return (object) [
            'si'=>$usuariosU,
            'no'=>$usuariosDeLaMateria
        ];
    }
        

    public function SubmitAsignarUsers(Request $request) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this,' materia');

        try {
            $materia = Materia::find($request->materiaid);
            // dd($request->selectedId);
            $materia->users()->attach(
                $request->selectedId
            );

            DB::commit();
            Log::info("U -> ".Auth::user()->name." matriculo a la materia ".count($request->selectedId)." estudiantes correctamente");

            return redirect()->route('materia.index')->with('success', __('app.label.created_success'));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en matricular(materia) - ".$th->getMessage());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage());
        }
    }

    public function lookForTemas($materiaid) {
        $materia = Materia::find($materiaid);
        $temas = $materia->temas;
        foreach ($temas as $key => $value) {
            // $temas[$key]->preguntas = 'asd';
            $temas[$key]->mat = $value->materia_nombre();
            $subtopicos = $value->subtopicos;
            foreach ($subtopicos as $subkey => $subtopic) {
                $subtopicos[$subkey]->ejer = $subtopic->ejercicios;
                // dd($subtopic->ejercicios);
            }
            $temas[$key]->sub = $subtopicos;
        }
        // dd($temas);


        // $valoresSelect = [];
        // $valoresSelect[] = [ 'label' => $objetivo, 'value' => 1];
        // $valoresSelect[] = [ 'label' => 'Intermedio', 'value' => 2];
        // $valoresSelect[] = [ 'label' => 'Preparacion', 'value' => 3];
        return [$temas, null];
    }









    private function gptPart1($pregunta,$nivel,$materia_nombre,$usuario,&$soloEjercicios, $debug=false ){

        if($debug) {
            $plantillaPracticar = 'Ejercicios para practicar';

            $client = OpenAI::client(env('GTP_SELECT'));
            $result = $client->completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => 'Eres un academico, experto en la asignatura de '.$materia_nombre.' con años de experiencia enseñandola,
                        responda el siguiente ejercicio: '.$pregunta
                        .'. La respuesta lo debe entender un estudiante'.$nivel
                        .'. Antes de resolver la pregunta, genera un contexto, si es posible, de entre 20 y 40 palabras. Cuando finalices el contexto, deja un renglon vacio.'
                        .'. Al finalizar la respuesta. sugiere 3 ejercicios para preguntarle a una inteligencia artificial(ponle de titulo '.$plantillaPracticar.') y seguir aprendiendo de '.$materia_nombre,
                'max_tokens' => HelpGPT::maxToken() // Adjust the response length as needed
            ]);
            $respuesta = $result['choices'][0]["text"];
            $finishReason = $result['choices'][0];
            $finishingReason = $finishReason["finish_reason"] ?? '';
            
            
            // $respuesta = 'wenas';
            // $finishingReason = 'stop';

            if($finishingReason == 'stop'){
                // dd($result['usage']);
                    $usageRespuesta = 260;
                    $usageRespuestaTotal = 500;
                    $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                    $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500
                    
                    $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta,$usageRespuestaTotal);
                    $usuario->update([ 'limite_token_leccion' => (intval($usuario->limite_token_leccion)) - $restarAlToken ]);

                    $soloEjercicios = HelpGPT::ApartarSujerencias($respuesta,$plantillaPracticar);
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
    public function VistaTema($materiaid,$ejercicioid = null,$idnivel=null) { //$ejercicioid
    // $wolf = WolframAlphaService::query('integral of x^2');
        $vectorYSelecNiveles = HelpGPT::nivelesAplicativo();
        if($idnivel){

            $ChosenNivel = $vectorYSelecNiveles[0][$idnivel];
        }else{
            $ChosenNivel = '';
        }

        $usuario = Auth::user();

        $restarAlToken = 0;
        set_time_limit(180);

        
        $materia = Materia::find($materiaid);
        $limite = $usuario->limite_token_leccion;
        
        if($ejercicioid != null){
            $ejercicio = Ejercicio::find($ejercicioid);
            $ejercicioSelec = $ejercicio->nombre;

            // $temaSelec = $materia->Tsubtemas;
            // $subtopicoSelec = $materia->temas;
            $subtopicoSelec = Subtopico::find($ejercicio->subtopico_id);
            $temaSelec = Tema::find($subtopicoSelec->tema_id)->nombre;
            $subtopicoSelec = $subtopicoSelec->nombre;
            if($limite > 0) {
                $soloEjercicios = '';

                $gpt = $this->gptPart1($ejercicioSelec, $ChosenNivel,$materia->nombre,$usuario,$soloEjercicios,true);
                $respuesta = preg_replace("/^\n\n/", "", $gpt[0]);
                
                $restarAlToken = $gpt[1];
                $limite = $usuario->limite_token_leccion;
            }else{//no le quedan mas tokens
                $respuesta = $this->respuestaLimite;
            }
        }else{
            $respuesta = '';
        }

        $temasYValores = $this->lookForTemas(intval($materiaid));
        $nivelSelect = $vectorYSelecNiveles[1];

        set_time_limit(70);

        return Inertia::render('materia/vistaTem', [ //carpeta
            'elid'              =>  intval($materiaid),
            'title'             =>  'Seleccione una leccion',
            'perPage'           =>  10,
            'fromController'    =>  $temasYValores[0],
            'respuesta'         =>  $respuesta,
            'objetivosCarrera'  =>  $materia->objetivos(),
            'temaSelec'         => $temaSelec ?? 'Aqui vera el tema',
            'subtopicoSelec'    => $subtopicoSelec ?? 'Aqui vera el subtopico',
            'ejercicioSelec'    => $ejercicioSelec ?? 'Aqui vera el ejercicio/pregunta',
            'limite'            => $limite,
            'usuario'           => $usuario,
            'materia'           => $materia,
            'restarAlToken'    => $restarAlToken,
            'nivelSelect'       => $nivelSelect,
            'ChosenNivel'       => $ChosenNivel,
            'soloEjercicios'    => $soloEjercicios ?? '',
            'breadcrumbs'       =>  [['label' => __('app.label.materias'), 
                                    'href' => route('materia.index')]],

        ]);
    }

    

    


    // public function masPreguntas($id,$nuevaPregunta) {
    public function masPreguntas(Request $request) {
        $usuario = Auth::user();
        // dd(
        //     $request->id,
        //     $request[0],
        //     $request[1],
        //     $request
        // );
        if(isset($request->materiaid) && $request->materiaid != null){

            $materia = Materia::find($request->materiaid);
            $materia->TodosObjetivos = $materia->objetivos();

            $limite = $usuario->limite_token_general;
            $tresEjercicios = session('tresEjercicios');
            $restarAlToken = 0;
            $respuesta = 'Probando';
            if($limite > 0) {
                $gpt = $this->gptPart($request,$materia,$usuario); //todo: usar gptpart 1
                $respuesta = preg_replace("/^\n\n/", "", $gpt[0]);
                $restarAlToken = $gpt[1];
            }else{//no le quedan mas tokens
                $respuesta = $this->respuestaLimite;
            }

            return Inertia::render('materia/masPreguntas', [ //carpeta
                'nuevaPregunta'     => $request->pregunta,
                'respuesta'         =>  $respuesta,
                'restarAlToken'     =>  $restarAlToken,

                'materia'           => $materia,
                'nivel'             => $request->nivel ?? 'Bachiller',
                'title'             =>  'Seccion de repaso general',
                'perPage'           =>  10,
                'restarAlToken'     => $restarAlToken,
                'tresEjercicios'    => $tresEjercicios,
                'limite'            => $limite,
                'breadcrumbs'       =>  [['label' => __('app.label.materias'), 
                                        'href' => route('materia.index')]],
            ]);
        }else{
            return Inertia::render('materia/masPreguntas', [
                'nuevaPregunta'    => '',
                'respuesta'         =>  '',

                'materia'           => null,
                'nivel'             => $request->nivel ?? 'Bachiller',
                'title'             =>  'Seccion de repaso general',
                'restarAlToken'     => null,
                'tresEjercicios'    => null,
                'limite'            => null,
                'breadcrumbs'       =>  [['label' => __('app.label.materias'), 
                                        'href' => route('materia.index')]],
            ]);
        }

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
                    $soloEjercicios = HelpGPT::ApartarSujerencias($respuesta,$plantillaPracticar);

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
    //fin gpt
}
