<?php

namespace App\Http\Controllers;


use App\helpers\Myhelp;
use Inertia\Inertia;

use App\Models\Articulo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticuloRequest;
use App\Models\Materia;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticulosController extends Controller{

    private $modelName = 'Articulo';
    private $yaEstaFiltrada = false;

    // - MapearClasePP, Filtros, losSelect

    public function MapearClasePP(&$Articulos, $numberPermissions) {
        if ($numberPermissions < 4 && !$this->yaEstaFiltrada) {
            $Articulos = Auth::user()->materias->flatMap(function ($materia) {
                return collect($materia->Tsubtemas);
            });
        } else {
            $Articulos = $Articulos->get();
        }

        $Articulos = $Articulos->map(function ($Articulo) {
            $Articulo->hijo = $Articulo->user_name();
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
        if ($numberPermissions < intval(env('PERMISS_VER_FILTROS_SELEC'))) { //coorPrograma,profe,estudiante
            $UserSelect = Articulo::WhereHas('articulos')->get();
        } else {
            $UserSelect = User::WhereHas('roles', function ($query) {
                return $query->whereNotIn('name', ['superadmin','admin']);
            })->get();
        }

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

    public function create() { }

    public function store(ArticuloRequest $request) {
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||Articulo|| ');

        $enum = Myhelp::getPropertieAutoIncrement($this->modelName, $request->enum, 'enum', 'unidad_id', $request->unidad_id);
        try {
            $Articulo = Articulo::create([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'enum' => $enum,
                'resultado_aprendizaje' => $request->resultado_aprendizaje,
                'unidad_id' => $request->unidad_id,
                'descripcion' => $request->descripcion,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo Articulo " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $Articulo->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar Articulo " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Articulo')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function show(Articulo $Articulo) { } public function edit(Articulo $Articulo) { }

    public function update(Request $request, $id) {
        $Articulo = Articulo::find($id);
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||Articulo|| ');
        try {
            $Articulo->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'unidad_id' => $request->unidad_id,
                'enum' => $request->enum,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo Articulo " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Articulo->nombre]));
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
                Log::alert("U -> " . Auth::user()->name . " fallo en borrar Articulo " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
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
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $th->getMessage());
        }
    }
}
