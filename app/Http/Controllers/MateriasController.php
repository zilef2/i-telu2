<?php

namespace App\Http\Controllers;

use App\helpers\HelpGPT;
use OpenAI;
use App\Models\User;

use Inertia\Inertia;
use App\helpers\Myhelp;
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
use App\Models\LosPromps;
use App\Models\Objetivo;
use App\Models\Parametro;
use App\Models\RespuestaEjercicio;
use App\Models\Subtopico;
use App\Models\Unidad;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
// use OpenAI\Exceptions\ErrorException;

class MateriasController extends Controller
{
    public $muyFrecuente = 'Espere un poco para usar GPT de nuevo';
    public $respuestaLimite = 'Limite de tokens';
    public $respuestaLarga = 'La respuesta es demasiado extensa';
    public $MAX_USAGE_RESPUESTA = 550;
    public $MAX_USAGE_TOTAL = 600;

    private $modelName = 'Materia';


    //! index functions ()
    public function MapearClasePP(&$materias, $numberPermissions)
    {
        $materias = $materias->get()->map(function ($materia) use ($numberPermissions) {

            if ($numberPermissions < 2) {
                $carreraUser = Auth::user()->carreras()->pluck('carreras.id')->toArray();
                if (!in_array($materia->carrera_id, $carreraUser)) return null;
            }

            $materia->papa = $materia->carrera_nombre();
            $materia->cuantoshijos = count($materia->unidads);

            $materia->muchos = $materia->users_nombres();

            $materia->objetivs = ($materia->objetivos()->count());
            $materia->objetivos = ($materia->objetivos);
            return $materia;
        })->filter();
        // dd($materias);
    }
    public function fNombresTabla($numberPermissions)
    {
        $nombresTabla[2] = [null, null, null, "enum", "nombre", "codigo", "carrera_id", null, null, null, "descripcion"];

        if ($numberPermissions <= 2) {
            $nombresTabla[0] = ["IA", "#", "Nombre", "Codigo", "Carrera", "Unidades", "Objetivos", "descripcion"];
        } else { //not estudiante
            $nombresTabla[0] = ["Edicion", "Matricular", "IA", "#", "Nombre", "Codigo", "Carrera", "Unidades", "usuarios", "Objetivos", "descripcion"];
        }

        return $nombresTabla;
    }

    public function Filtros($request, &$materias)
    {
        if ($request->has('selectedUni') && $request->selectedUni != 0) {
            // dd($request->selectedUni);
            $carrerasid = Carrera::has('materias')->where('universidad_id', $request->selectedUni)->pluck('id')->toArray();
            $materias->whereIn('carrera_id', $carrerasid);
        }
        if ($request->selectedUni == 0) $request->selectedcarr = 0;

        if ($request->has('search')) {
            $materias->where('descripcion', 'LIKE', "%" . $request->search . "%");
            // $materias->whereMonth('descripcion', $request->search);
            // $materias->OrwhereMonth('fecha_fin', $request->search);
            $materias->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            // $materias->orderBy($request->field, $request->order);
            $materias->orderBy($request->field, $request->order);
        } else {
            $materias->orderBy('nombre');
        }
    }
    public function losSelect($numberPermissions, &$carrerasSelect, &$MateriasRequisitoSelect, &$UniversidadSelect, $request, &$materias)
    {
        if ($request->has('selectedUni') &&  $request->selectedUni != 0) {
            $carrerasSelect = Carrera::where('universidad_id', $request->selectedUni)->get();
        } else {
            $carrerasSelect = Carrera::all();
        }
        if ($request->has('selectedcarr') && $request->selectedcarr != 0) {
            $materias = $materias->whereIn('carrera_id', $request->selectedcarr);
        }

        // $carrerasSelect = Carrera::has('materias')->get();
        if ($numberPermissions < intval(env('PERMISS_VER_FILTROS_SELEC'))) { //coorPrograma,profe,estudiante
            $UniversidadSelect = Auth::user()->universidades;
            $MateriasRequisitoSelect = Auth::user()->materias;
        } else {
            $UniversidadSelect = Universidad::has('carreras')->get();
            $MateriasRequisitoSelect = Universidad::has('carreras')->get();
        }
    }


    public function index(Request $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, ' materia');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.materias');

        $perPage = $request->has('perPage') ? $request->perPage : 10;

        $materias = Materia::query();

