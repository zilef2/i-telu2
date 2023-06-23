<?php

namespace App\Http\Controllers;

use App\helpers\HelpGPT;
use OpenAI;
use App\Models\User;

use Inertia\Inertia;
use App\helpers\Myhelp;
use App\Models\Carrera;
use App\Models\materia;
use App\Models\Universidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\MateriumRequest;
use Illuminate\Pagination\LengthAwarePaginator;
// use OpenAI\Exceptions\ErrorException;

class MateriasController extends Controller {

    public $respuestaLimite = 'Limite de tokens';
    public $respuestaLarga = 'La respuesta es demasiado extensa';
    public $MAX_USAGE_RESPUESTA = 550;
    public $MAX_USAGE_TOTAL = 600;


    public function CalcularClasePrincipal(&$materias) {
        $materias = $materias->get()->map(function ($materia){
            $materia->hijo = $materia->carrera_nombre();
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
        $MateriasRequisitoSelect = materia::all();
        $UniversidadSelect = Universidad::has('carreras')->get();
    }
    public function NombresTabla($estudiante) {
        
        $nombresTabla =[//0: como se ven //1 como es la BD //2orden
            ["Acciones","#"],
            [],
            [null,null,null]
        ];

        if($estudiante){

            $nombresTabla[0][] = ["nombre", "observaciones"];
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1][] = ["s_nombre", "s_descripcion"]; 
            //campos ordenables
            $nombresTabla[2][] = ["s_nombre", "s_descripcion"]; 


        }else{//not estudiante
            $nombresTabla[0] = array_merge($nombresTabla[0] , ["carrera","nombre","usuarios","Requisitos","Objetivos","descripcion"]);
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1] = array_merge($nombresTabla[1] , ["s_carrera_id","s_nombre", "s_usuarios","s_Requisitos","Objetivos","s_descripcion"]);
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2] , ["carrera_id","nombre", "","","descripcion"]);
        }

        return $nombresTabla;
    }


    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this,' materia');
        
        $titulo = __('app.label.materias');
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $materias = materia::query();
        if($permissions === "estudiante") {
            $nombresTabla = $this->NombresTabla(1);
            
        }else{ // not estudiante

            $titulo = 'materia';
            $this->Filtros($request,$materias);
            $perPage = $request->has('perPage') ? $request->perPage : 10;
            $nombresTabla = $this->NombresTabla(0);
        }

        //hijos materias 
        $this->CalcularClasePrincipal($materias);

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
            'breadcrumbs'    =>  [['label' => __('app.label.materias'), 
                                    'href' => route('materia.index')]],
            'nombresTabla'   =>  $nombresTabla,
            'errorMessage' => $errorMessage,
            'carrerasSelect' => $carrerasSelect,
            'MateriasRequisitoSelect' => $MateriasRequisitoSelect,
            'UniversidadSelect' => $UniversidadSelect,
        ]);
    }//fin index


    public function AsignarUsers(Request $request, $materiaid){
        $titulo = 'Seleccione los estudiantes a matricular';
        $permissions = Myhelp::EscribirEnLog($this,'carrera');
        if($permissions === "estudiante") {
            return back()->with('error', __('app.label.no_permission'));
        } else { // not estudiante
            // $nombresTabla = $this->NombresTabla(0);
        }

        $materia = materia::find($materiaid);
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
            $materia = materia::find($request->materiaid);
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

    public function lookForTemas($id) {
        $materia = Materia::find($id);
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

    public function VistaTema($id,$temaSelec = "", $subtopicoSelec = "", $ejercicioSelec = "") {
       
        //cuando entra - universidad - carrera
        //materia - tema
        //propuesta (intro)
        // dd( $temaSelec,$subtopicoSelec,$ejercicioSelec);
        $usuario = Auth::user();
        $materia = materia::find($id);
        
        $limite = $usuario->limite_token_leccion;
        $restarAlToken = 0;
        set_time_limit(120);

        if($limite > 0) {
                // 'Eres un experto en la materia universitaria: '.$pregunta.', se lo mas cordial posible. Propon 2 ejercicios, 1 muy sencillo y otro mas dificil, para estudiantes que desean estudiar para un parcial de la materia '.$pregunta.'. Antes de darles los ejercicios, dales un contexto de almenos 20 palabras.'
                // eres un academico con exp en la asignatura X con mas de 20 años, responda: X2 , con un nivel X3. con un nivel (Bachillerato, Universitario o posgrado)
                // 'Eres un experto en la materia universitaria: Fisica mecanica. se desea saber '.$ejercicioSelec.' para estudiantes. Antes de resolver la pregunta, genera un contexto, si es posible, de entre 20 y 40 palabras.',

            if($temaSelec != '') {
                $plantillaPracticar = 'Ejercicios para practicar';
                $client = OpenAI::client(env('GTP_SELECT'));
                $result = $client->completions()->create([
                    'model' => 'text-davinci-003',
                    'prompt' =>
                    'Eres un academico, experto en la asignatura de '.$materia->nombre.' con años de experiencia enseñandola, el tema es: '.$temaSelec.', el sub-tema es: '.$subtopicoSelec.' 
                            responda el siguiente ejercicio: '.$ejercicioSelec
                            .'. La respuesta debe tener el nivel de un estudiante '.$usuario->pgrado
                            .'. Antes de resolver la pregunta, genera un contexto, si es posible, de entre 20 y 40 palabras. Cuando finalices el contexto, deja un renglon vacio.'
                            .'. Al finalizar la respuesta. sujiere 3 ejercicios para preguntarle a una inteligencia artificial(ponle de titulo '.$plantillaPracticar.') y seguir aprendiendo de '.$temaSelec,
                    'max_tokens' => 723 // Adjust the response length as needed
                ]);
                // // dd($result);
                $respuesta = substr($result['choices'][0]["text"],2);

                $PP=[];
                $PP[0] = ["finish_reason" => 'stop'];
                // $PP[0] = $result['choices'][0] ?? 'fallo';
                // $PP[1] = $result['choices'][0]["finishReason"] ?? 'fallo finishReason';

                    $R_finishReason = $result['choices'][0]["finishReason"] ?? 'fallo';
                    $R_index = $result['choices'][0]["index"];
                    $R_logprobs = $result['choices'][0]["logprobs"];

                    // $usageEntrada = $result['usage']["promptTokens"]; //~  240
                    $usageRespuesta = $result['usage']["completion_tokens"]; //~ 260
                    $usageTotal = $result['usage']["total_tokens"]; //~ 500
                    $usageRespuesta = HelpGPT::CalcularTokenConsumidos($usageRespuesta,$usageTotal);

                    // $respuesta = '
                    // .
                    // La energía potencial es un concepto clave en la física ya que representa la cantidad de energía disponible que un cuerpo posee en un sistema conservativo debido a su posición en el espacio. En este caso, para un cuerpo de masa m=1 kg a una altura h=2 m, la energía potencial se puede calcular como Ep=m*g*h, donde g es la aceleración de la gravedad. La energía potencial en este caso es 1 * 9.81 m/s2 * 2 m = 19.62 J. ◀La energía potencial es un concepto clave en la física ya que representa la cantidad de energía disponible que un cuerpo posee en un sistema conservativo debido ▶
                    
                    // Ejercicios para preguntar a una IA: 
                    // 1. ¿Cuál es la fórmula para calcular la energía cinética? 
                    // 2. ¿Cómo se relaciona la energía cinética y la energía potencial? 
                    // 3. ¿Cuáles son algunas aplicaciones prácticas de la energía cinética y potencial?';

                $finishingReason = '';
                $soloEjercicios = $this->GenerarSujerencias($respuesta,$plantillaPracticar,$PP,$finishingReason);

                if($finishingReason != 'stop'){
                    $respuesta = 'El servicio no esta disponible';
                }else{
                    $usuario->update([ 'limite_token_leccion' => (intval($usuario->limite_token_leccion)) - $restarAlToken ]);
                }

            } else {
                $respuesta = 'Tema no selecionado';
            }
        }else{
            $respuesta = $this->respuestaLimite;
        }

        $temasYValores = $this->lookForTemas(intval($id));
        set_time_limit(70);

        return Inertia::render('materia/vistaTem', [ //carpeta
            'elid'              =>  intval($id),
            'title'             =>  'Seleccione una leccion',
            'perPage'           =>  10,
            'fromController'    =>  $temasYValores[0],
            'respuesta'         =>  $respuesta,
            'objetivosCarrera'  =>  $materia->objetivos(),
            'temaSelec'         => $temaSelec,
            'subtopicoSelec'    => $subtopicoSelec,
            'ejercicioSelec'    => $ejercicioSelec,
            'limite'            => $limite,
            'usuario'           => $usuario,
            'materia'           => $materia,
            'restarAlToken'    => $restarAlToken,
            'soloEjercicios'    => $soloEjercicios ?? '',
            'breadcrumbs'       =>  [['label' => __('app.label.materias'), 
                                    'href' => route('materia.index')]],

        ]);
    }

    public function GenerarSujerencias($respuestaGPT,$plantillaPracticar, $pp, &$finishingReason) {
        // $vectorEjercicios = explode("\n", $respuestaGPT);
        // $vectorEjercicios = array_filter($vectorEjercicios, 'trim');

        $vectorEjercicios = [
            1 => "La energía cinética y potencial son dos formas de energía cruciales para la física y deben ser entendidas. La energía cinética es la energía que un objeto posee cuando está en movimiento, mientras que la energía potencial es la energía que se almacena en el objeto gracias a las fuerzas que actúan sobre él. ◀La energía cinética y potencial son dos formas de energía cruciales para la física y deben ser entendidas. La energía cinética es la energía que un objeto posee ",
            3 => "Para calcular la energía potencial de un cuerpo con masa 1 kg a una altura de 2 metros, se debe utilizar la ecuación de la energía potencial gravitacional, es decir, U = mgh, donde m es la masa, g es la aceleración de la gravedad y h es la altura. Por lo tanto, la energía potencial es igual a 2 kg × 10 m/s2 × 2 m, lo que equivale a 40 Joules. ◀Para calcular la energía potencial de un cuerpo con masa 1 kg a una altura de 2 metros, se debe utilizar la ecuación de la energía potencial gravitacional, es d ",
            5 => "Ejercicios para practicar:",
            7 => "1. Calcular la energía cinética de un objeto con masa de 1 kg que se mueve a una velocidad de 5 m/s.",
            9 => "2. Calcular la energía potencial de un objeto con masa de 10 kg a una altura de 50 metros.",
            11 => "3. ¿Qué es una ecuación de energía cinética? ¿Y una ecuación de energía potencial? Diferencia las dos ecuaciones.",
        ];

        // $posicionEjercicios = -1;
        // foreach ($vectorEjercicios as $key => $value) {
        //     if(strpos($value, 'Ejercicios') !== false){
        //         $posicionEjercicios = $key;
        //     }
        // }

        // $posicionEjercicios = array_search($plantillaPracticar.': ',$vectorEjercicios,true);
        $posicionEjercicios2 = array_search($plantillaPracticar.':',$vectorEjercicios,true);
        
        if($posicionEjercicios2 !== false) {
            $contador = $posicionEjercicios2;
            while($contador <= array_key_last($vectorEjercicios)){
                if($vectorEjercicios[$contador] ?? false){
                    $soloEjercicios[] = $vectorEjercicios[$contador];
                }
                $contador++;
                if($contador > 25)break;
            }
        }else{
            $soloEjercicios = ['Sin sugerencias'];
        }
        $finishingReason = $pp[0]["finish_reason"];

        session(['tresEjercicios' => $soloEjercicios]);
        return $soloEjercicios;
    }//fin: GenerarSujerencias

    


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

    //fin GPT
    public function store(MateriumRequest $request) {
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||materia|| ' );

        try {
            if($request->cuantosReq != 0){
                $req1 = $request->cuantosReq > 0 && $request->requisito1 != '' ? intval($request->requisito1) : null;
                $req2 = $request->cuantosReq > 1 && $request->requisito2 != '' ? intval($request->requisito2) : null;
                $req3 = $request->cuantosReq > 2 && $request->requisito3 != '' ? intval($request->requisito3) : null;
            }

            // dd(
            //      $request->objetivo
            // );

            $materia = materia::create([
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

    public function show(materia $materia) { }
    public function edit(materia $materia) { }

    public function update(Request $request, $id) {
        $materia = Materia::find($id);
        DB::beginTransaction();
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||materia|| ' );
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

    // public function destroy(materia $materia)
    public function destroy($id) {
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||materia|| ' );

        DB::beginTransaction();

        try {
            $materias = materia::findOrFail($id);
            Log::info($nombreC." U -> ".Auth::user()->name."La materia id:".$id." y nombre:".$materias->nombre." ha sido borrada correctamente");
            $materias->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2',['nombre' => $materias->nombre]));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar materia ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.materias')]) . $th->getMessage());
        }
    }
}
