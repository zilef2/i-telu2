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
use App\Http\Requests\IA_MateriaRequest;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\MateriumRequest;
use App\Models\Archivo;
use App\Models\Ejercicio;
use App\Models\LosPromps;
use App\Models\Objetivo;
use App\Models\Parametro;
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
    public function MapearClasePP(&$materias, $numberPermissions) {
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
    public function fNombresTabla($numberPermissions) {
        if ($numberPermissions <= 2) {
            $nombresTabla[2] = [null, null,      "enum",    "nombre",  "codigo", "carrera_id", null     , null,       "descripcion"];
            $nombresTabla[0] = ["IA", "Archivos","Semestre", "Nombre", "Codigo", "Carrera",   "Unidades", "Objetivos", "descripcion"];
        } else { //not estudiante
            $nombresTabla[2] = [null, null, null, null,                     "enum",     "nombre", "codigo", "carrera_id", null,      null,       null, "descripcion"];
            $nombresTabla[0] = ["Edicion", "Matricular", "IA", "Archivos",  "Semestre", "Nombre", "Codigo", "Carrera",  "Unidades", "usuarios", "Objetivos", "descripcion"];
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
            if($numberPermissions > 8){
                $carrerasSelect = Carrera::all();
            }else{
                $carrerasSelect = Auth::user()->carreras;
            }
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
            $MateriasRequisitoSelect = Materia::all();
        }
    }


    public function index(Request $request) {
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

        //# generarTodo.vue :> generar

        // $listaMaterias = Materia::where('carrera_id',$request->carrera_id)->pluck('nombre') ?? [];
        $materiaNombre = Materia::find($request->materia_id);
        $materiaNombre = $materiaNombre->nombre ?? '';
        $laCarrera = Carrera::find($request->carrera_id) ?? null;
        $ValoresGenerarMateria = Inertia::lazy(fn () => HelpGpt::ValoresGenerarMateria($laCarrera->nombre, $materiaNombre,[
            'temas' => $request->temas,
            'unidades' => $request->unidades,
        ]));
        // dd(
        //     $request->temas,
        //     $request->unidades,
        // );

        //# generarTodo.vue :> buscarMateriasSelect
        if ($request->has('carrera_id_buscar') && $request->carrera_id_buscar != 0) {
            $MateriasRequisitoSelect = Inertia::lazy(fn () => Myhelp::buscarMaterias($request->carrera_id_buscar));

        }

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

            'ValoresGenerarMateria'     => $ValoresGenerarMateria ?? null,
        ]);
    } //fin index


    //! STORE & SHOW & UPDATE & DESTTROY
    public function store(MateriumRequest $request) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        try {
            //very usefull
            // $modelInstance = resolve('App\\Models\\' . $this->modelName);
            // $ultima = $modelInstance::latest('enum')->first()->enum;
            $enum = $this->enumUltimo($request->enum);

            $materia = Materia::create([
                'nombre' => $request->nombre,
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
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar materia " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.materia')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function enumUltimo($requeEnum) {
        if ($requeEnum) {
            return $requeEnum;
        } else {
            $modelInstance = resolve('App\\Models\\' . $this->modelName);
            return intval($modelInstance::latest('enum')->first()->enum) + 1 ?? 1;
        }
    }

    //usefull
    public function materiaguardarGenerado(IA_MateriaRequest $request) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        try {
            // $enum = $this->enumUltimo($request->enum_mat);
            $materia = Materia::find($request->materia_id);
            // $materia = Materia::create([
            //     'nombre' => $request->nombre_mat,
            //     'descripcion' => '',
            //     'carrera_id' =>  $request->carrera_id,
            //     'enum' => $enum,
            //     'codigo' => $request->codigo_mat
            // ]);
            Objetivo::create(['nombre' => $request->objetivo, 'materia_id' => $materia->id]);
            $contadorUnidad = 0;
            foreach ($request->nombre_unidad as $keyUnidad => $unidad) {

                $unid = Unidad::create([
                    'nombre' => $unidad,
                    'descripcion' => '',
                    'materia_id' => $materia->id,
                    'codigo' => $request->codigo_mat .'_'.$unidad,
                    'enum' => $contadorUnidad + 1,
                ]);

                for ($i=0; $i < intval($request->Cuantas_t); $i++) { 
                    Subtopico::create([
                        'nombre' => $request->Array_nombre_tema[$contadorUnidad][$i],
                        'descripcion' => '',
                        'unidad_id' => $unid->id,
                        'resultado_aprendizaje' => $request->Array_RA[$contadorUnidad][$i],
                        'enum' => $i+1,
                        'codigo' => $request->codigo_mat,
                    ]);
                }
                $contadorUnidad++;
            }

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Generar la materia " . $request->nombre_mat . " correctamente");
            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $materia->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Generar la materia " . $request->nombre_mat . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.materia')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }


    public function show($id) {
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
    public function edit(materia $materia) { }

    public function update(Request $request, $id) {
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
            // $diferenciaObjetivos =  $cuantosObj - $OriginalObj;

            // if($cuantosObj != count($request->objetivo)){
            //     DB::rollback();
            //     return session()->flash('warning', 'Hay objetivos vacios o erroneos');
            // }

            if ($cuantosObj >= $OriginalObj) {
                for ($i = 0; $i < $OriginalObj; $i++) {
                    $objetivos[$i]->update(['nombre' => $request->objetivo[$i]]);
                }
                for ($i = $OriginalObj; $i <= $cuantosObj - 1; $i++) {
                    if (isset($request->objetivo[$i]))
                        Objetivo::create(['nombre' => $request->objetivo[$i], 'materia_id' => $materia->id]);
                }
            } else {
                for ($i = $OriginalObj - 1; $i >= $cuantosObj; $i--) {
                    if (isset($objetivos[$i]))
                        $objetivos[$i]->delete();
                }
            }

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo materia " . $request->nombre . " correctamente");
            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $materia->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar materia " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.updated_error', ['nombre' => $materia->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }


    public function destroy($id) {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();

        //todo: si borra esta materia, tendra que borrar todos los archivo asociadas a ella

        try {
            $materias = Materia::findOrFail($id);
            Myhelp::EscribirEnLog($this, get_called_class(), "La materia id:" . $id . " y nombre:" . $materias->nombre . " ha sido borrada correctamente", false);

            $materias->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $materias->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar materia " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.materias')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
    //STORE & UPDATE & DESTROY



    public function AsignarUsers(Request $request, $materiaid) {
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

    private function UsuariosSinLosInscritos($modelo, $carrera) {
        $usuariosU = $carrera->users->pluck('id');
        $usuariosDeLaMateria = $modelo->users->pluck('id');

        return (object) [
            'si' => $usuariosU,
            'no' => $usuariosDeLaMateria
        ];
    }


    public function SubmitAsignarUsers(Request $request) {
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
            Log::alert("U -> " . Auth::user()->name . " fallo en matricular(materia) - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function lookForTemas($materiaid) {
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


    public function VistaTema(Request $request, $materiaid, $ejercicioid = null, $idnivel = null, $subtemaid = null, $selectedPrompID = null) {
        $permissions = Myhelp::EscribirEnLog($this, ' VistaTem ');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $usuario = Auth::user();
        $restarAlToken = 0;
        set_time_limit(180);
        $unidad = Unidad::where('materia_id', $materiaid)->first();
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

                if ($subtemaid !== null || $ejercicioid !== null) {
                    if ($subtemaid !== null) {
                        $subtopicoSelec = Subtopico::find($subtemaid);
                        $temaSelec = Unidad::find($subtopicoSelec->unidad_id)->nombre;
                        if ($ejercicioid === 'explicar') {
                            $opcion = 2; //se va a resolver desde admin
                        }
                        if ($ejercicioid === 'practicar') {
                            $opcion = 4; //Quiz
                        }
                    } else {
                        $ejercicio = Ejercicio::find($ejercicioid);

                        $subtopicoSelec = Subtopico::find($ejercicio->subtopico_id);
                        $temaSelec = Unidad::find($subtopicoSelec->unidad_id)->nombre;
                        $opcion = 3; //se va a resolver un ejercicio
                    }
                }
                $selectedReasonString = '';
                if ($opcion !== 1) {
                    if ($limite > 0) {
                        if ($opcion != 4) {
                            if ($opcion === 2) { //resolver unidad
                                if ($selectedPrompID) {
                                    $selectedReasonString = LosPromps::Find($selectedPrompID)->principal;
                                } else {
                                    $selectedReasonString = Parametro::first()->prompExplicarTema;
                                }
                                $gpt = HelpGPT::gptResolverTema($selectedReasonString, $subtopicoSelec, $unidad->nombre, $ChosenNivel, $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                            }

                            if ($opcion === 3) { //ejercicio
                                $gpt = HelpGPT::gptPart1($ejercicio, $ChosenNivel, $materia->nombre, $usuario, $soloEjercicios, env('DEBUGGINGGPT'));
                            }
                            $respuesta = preg_replace("/^\n\n/", "", $gpt['respuesta']);
                        } else { //resolver quiz opcion = 4
                            $selectedReasonString = LosPromps::Find($selectedPrompID)->principal;
                            $gpt = HelpGPT::gptResolverQuiz($selectedReasonString, $subtopicoSelec->nombre, $ChosenNivel, $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                        }
                        $restarAlToken = $gpt['restarAlToken'];
                        $limite = intval($usuario->limite_token_leccion);
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

                    'notvalidbyteacher'     => $respuesta != env('NOTVALIDATEDBYTEACHER'),

                    //solo practica
                    'vectorChuleta'            => $gpt['vectorChuleta'] ?? [],
                    'ArrayPreguntas'            => $gpt['ArrayPreguntas'] ?? [],
                    'ArrayRespuestasCorrectas'   => $gpt['ArrayRespuestasCorrectas'] ?? [],
                ]);
            } catch (\Throwable $th) {
                Log::alert("U -> " . Auth::user()->name . " fallo en preguntar la IA:  " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
                return back()->with('error', 'fallo en preguntar la IA: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            }
        } else { //solo estudiante | numberpermission == 1

            $limite = intval($usuario->limite_token_leccion);
            $materia = Materia::find($materiaid);
            $temasYValores = $this->lookForTemas(intval($materiaid));
            $selectedReasonString = Parametro::first()->prompExplicarTema;
            if ($limite > 0) {

                if ($subtemaid !== null) {
                    $subtopicoModel = Subtopico::find($subtemaid);
                    $temaSelec = Unidad::find($subtopicoModel->unidad_id);
                    $gpt = HelpGPT::gptResolverTema($selectedReasonString, $subtopicoModel, $unidad->nombre, 'Profesional', $materia->nombre, $usuario, env('DEBUGGINGGPT'));
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
                'subtopicoSelec'        => $subtopicoModel ?? null,
                'usuario'               => $usuario,
                'materia'               => $materia ?? null,
                'restarAlToken'         => $restarAlToken,
                'numberPermissions'     => $numberPermissions,
                'limite'                => $limite,
                'notvalidbyteacher'     => $respuesta != env('NOTVALIDATEDBYTEACHER'),

                'filters'        =>  $request->all(['actionEQH', 'laRespuesta']),

                'opcion'                => 0,
                'ListaPromp'            => [],
                'selectedPrompID'       => 0,
                'selectedReasonString'  => '',
            ]);
        }
    }

    public function actionEQH(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, ' materia');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $usuario = Auth::user();
        try {
            
            $SubtopicoEsString = is_string($request->subtopicoSelec);
            if($SubtopicoEsString){
                $subtopicoModel = Subtopico::Where('nombre', $request->subtopicoSelec)->first();
            }else{
                $subtopicoModel = Subtopico::find($request->subtopicoSelec['id']);
            }

            $materia = Materia::find($request->materiaid);
            $unidad = Unidad::where('materia_id', $materia->id)->first();
            $respuesta1 = $request->respuesta1;
            $actionEQH = $request->actionEQH;

            

            if ($actionEQH === 1) { //Ejemplo
                $selectedReasonString = 'Explica con al menos 3 ejemplos, '
                    . 'el subtema: ' . $subtopicoModel->nombre
                    . ' del tema: ' . $unidad->nombre
                    . ' de la asignatura: ' . $materia->nombre;

                $gpt = HelpGPT::gptResolverTema($selectedReasonString, $subtopicoModel, $unidad->nombre, 'Profesional', $materia->nombre, $usuario, env('DEBUGGINGGPT'));

                $ejemplosRespuesta = str_replace('\n','<br>',$gpt['respuesta']);
            }

            if ($actionEQH === 2) { //quiz
                $selectedReasonString = 'Genere una pregunta de seleccion multiple sobre [tema] de la asignatura [materia].';
                $quiz = true;
                $gpt = HelpGPT::gptQuizEstudiante($selectedReasonString, $subtopicoModel, 'Profesional', $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                $quizPregunta = $gpt['vectorChuleta'][0];
                $quizCorrectaString = trim($gpt['vectorChuleta'][$gpt['ArrayRespuestasCorrectas'][0]]);
                $myhelp = new Myhelp();
                if (count($myhelp->EncontrarEnString($quizCorrectaString, "RESPUESTA=A")) > 0) $quizCorrecta = 0;
                if (count($myhelp->EncontrarEnString($quizCorrectaString, "RESPUESTA=B")) > 0) $quizCorrecta = 1;
                if (count($myhelp->EncontrarEnString($quizCorrectaString, "RESPUESTA=C")) > 0) $quizCorrecta = 2;
                if (count($myhelp->EncontrarEnString($quizCorrectaString, "RESPUESTA=D")) > 0) $quizCorrecta = 3;

                array_shift($gpt['vectorChuleta']);
                $RespuestaUnicaCorrecta = array_pop($gpt['vectorChuleta']);
                $quizRespuestas = $gpt['vectorChuleta'];
            }
            if ($actionEQH === 3) { //hacer pregunta
                $hagapregunta = true;
                $HacerlaPregunta = $request->HacerlaPregunta;

                $preguntaAbierta = HelpGPT::GenerarPreguntaAbierta($materia->nombre,$unidad->nombre, $subtopicoModel->nombre,$request->HacerlaPregunta,$numberPermissions);

                $gpt = HelpGPT::gptResolverTema($preguntaAbierta, $subtopicoModel, $unidad->nombre, 'Profesional', $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                $RespuestaPregunta = $gpt['respuesta'];
            }

            if ($actionEQH === 4) { //mas sencilla
                $selectedReasonString = 'Explica de una manera mas sencilla y simple, '
                    . 'el subtema: ' . $subtopicoModel->nombre
                    . ' del tema: ' . $unidad->nombre
                    . ' de la asignatura: ' . $materia->nombre;
                $gpt = HelpGPT::gptResolverTema($selectedReasonString, $subtopicoModel, $unidad->nombre, 'Profesional', $materia->nombre, $usuario, env('DEBUGGINGGPT'));
                $ejemplosRespuesta = $gpt['respuesta'];
            }

            $restarAlToken = $gpt['restarAlToken'];
            $limite = intval($usuario->limite_token_leccion);

            return Inertia::render('materia/EQH', [ //carpeta
                'breadcrumbs'           =>  [['label' => __('app.label.materias'), 'href' => route('materia.index')]],
                'title'                 =>  'Aprendiendo con GPT',
                // 'filters'               =>  $request->all(['actionEQH', 'laRespuesta']),
                'materia'               => $materia ?? null,

                'temaSelec'             => $unidad->nombre ?? '',
                'subtopicoSelec'        => $subtopicoModel->nombre ?? '',
                'respuesta1'            => $respuesta1 ?? '', //la que viene de versionEstudiante
                'limite'                => $limite,

                'actionEQH'             => $actionEQH,
                'ejemplosRespuesta'     => $ejemplosRespuesta ?? null, //respuesta actual

                'restarAlToken'         => $restarAlToken ?? 0,

                'quiz'                  => $quiz ?? false,
                'quizPregunta'          => $quizPregunta ?? null,
                'quizCorrecta'          => $quizCorrecta ?? null,
                'quizRespuestas'        => $quizRespuestas ?? null,
                'RespuestaUnicaCorrecta' => $RespuestaUnicaCorrecta ?? null,

                'hagapregunta'          => $hagapregunta ?? false,
                'HacerlaPregunta'       => $HacerlaPregunta ?? null,
                'RespuestaPregunta'     => $RespuestaPregunta ?? null,
            ]);
        } catch (\Throwable $th) {
            $everythingError = $th->getMessage().' L:'.$th->getLine(). ' Ubi:' . $th->getFile();
            dd($everythingError);
            Log::Critical("U -> " . Auth::user()->name . " fallo en en EQH - " . $everythingError);

            $myhelp = new Myhelp();
            // $myhelp->redirect('materias.index')->with('error', 'error: ' . $th->getMessage() . ' L:' . $th->getLine(). ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.materia')]) . $everythingError);
        }
    }

    public function Archivosindex($materiaid) {
        $permissions = Myhelp::EscribirEnLog($this, ' archivos_materia');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $materia = Materia::find($materiaid);
        if ($permissions === "estudiante") {
        } else { // not estudiante
        }

        $archivos = Archivo::where('materia_id',$materiaid)->get();
        return Inertia::render('materia/docs/ArchivosIndex', [ //carpeta
            'breadcrumbs'               =>  [
                ['label' => __('app.label.materias'), 'href' => route('materia.index')],
                ['label' => __('app.label.archivos'), 'href' => route('materia.Archivos',$materiaid)]
            ],
            'title'                     =>  'Documentos',
            'numberPermissions'         =>  $numberPermissions,
            'archivos'                  =>  $archivos,
            'materia'                   =>  $materia,
        ]);
    }

    public function storeArchivos(Request $request) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);
        $theUser = Auth::user();
        try {
            if($request->type == 'application/pdf'){

                $nombreContenido = str_replace(" ","",time() ."_". $request->archivo->getClientOriginalName());
                $request->archivo->storeAs('public/archivosSubidos',$nombreContenido);

                $archivo = Archivo::create([
                    'NombreOriginal'    => $nombreContenido,
                    'nombre'            => $request->nombre ?? '',
                    'peso'              => $request->peso ?? 0,
                    'type'              => $request->type,
                    'user_id'           => $theUser->id,
                    'materia_id'        => $request->materia_id,
                ]);
                
                DB::commit();
                Log::info("U -> " . Auth::user()->name . " Guardo archivo " . $request->nombre . " correctamente");
                return back()->with('success', __('app.label.created_successfully2', ['nombre' => $archivo->nombre]));
            }
            
            DB::rollback();
            Log::Critical("U -> " . Auth::user()->name . ". El archivo no es un PDF");
            return back()->with('error', "El archivo no es PDF");

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar el archivo " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.archivo')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
}
