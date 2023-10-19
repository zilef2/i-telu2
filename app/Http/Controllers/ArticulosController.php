<?php

namespace App\Http\Controllers;

use App\helpers\HelpArticulo;
use App\helpers\Myhelp;
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

    // - MapearClasePP, Filtros, losSelect

    public function MapearClasePP(&$Articulos, $numberPermissions) {
        if ($numberPermissions < 2 && !$this->yaEstaFiltrada) {
            $Articulos = Auth::user()->articulos->flatMap(function ($articulo) {
                return collect($articulo);
            })->get();
        } else {
            if($numberPermissions < 8){
                $matrizMateriasEstudiantes = Auth()->user()->EstudiantesDelProfe();
                foreach ($matrizMateriasEstudiantes as $key => $value) {
                    $estudiantesID[] = $value->pluck('id');
                }
                $Articulos = Articulo::WhereIn('user_id',$estudiantesID)->get();

            }else{
                $Articulos = $Articulos->get();
            }
        }

        $Articulos = $Articulos->map(function ($Articulo) {
            $Articulo->hijo = $Articulo->user_name();
            $Articulo->cal = $Articulo->calificacion_name();
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
    public function losSelect($numberPermissions) {
        // if ($numberPermissions < intval(env('PERMISS_VER_FILTROS_SELEC'))) { //coorPrograma,profe,estudiante
        //     $UserSelect = Articulo::WhereHas('articulos')->get();
        // } else {
            $UserSelect = User::WhereHas('roles', function ($query) {
                return $query->whereNotIn('name', ['superadmin','admin']);
            })->get();
        // }

        return [
            'HijoSelec' => $UserSelect
        ];
    }

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, 'Articulo');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.Articulos');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Articulos = Articulo::query();

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

        $HijoSelec = $this->losSelect($numberPermissions);

        return Inertia::render('Articulo/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Articulos'), 'href' => route('Articulo.index')]],
            'title'             =>  $titulo,
            'filters'           =>  $request->all([
                                        'search',
                                        'field', 
                                        'order'
                                    ]),

            'perPage'           =>  (int) $perPage,
            'fromController'    =>  $paginated,
            'HijoSelec'         =>  $HijoSelec['HijoSelec'],
            'numberPermissions' =>  $numberPermissions,
        ]);
    } //fin index

    public function create(Request $request) {
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
        $ValoresGenerarSeccion = Inertia::lazy(fn () => HelpArticulo::OptimizarResumenOIntroduccion(
            $request->elTexto, 
            $request->materia,
            $request->tipoTexto
        ));

        return Inertia::render('Articulo/Create', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Articulos'), 'href' => route('Articulo.create')]],
            'title'             =>  'Creando nuevo articulo',
            'Selects'           =>  [
                'opcionesU'             =>  $opcionesU,
                'opcionesCarreras'      =>  $opcionesCarreras,
                'opcionesAsignatura'    =>  $opcionesAsignatura,
            ],

            'numberPermissions'         =>  $numberPermissions,
            'ValoresGenerarSeccion'     =>  $ValoresGenerarSeccion,
        ]);
    }

    public function store(ArticuloRequest $request) {
        DB::beginTransaction();
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Articulo'));

        try {
            $input = $request->all();
            $finalInput = [];
            foreach ($input as $key => $value) {
                if(!isset($value[0])){
                    $finalInput[$key] = $value;
                }else{
                    $finalInput[$key] = $value[0];
                    if(isset($value[1])){
                        $finalInput[$key.'_ia'] = $value[1][0];
                        $finalInput[$key.'_final'] = $value[2];
                    }
                }
            }
            $finalInput['universidad_id'] = $input['universidadid']['value'];
            $finalInput['carrera_id'] = $input['carreraid']['value'];
            $finalInput['materia_id'] = $input['materiaid']['value'];
            $finalInput['user_id'] = auth()->user()->id;
            $finalInput['version'] = 1;
            $Articulo = Articulo::create($finalInput);
            DB::commit();
            $mensaje = "U -> " . Auth::user()->name . " Guardo Articulo " . $request->nick[0] . " correctamente";
            Myhelp::EscribirEnLog($this, $mensaje);

            return redirect()->route('Articulo.index')->with(
                'success', __('app.label.created_successfully2', ['nombre' => $Articulo->nick]),
            );

        } catch (\Throwable $th) {
            DB::rollback();
            $problema = " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar Articulo " . $request->nick[0] . $problema);
            return redirect('Articulo/create')->with('error', __('app.label.created_error', ['nombre' => __('app.label.Articulo')]) . $problema);
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
        $ValoresGenerarSeccion = Inertia::lazy(fn () => HelpArticulo::OptimizarResumenOIntroduccion(
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
        // dd( $request->elformulario );

        $ValoresGenerarSeccion = Inertia::lazy(fn () => HelpArticulo::CalificarArticulo(
            $request->elformulario,
            $request->articuloid,
            $request->notaManual,
        ));

        return Inertia::render('Articulo/Revis', [ //carpeta
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

    public function update(Request $request, $id) {
        $Articulo = Articulo::find($id);
        DB::beginTransaction();
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Articulo'));

        try {
            $input = $request->all();
            $finalInput = [];
            foreach ($input as $key => $value) {
                if(!isset($value[0])){
                    $finalInput[$key] = $value;
                }else{
                    $finalInput[$key] = $value[0];
                    if(isset($value[1])){
                        $finalInput[$key.'_ia'] = $value[1][0];
                        $finalInput[$key.'_final'] = $value[2];
                    }
                }
            }
            $finalInput['universidad_id'] = $input['universidadid']['value'];
            $finalInput['carrera_id'] = $input['carreraid']['value'];
            $finalInput['materia_id'] = $input['materiaid']['value'];
            $finalInput['user_id'] = auth()->user()->id;
            $finalInput['version'] = 1;
            $Articulo->update($finalInput);
            DB::commit();
            $mensaje = "U -> " . Auth::user()->name . " Guardo Articulo " . $request->nick[0] . " correctamente";
            Myhelp::EscribirEnLog($this, $mensaje);

            return redirect()->route('Articulo.index')->with('success', __('app.label.created_successfully2', ['nombre' => $Articulo->nick]));

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
}