        if ($permissions === "estudiante") {
            $nombresTabla = $this->fNombresTabla($numberPermissions);
        } else { // not estudiante
            $this->Filtros($request, $materias);
            $perPage = $request->has('perPage') ? $request->perPage : 10;
            $nombresTabla = $this->fNombresTabla($numberPermissions);
        }

        //hijos materias 
        $this->MapearClasePP($materias, $numberPermissions);

        $carrerasSelect = $MateriasRequisitoSelect = $UniversidadSelect = null;
        $this->losSelect($numberPermissions, $carrerasSelect, $MateriasRequisitoSelect, $UniversidadSelect, $request, $materias);

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
            'breadcrumbs'               =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
            'title'                     =>  $titulo,
            'filters'                   =>  $request->all(['search', 'field', 'order', 'selectedUni', 'selectedcarr']),
            'perPage'                   =>  (int) $perPage,
            'fromController'            =>  $paginated,
            'nombresTabla'              =>  $nombresTabla,
            'errorMessage'              => $errorMessage,
            'carrerasSelect'            => $carrerasSelect,
            'MateriasRequisitoSelect'   => $MateriasRequisitoSelect,
            'UniversidadSelect'         => $UniversidadSelect,
            'numberPermissions'         => $numberPermissions,
        ]);
    } //fin index


    //! STORE & SHOW & UPDATE & DESTTROY
    public function store(MateriumRequest $request)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        try {
            //usefull
            // $modelInstance = resolve('App\\Models\\' . $this->modelName);
            // $ultima = $modelInstance::latest('enum')->first()->enum;
            if ($request->enum) {
                $enum = $request->enum;
            } else {
                $modelInstance = resolve('App\\Models\\' . $this->modelName);
                $enum = intval($modelInstance::latest('enum')->first()->enum) + 1 ?? 1;
            }

            $materia = Materia::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => $request->descripcion,
                'carrera_id' =>  $request->carrera_id,
                'enum' => $enum,
                'codigo' => $request->codigo
            ]);

            for ($i = 0; $i < intval($request->cuantosObj); $i++) {
                Objetivo::create(['nombre' => $request->objetivo[$i], 'materia_id' => $materia->id]);
            }
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo materia " . $request->nombre . " correctamente");
            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $materia->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar materia " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.materia')]) . $th->getMessage() . ' L:' . $th->getLine());
        }
    }

    public function show($id)
    {
        $materia = Materia::find($id);
        $unidads = $materia->unidads;
        $objetivos = $materia->objetivos;

        foreach ($unidads as $temaKey => $Unidad) {
            $Unidad->sub = $Unidad->subtopicos;
            $ArrayEjercicios = [];
            foreach ($Unidad->sub as $key => $subtopico) {
                $ArrayEjercicios[$key] = $subtopico->ejercicios->pluck('id', 'nombre')->toArray();
            }
            $Unidad->sub->ejercis = $ArrayEjercicios;
        }

        return Inertia::render('materia/show', [ //carpeta
            'title'          =>  'Ver materia',
            // 'filters'        =>  $request->all(['search', 'field', 'order','selectedUni','selectedcarr']),
            'fromController' =>  $materia,
            'unidads' =>  $unidads,
            'objetivos' =>  $objetivos,
            'breadcrumbs'    =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
        ]);
    }
    public function edit(materia $materia)
    {
    }

    public function update(Request $request, $id)
    {
        $materia = Materia::find($id);
        $request->validate([
            'codigo' => 'required|unique:materias,codigo,' . $id,
        ]);

        $objetivos = $materia->objetivos;
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);
        try {

            $OriginalObj = intval($materia->objetivosString(true));

            $materia->update([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => $request->descripcion,
                'carrera_id' =>  $request->carrera_id,
                'enum' => $request->enum,
                'codigo' => $request->codigo
            ]);

            $cuantosObj = intval($request->cuantosObj);
            $diferenciaObjetivos =  $cuantosObj - $OriginalObj;

            if ($diferenciaObjetivos >= 0) {
                for ($i = 0; $i < $OriginalObj; $i++) {
                    $objetivos[$i]->update(['nombre' => $request->objetivo[$i]]);
                }
                for ($i = $OriginalObj; $i <= $cuantosObj - 1; $i++) {
                    Objetivo::create(['nombre' => $request->objetivo[$i], 'materia_id' => $materia->id]);
                    // $objetivos[$i]->update(['nombre' => $request->objetivo[$i]]);
                }
            } else {
                for ($i = $OriginalObj - 1; $i >= $cuantosObj; $i--) {
                    $objetivos[$i]->delete();
                }
            }

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo materia " . $request->nombre . " correctamente");
            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $materia->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar materia " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine());

            return back()->with('error', __('app.label.updated_error', ['nombre' => $materia->nombre]) . $th->getMessage() . ' L:' . $th->getLine());
        }
    }


    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();

        try {
            $materias = Materia::findOrFail($id);
            Myhelp::EscribirEnLog(
                $this,
                get_called_class(),
                "La materia id:" . $id . " y nombre:" . $materias->nombre . " ha sido borrada correctamente",
                false
            );


            $materias->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $materias->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar materia " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.materias')]) . $th->getMessage() . ' L:' . $th->getLine());
        }
    }
    //STORE & UPDATE & DESTROY



    public function AsignarUsers(Request $request, $materiaid)
    {
        $titulo = 'Seleccione los estudiantes a matricular';
        $permissions = Myhelp::EscribirEnLog($this, 'carrera');
        if ($permissions === "estudiante") {
            return back()->with('error', __('app.label.no_permission'));
        } else { // not estudiante
            // $nombresTabla = $this->NombresTabla(0);
        }

        $materia = Materia::find($materiaid);
        $carrera = Carrera::find($materia->carrera_id);
        $universidad = Universidad::find($carrera->universidad_id);

        $users = User::query();

        $filtroUser = $this->UsuariosSinLosInscritos($materia, $carrera);

        if (count($filtroUser->si) > 0) {
            $users->whereNotIn('users.id', $filtroUser->no)
                ->whereIn('users.id', $filtroUser->si);

            if ($request->has('search')) {
                $users->Where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%" . $request->search . "%");
                    $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                });
            }
        } else {
            $users->where('id', 0);
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

    private function UsuariosSinLosInscritos($modelo, $carrera)
    {
        $usuariosU = $carrera->users->pluck('id');
        $usuariosDeLaMateria = $modelo->users->pluck('id');

        return (object) [
            'si' => $usuariosU,
            'no' => $usuariosDeLaMateria
        ];
    }


    public function SubmitAsignarUsers(Request $request)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, ' materia');

        try {
            $materia = Materia::find($request->materiaid);
            // dd($request->selectedId);
            $materia->users()->attach(
                $request->selectedId
            );

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " matriculo a la materia " . count($request->selectedId) . " estudiantes correctamente");

            return redirect()->route('materia.index')->with('success', __('app.label.created_success'));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en matricular(materia) - " . $th->getMessage() . ' L:' . $th->getLine());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage() . ' L:' . $th->getLine());
        }
    }

    public function lookForTemas($materiaid)
    {
        $materia = Materia::find($materiaid);
        $unidads = $materia->unidads;
        foreach ($unidads as $key => $value) {
            // $unidads[$key]->preguntas = 'asd';
            $unidads[$key]->mat = $value->materia_nombre();
            $subtopicos = $value->subtopicos;
            foreach ($subtopicos as $subkey => $subtopic) {
                $subtopicos[$subkey]->ejer = $subtopic->ejercicios;
                // dd($subtopic->ejercicios);
            }
            $unidads[$key]->sub = $subtopicos;
        }
        // dd($unidads);
        // $valoresSelect = [];
        // $valoresSelect[] = [ 'label' => $objetivo, 'value' => 1];
        // $valoresSelect[] = [ 'label' => 'Intermedio', 'value' => 2];
        // $valoresSelect[] = [ 'label' => 'Preparacion', 'value' => 3];
        return [$unidads, null];
    }




    // $wolf = WolframAlphaService::query('integral of x^2');
    public function VistaTema(Request $request, $materiaid, $ejercicioid = null, $idnivel = null, $subtemaid = null, $selectedPrompID = null)
    {
        // dd($subtemaid);

        $permissions = Myhelp::EscribirEnLog($this, ' VistaTEma materia');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $usuario = Auth::user();
        $restarAlToken = 0;
        set_time_limit(180);
        $respuesta = '';

        if ($numberPermissions > 1) {
            try {

                $vectorYSelecNiveles = HelpGPT::nivelesAplicativo();
                if ($idnivel) {
                    $ChosenNivel = $vectorYSelecNiveles[0][$idnivel];
                } else {
                    $ChosenNivel = '';
                }
                $materia = Materia::find($materiaid);
                $limite = intval($usuario->limite_token_leccion);
                $soloEjercicios = [];

                $opcion = 1; //primer pantallazo

                if ($numberPermissions > 4) {
                    $ListaPromp = LosPromps::All();
                } else {
                    $ListaPromp = $usuario->LosPromps()->Where('user_id', $usuario->id);
                    // $ListaPromp = LosPromps::Where('clasificacion', 'Expectativas Altas')->get();
                }
                $ListaPromp = HelpGPT::NEW_turnInSelectID($ListaPromp);

                // dd($subtemaid !== null , $ejercicioid !== null);
                if ($subtemaid !== null || $ejercicioid !== null) {
                    if ($subtemaid !== null) {
                        if ($ejercicioid === 'explicar') {
                            $subtopicoSelec = Subtopico::find($subtemaid);
                            $temaSelec = Unidad::find($subtopicoSelec->unidad_id)->nombre;
                            $opcion = 2; //se va aresolver un unidad        } else {
                        }
                        if ($ejercicioid === 'practicar') {
                            $subtopicoSelec = Subtopico::find($subtemaid);
                            $temaSelec = Unidad::find($subtopicoSelec->unidad_id)->nombre;
                            $opcion = 4; //Quiz
                        }
                    } else {
                        $ejercicio = Ejercicio::find($ejercicioid);
                        $ejercicioSelec = $ejercicio->nombre;

                        $subtopicoSelec = Subtopico::find($ejercicio->subtopico_id);
                        $temaSelec = Unidad::find($subtopicoSelec->unidad_id)->nombre;
                        $opcion = 3; //se va a resolver un ejercicio
                    }
                }

                $selectedReasonString = '';
                if ($opcion !== 1) {
                    if ($limite > 0) {
                        $tiempo = session('tiempo', Carbon::now());
                        $diffTiempos = Carbon::now()->diffInSeconds($tiempo);
                        session(['tiempo' => Carbon::now()]);

                        if ($diffTiempos == 0 || $diffTiempos > 1) { //debe esperar 1 segundo almenos
                            if ($opcion != 4) {
                                if ($opcion === 2) { //resolver unidad
                                    $selectedReasonString = LosPromps::Find($selectedPrompID)->principal;
                                    $gpt = HelpGPT::gptResolverTema($selectedReasonString, $subtopicoSelec->nombre, $ChosenNivel, $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                                }

                                if ($opcion === 3) { //ejercicio
                                    $gpt = HelpGPT::gptPart1($ejercicioSelec, $ChosenNivel, $materia->nombre, $usuario, $soloEjercicios, env('DEBUGGINGGPT'));
                                }
                                $respuesta = preg_replace("/^\n\n/", "", $gpt['respuesta']);
                            } else { //resolver quiz opcion = 4
                                $selectedReasonString = LosPromps::Find($selectedPrompID)->principal;
                                $gpt = HelpGPT::gptResolverQuiz($selectedReasonString, $subtopicoSelec->nombre, $ChosenNivel, $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                            }
                            $restarAlToken = $gpt['restarAlToken'];
                            $limite = intval($usuario->limite_token_leccion);
                        } else {
                            $respuesta = $this->muyFrecuente; //hizo una peticion en menos de un segundo a la anterior
                            //todo: grabar en el log, que este usuario es desesperado o hacker jaja
                        }
                    } else { //no le quedan mas tokens
                        $respuesta = $this->respuestaLimite;
                    }
                }

                $temasYValores = $this->lookForTemas(intval($materiaid));
                $nivelSelect = $vectorYSelecNiveles[1];

                set_time_limit(70);
                session(['tiempo' => Carbon::now()]);
                return Inertia::render('materia/vistaTem', [ //carpeta
                    'breadcrumbs'           =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
                    'title'                 =>  'Aprendiendo con GPT',
                    'elid'                  =>  intval($materiaid),
                    'perPage'               =>  10,
                    'fromController'        =>  $temasYValores[0],
                    'respuesta'             =>  $respuesta,
                    'objetivosCarrera'      =>  $materia->objetivosString(),
                    'temaSelec'             => $temaSelec ?? 'Esperando Unidad...',
                    'subtopicoSelec'        => $subtopicoSelec ?? null,
                    'ejercicioSelec'        => $ejercicioSelec ?? 'Aqui vera la pregunta',
                    'limite'                => $limite,
                    'usuario'               => $usuario,
                    'materia'               => $materia,
                    'restarAlToken'         => $restarAlToken,
                    'nivelSelect'           => $nivelSelect,
                    'ChosenNivel'           => $ChosenNivel,
                    'soloEjercicios'        => $soloEjercicios,
                    'opcion'                => $opcion,
                    'ListaPromp'            => $ListaPromp,
                    'numberPermissions'     => $numberPermissions,
                    'selectedPrompID'       => intval($selectedPrompID),
                    'selectedReasonString'  => $selectedReasonString,
                    //solo practica
                    'vectorChuleta'            => $gpt['vectorChuleta'] ?? [],
                    'ArrayPreguntas'            => $gpt['ArrayPreguntas'] ?? [],
                    'ArrayRespuestasCorrectas'   => $gpt['ArrayRespuestasCorrectas'] ?? [],
                ]);
            } catch (\Throwable $th) {
                Log::alert("U -> " . Auth::user()->name . " fallo en preguntar la IA:  " . $th->getMessage() . ' L:' . $th->getLine());
                return back()->with('error', 'fallo en preguntar la IA: ' . $th->getMessage() . ' L:' . $th->getLine());
            }
        } else { //solo estudiante | numberpermission == 1

            $limite = intval($usuario->limite_token_leccion);
            $materia = Materia::find($materiaid);
            $temasYValores = $this->lookForTemas(intval($materiaid));
            $selectedReasonString = Parametro::first()->prompExplicarTema;
            if ($limite > 0) {

                if ($subtemaid !== null) {
                    $subtopicoSelec = Subtopico::find($subtemaid);
                    $temaSelec = Unidad::find($subtopicoSelec->unidad_id);
                    $gpt = HelpGPT::gptResolverTema($selectedReasonString, $subtopicoSelec->nombre, 'Profesional', $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                    $restarAlToken = $gpt['restarAlToken'];
                    $limite = intval($usuario->limite_token_leccion);
                    $respuesta = $gpt['respuesta'];
                }
            } else { //no le quedan mas tokens
                $respuesta = $this->respuestaLimite;
            }


            return Inertia::render('materia/vistaTem', [ //carpeta
                'breadcrumbs'           =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
                'title'                 =>  'Aprendiendo con GPT',
                'elid'                 =>  $materiaid,

                'fromController'        =>  $temasYValores[0],
                'respuesta'             =>  $respuesta,
                'temaSelec'             => $temaSelec ?? '',
                'subtopicoSelec'        => $subtopicoSelec ?? null,
                'usuario'               => $usuario,
                'materia'               => $materia ?? null,
                'restarAlToken'         => $restarAlToken,
                'numberPermissions'     => $numberPermissions,
                'limite'                => $limite,

                'filters'        =>  $request->all(['actionEQH', 'laRespuesta']),

                'opcion'                => 0,
                'ListaPromp'            => [],
                'selectedPrompID'       => 0,
                'selectedReasonString'  => '',
            ]);
        }
    }

    // public function masPreguntas($id,$nuevaPregunta) {
    public function masPreguntas(Request $request)
    {
        $usuario = Auth::user();
        // dd(
        //     $request->id,
        //     $request[0],
        //     $request[1],
        //     $request
        // );
        if (isset($request->materiaid) && $request->materiaid != null) {

            $materia = Materia::find($request->materiaid);
            $materia->TodosObjetivos = $materia->objetivos();

            $limite = $usuario->limite_token_general;
            $tresEjercicios = session('tresEjercicios');
            $restarAlToken = 0;
            $respuesta = 'Probando';
            if ($limite > 0) {
                $gpt = $this->gptPart($request, $materia, $usuario); //todo: usar gptpart 1
                $respuesta = preg_replace("/^\n\n/", "", $gpt[0]);
                $restarAlToken = $gpt[1];
            } else { //no le quedan mas tokens
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
                'breadcrumbs'       =>  [[
                    'label' => __('app.label.materias'),
                    'href' => route('materia.index')
                ]],
            ]);
        } else {
            return Inertia::render('materia/masPreguntas', [
                'nuevaPregunta'    => '',
                'respuesta'         =>  '',

                'materia'           => null,
                'nivel'             => $request->nivel ?? 'Bachiller',
                'title'             =>  'Seccion de repaso general',
                'restarAlToken'     => null,
                'tresEjercicios'    => null,
                'limite'            => null,
                'breadcrumbs'       =>  [[
                    'label' => __('app.label.materias'),
                    'href' => route('materia.index')
                ]],
            ]);
        }
    }

    //todo: maybe this is not using
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


            // $respuesta = 'a';
            // $finishingReason ='length';
            // $result['usage']["completionTokens"] = 100;

            if ($finishingReason == 'stop') {
                // dd($result['usage']);
                $usageRespuesta = intval($result['usage']["completion_tokens"]); //~ 260
                $usageRespuestaTotal = intval($result['usage']["total_tokens"]); //~ 500

                $restarAlToken = HelpGPT::CalcularTokenConsumidos($usageRespuesta, $usageRespuestaTotal);
                $usuario->update(['limite_token_general' => (intval($usuario->limite_token_general)) - $restarAlToken]);
                $soloEjercicios = HelpGPT::ApartarSujerencias($respuesta, $plantillaPracticar);

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



    public function actionEQH(Request $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, ' materia');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $usuario = Auth::user();
        try {
            $temaSelec = $request->temaSelec;
            $subtopicoSelec = $request->subtopicoSelec;
            $materia = Materia::find($request->materiaid);
            $respuesta1 = $request->respuesta1;
            $actionEQH = $request->actionEQH;
            // dd($actionEQH,$actionEQH==1);
            if ($actionEQH == 1) { //Ejemplo
                //todo: aquii
                // $selectedReasonString = Parametro::first()->prompEjemplosTema;
                // dd(
                //     $actionEQH,
                //     $subtopicoSelec,
                //     $materia,
                //     $temaSelec,
                // );
                $selectedReasonString = 'Explica 2 ejemplos del subtema: ' . $subtopicoSelec['nombre']
                    . ' de la asignatura: ' . $materia->nombre
                    . ' del tema: ' . $temaSelec['nombre'];
                
                $gpt = HelpGPT::gptResolverTema($selectedReasonString, $subtopicoSelec['nombre'], 'Profesional', $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                $restarAlToken = $gpt['restarAlToken'];
                $ejemplosRespuesta = $gpt['respuesta'];
                
            }
            if ($actionEQH == 2) { //quiz
                $quiz = true;
                $quizPregunta = 'Una pregunta del tema, El diseño esta correcto?';
                $quizCorrecta = 0;
                $quizRespuestas = [
                    'a) si',
                    'b) no',
                    'c) tal vez',
                    'd) todas las anteriores',
                ];
            }
            if ($actionEQH == 3) { //hacer pregunta
                $hagapregunta = true;
                $HacerlaPregunta = $request->HacerlaPregunta;
                
                $selectedReasonString = 'Aqui se responde cualquier pregunta: '. $request->HacerlaPregunta. '. En caso que no sea una pregunta para aprender, responde un mensaje que le haga entender que esto es un aplicativo para la enseñanza';
                
                $gpt = HelpGPT::gptResolverTema($selectedReasonString, $subtopicoSelec['nombre'], 'Profesional', $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                $restarAlToken = $gpt['restarAlToken'];
                $RespuestaPregunta = $gpt['respuesta'];
            }
            $limite = intval($usuario->limite_token_leccion);

            return Inertia::render('materia/EQH', [ //carpeta
                'breadcrumbs'           =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
                'title'                 =>  'Aprendiendo con GPT',
                // 'filters'               =>  $request->all(['actionEQH', 'laRespuesta']),
                'materia'               => $materia ?? null,

                'temaSelec'             => $temaSelec ?? '',
                'subtopicoSelec'        => $subtopicoSelec ?? '',
                'respuesta1'            => $respuesta1 ?? '', //la que viene de versionEstudiante
                'limite'                => $limite,

                'actionEQH'             => $actionEQH,
                'ejemplosRespuesta'     => $ejemplosRespuesta ?? null, //respuesta actual

                'restarAlToken'         => $restarAlToken ?? 0,

                'quiz'                  => $quiz ?? false,
                'quizPregunta'          => $quizPregunta ?? null,
                'quizCorrecta'          => $quizCorrecta ?? null,
                'quizRespuestas'        => $quizRespuestas ?? null,

                'hagapregunta'          => $hagapregunta ?? false,
                'HacerlaPregunta'       => $HacerlaPregunta ?? null,
                'RespuestaPregunta'     => $RespuestaPregunta ?? null,
            ]);
        } catch (\Throwable $th) {
            Log::Critical("U -> " . Auth::user()->name . " fallo en en EQH - " . $th->getMessage() . ' L:' . $th->getLine());

            $myhelp = new Myhelp();
            $myhelp->redirect('materias.index', 0);
            // return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.materia')]) . $th->getMessage().' L:'.$th->getLine());
        }
    }
    //fin gpt
}
