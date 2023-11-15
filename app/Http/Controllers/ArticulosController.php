<?php

namespace App\Http\Controllers;

use App\helpers\HelpArticulo;
use App\helpers\Myhelp;
use App\Jobs\CriticarArticulo;
use App\Models\Calificacion;
use App\Models\TiemposArticulo;
use Illuminate\Support\Arr;
use Inertia\Inertia;

use App\Models\Articulo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticuloRequest;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Unidad;
use App\Models\Universidad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class ArticulosController extends Controller{

    private $modelName = 'Articulo';
    private $yaEstaFiltrada = false;
    private $Myhelp;

    public function __construct()
    {
        $this->Myhelp = new Myhelp();
    }

    // - MapearClasePP, Filtros, losSelect

    public function MapearClasePP(&$Articulos, $numberPermissions) {
        $user = Myhelp::AuthU();
        if ($numberPermissions < 2 && !$this->yaEstaFiltrada) {

//            $Articulos = Auth::user()->articulos->flatMap(function ($articulo) {
//                return collect($articulo);
//            });
            $IdArticulosDelUser = $user->articulos()->pluck('id');
            $Articulos = $Articulos->WhereIn('id',$IdArticulosDelUser)->get();
//            if(!$Articulos->isEmpty())
//                $Articulos->get();
        } else {
            if($numberPermissions < 8){
                $estudiantesID = [];
                $matrizMateriasEstudiantes = $user->EstudiantesDelProfe();
                foreach ($matrizMateriasEstudiantes as $key => $value) {
                    $estudiantesID[] = $value->pluck('id');
                }
                $estudiantesID[] = $user->id;
                $FlattenestudiantesID = Arr::flatten($estudiantesID);
                $Articulos = Articulo::WhereIn('user_id',$FlattenestudiantesID)->get();

            }else{
                $Articulos = $Articulos->get();
            }
        }

        $Articulos = $Articulos->map(function ($Articulo) {
            $Articulo->hijo = $Articulo->user_name();
            $Articulo->cal = $Articulo->calificacion_name();
            $Articulo->calIA = $Articulo->calificacion_IA();
            $Articulo->PromedioValores = $Articulo->PromedioValores();
            return $Articulo;
        })->filter();
    }

    public function Filtros($request, &$Articulos, &$showMateria) {
        // if ($request->has('selectedUnidadID') && $request->selectedUnidadID != 0) {
        //     $showMateria = Materia::find(Unidad::find($request->selectedUnidadID)->materia_id);
        //     $unidadsid = Unidad::has('Articulos')->where('id', $request->selectedUnidadID)->pluck('id')->toArray();
        //     $Articulos->whereIn('unidad_id', $unidadsid);
        //     $this->yaEstaFiltrada = true;
        // }

        if ($request->has('search')) {
            $Articulos->where('nick', 'LIKE', "%" . $request->search . "%");
            $Articulos->orWhere('Palabras_Clave', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $Articulos->orderBy($request->field, $request->order);
        } else {
            $Articulos->orderBy('nick')
                // ->orderBy('enum')
                ;
        }
    }
    public function AutoSelect($numberPermissions) {

        $myhelp = new Myhelp();
        if($numberPermissions < 8){
            $selecs = $myhelp->Vector_TurnInSelectID_AUTH(
                ['universidades','carreras','materias'],
                ['a universidad', 'a carrera', 'a materia'],
            );
        }else{
            $selecs['universidades'] = Myhelp::NEW_turnInSelectID(Universidad::all(),'a universidad');
            $selecs['carreras'] =      Myhelp::NEW_turnInSelectID(Carrera::all(),'a carrera');
            $selecs['materias'] =      Myhelp::NEW_turnInSelectID(Materia::all(),'a materia');
            $selecs['aviso'] = true;
        }

        return $selecs;
    }

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, 'Articulo');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.Articulos');
        $permissions = auth()->user()->roles->pluck('name')[0];
        //$Articulos = Articulo::query();
        $Articulos = Articulo::query()->Where('tipo','Articulo');

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        if ($permissions === "estudiante") {
        } else { // not estudiante
            $this->Filtros($request, $Articulos, $showMateria);
        }
        $this->MapearClasePP($Articulos, $numberPermissions);
        $page = request('page', 1); // Current page number
        $total = $Articulos->count();
        $paginated = new LengthAwarePaginator(
            $Articulos->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $HijoSelec = $this->AutoSelect($numberPermissions);

        return Inertia::render('Articulo/Index', [ //carpeta
            'breadcrumbs'           =>  [['label' => __('app.label.Articulos'), 'href' => route('Articulo.index')]],
            'title'                 =>  $titulo,
            'filters'               =>  $request->all(['search', 'field',
                                        'order'
                                    ]),
            'perPage'               =>  (int) $perPage,
            'fromController'        =>  $paginated,
            'HijoSelec'             =>  $HijoSelec,
            'numberPermissions'     =>  $numberPermissions,
        ]);
    } //fin index

    //index de solo resumenes
    public function index2(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, 'Resumenes');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.Articulos');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Articulos = Articulo::Where('tipo','SoloesunResumen');

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        if ($permissions === "estudiante") {
        } else { // not estudiante
            $this->Filtros($request, $Articulos, $showMateria);
        }
        $this->MapearClasePP($Articulos, $numberPermissions);
        // dd($Articulos);
        $page = request('page', 1); // Current page number
        $total = $Articulos->count();
        $paginated = new LengthAwarePaginator(
            $Articulos->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $HijoSelec = $this->AutoSelect($numberPermissions);

        //# generar()
        $ValoresGenerarSeccion = Inertia::lazy(fn () => HelpArticulo::OptimizarResumen(
            $request->elTexto,1
//            $request->materia
            // $request->tipoTexto
        ));

        return Inertia::render('Articulo/Index2', [ //carpeta
            'breadcrumbs'           =>  [['label' => __('app.label.Articulos'), 'href' => route('Articulo.index')]],
            'title'                 =>  $titulo,
            'filters'               =>  $request->all(['search', 'field',
                                        'order'
                                    ]),

            'perPage'               =>  (int) $perPage,
            'fromController'        =>  $paginated,
            'HijoSelec'             =>  $HijoSelec,
            'numberPermissions'     =>  $numberPermissions,
            'ValoresGenerarSeccion' =>  $ValoresGenerarSeccion,
            'CuantasUniversidades'   => auth()->user()->universidades()->count()
        ]);
    } //fin index

    public function ArticuloResumen() {
       return redirect()->route('Articulo.index')->with('error', 'no hay ningun problema');
    }

    public function create(Request $request) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Articulo'));

        if($numberPermissions < 9 && auth()->user()->universidades()->count() === 0){
            return redirect()->route('Articulo.index')->with('error','Usted aun no esta inscrito en una universidad');
        }

        if($numberPermissions < 9){
            $universidades = auth()->user()->universidades();
        }else{
            $universidades = Universidad::all();
        }

        $HijoSelec = $this->AutoSelect($numberPermissions);
        //# generar()
        $ValoresGenerarSeccion = Inertia::lazy(fn () => HelpArticulo::MejorarResumen(
            $request->elTexto,
            $request->materia,
            $request->tipoTexto
        ));

        return Inertia::render('Articulo/Create', [ //carpeta
            'breadcrumbs'               =>  [['label' => __('app.label.Articulos'), 'href' => route('Articulo.create')]],
            'title'                     =>  'Creando nuevo articulo',
            'HijoSelec'                 =>  $HijoSelec,
            'numberPermissions'         =>  $numberPermissions,
            'ValoresGenerarSeccion'     =>  $ValoresGenerarSeccion,
        ]);
    }

    public function store(ArticuloRequest $request) {
        DB::beginTransaction();
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Articulo'));
        $rutaRedirect = 'Articulo.index';
        try {
            $OwnUser = Myhelp::AuthU();
            $myhelp = new Myhelp();

            if(isset($request->isArticulo) && $request->isArticulo){

                $huellaArticulo = session('huellaArticulo');
                $tiempos = [];
                if($huellaArticulo){
                    $tiempos = TiemposArticulo::Where('huellaArticulo',$huellaArticulo)->Where('user_id',$OwnUser->id)->get();
                }

                $finalInput = $myhelp->GuardarInput($request,$OwnUser);
                $rutaRedirect = 'Articulo.create';

                $Articulo = Articulo::create($finalInput);

                foreach ($tiempos as $index => $tiempo) {
                    $tiempo->update([
                       'articulo_id' => $Articulo->id
                    ]);
                }

                DB::commit();
                $mensaje = "U -> " . Auth::user()->name . " Guardo Articulo con nick: " . $request->nick[0] . " correctamente";
                Myhelp::EscribirEnLog($this, $mensaje);

                // $CriticarJob = new CriticarArticulo($Articulo->id); dispatch($CriticarJob);
                //dispatchAfterResponse
                //CriticarArticulo::dispatch($Articulo->id)->onConnection('database');
                CriticarArticulo::dispatch($Articulo->id, auth()->user())->onQueue('secondary');

                return redirect()->route('Articulo.index')->with(
                    'success', __('app.label.created_successfully2', ['nombre' => $Articulo->nick]),
                );
            }else{
                $vectorResumen = [
                    $request['universidad_id']['value'],
                    $request['carrera_id']['value'],
                    $request['materia_id']['value']
                ];
                $finalInput = $myhelp->GuardarInput($request,$OwnUser,$vectorResumen);
                $Articulo = Articulo::create($finalInput);
                DB::commit();
                $mensaje = "U -> " . Auth::user()->name . " Guardo Resumen con nick" . $request->nick[0] . " correctamente";
                Myhelp::EscribirEnLog($this, $mensaje);
                return redirect()->route('Articulo.index2')->with(
                    'success', __('app.label.created_successfully2', ['nombre' => $Articulo->nick]),
                );
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $problema = " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            $this->Myhelp->LogWithTrace($this,$th,$problema);
            return redirect()->route($rutaRedirect)->with('error', __('app.label.created_error', ['nombre' => __('app.label.Articulo')]) . $problema);
        }
    }


    public function show(Articulo $Articulo) { }

    public function edit(Articulo $Articulo, Request $request) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Articulo'));

        $universidades = Universidad::all();
        $opcionesU = Myhelp::NEW_turnInSelectID($universidades,' una universidad');
        foreach ($universidades as $key => $value) {
            $queryCarrera = Carrera::Where('universidad_id',$value->id);
            $carrerasIDs = $queryCarrera->pluck('id');
            foreach ($carrerasIDs as $carreraid) {

                $opcionesAsignatura[$carreraid] = Myhelp::NEW_turnInSelectID(Materia::Where('carrera_id',$carreraid)->get(), ' una asignatura');
            }
            $opcionesCarreras[$value->id] = Myhelp::NEW_turnInSelectID($queryCarrera->get(), ' una carrera');
        }

        //# generar()
        $ValoresGenerarSeccion = Inertia::lazy(fn () => HelpArticulo::MejorarResumen(
            $request->elTexto,
            $request->materia,
            $request->tipoTexto
        ));
        return Inertia::render('Articulo/Edit', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Articulos'), 'href' => route('Articulo.create')]],
            'title'             =>  'Creando nuevo articulo',
            'Selects'           =>  [
                'opcionesU'             =>  $opcionesU,
                'opcionesCarreras'      =>  $opcionesCarreras,
                'opcionesAsignatura'    =>  $opcionesAsignatura,
            ],

            'articulo'                  =>  $Articulo,
            'numberPermissions'         =>  $numberPermissions,
            'ValoresGenerarSeccion'     =>  $ValoresGenerarSeccion,
        ]);
    }

    public function RevisarArticulo(Request $request, $id) {
        $Articulo = Articulo::find($id);
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Articulo'));
        $HelpArticulo = new HelpArticulo();

        $Universidad = Universidad::Find($Articulo->universidad_id);
        $Carrera = Carrera::Find($Articulo->carrera_id);
        $Materia = Materia::Find($Articulo->materia_id);

        //# generar()
        $ValoresGenerarSeccion = Inertia::lazy(fn () => HelpArticulo::CalificarArticulo(
            $request->elformulario,
            $request->articuloid,
            $request->notaManual,
        ));
        $CalifiInicial = $HelpArticulo->ConsultarCalificacion($Articulo->id);
        $CalifiConslta = Inertia::lazy(fn () => $HelpArticulo->ConsultarCalificacion($Articulo->id));

        return Inertia::render('Articulo/Revis', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Articulos'), 'href' => route('Articulo.create')]],
            'title'             =>  'Creando nuevo articulo',

            'Universidad' => $Universidad,
            'Carrera' => $Carrera,
            'Materia' => $Materia,
            'articulo'                  =>  $Articulo,
            'numberPermissions'         =>  $numberPermissions,
            'CalifiConslta'             =>  $CalifiConslta,
            'CalifiInicial'             =>  $CalifiInicial,
            'ValoresGenerarSeccion'     =>  $ValoresGenerarSeccion,
        ]);
    }

    public function update(Request $request, $id) {
        $Articulo = Articulo::find($id);
        DB::beginTransaction();
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Articulo'));
        $OwnUser = auth()->user();
        try {
            $myhelp = new Myhelp();
            if($request->is_correcciones){
                $result = $myhelp->GuardarInputSiTermina($request);
            }else{
                $result = $myhelp->GuardarInput($request,$OwnUser);
            }
            $Articulo->update($result);

            DB::commit();
            $mensaje = "U -> " . Auth::user()->name . " Guardo Articulo correctamente";
            Myhelp::EscribirEnLog($this, $mensaje);

            return redirect()->route('Articulo.index')->with('success', __('app.label.updated_successfully2', ['nombre' => $Articulo->nick]));

        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar Articulo " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Articulo->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function destroy($id) {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();
        try {
            $Articulos = Articulo::findOrFail($id);

            $Articulos->delete();
            Myhelp::EscribirEnLog($this, get_called_class(), "La Articulo id:" . $id . " y nombre:" . $Articulos->nombre . " ha sido borrada correctamente", false);
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $Articulos->nombre]));
        } catch (\Throwable $th) {
            // 23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row
            if ($th->getCode() == 23000) {
                Log::info("U -> " . Auth::user()->name . " fallo en borrar tema " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
                DB::rollback();
                return back()->with('warning', 'Debe borrar los promps asociados a este tema antes de proceder. ');
            } else {
                DB::rollback();
                $problema = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
                Log::alert("U -> " . Auth::user()->name . " fallo en borrar Articulo " . $id . " - " . $problema);
                return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Articulos')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            }
        }
    }
    public function destroyBulk(Request $request) {
        try {
            $user = Articulo::whereIn('id', $request->id);
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' '. $this->modelName]));
        } catch (\Throwable $th) {
            $problema = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $problema);
        }
    }

    public function guardarTiempoUser(Request $request) {
        try {

            $user = Myhelp::AuthU();
//            dd($request->index,
//                $request->startTime,
//                $request->endTime,
//            );
            $tiempo = HelpArticulo::updatingDateTime(
                $request->startTime[$request->index]);

            if($request->index == 1){
                $huella = $tiempo;
                session(['huellaArticulo',$tiempo]);
            }else{
                $huella = session('huellaArticulo');
            }

            TiemposArticulo::create([
                'startTime' => $tiempo,
                'endTime' => HelpARticulo::updatingDateTime($request->endTime[$request->index]),
                'tiempoEscritura' => ($request->tiempoEscritura[$request->index]),
                'user_id' => $user->id,
                'articulo_id' => null,
                'huellaArticulo' => $huella
            ]);

        } catch (\Throwable $th) {
            $problema = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            dd($problema);
            Myhelp::EscribirEnLog($this, ' guardarTiempoUser','Tiempo guardado incorrectamente: '.$problema);
        }
    }
}
