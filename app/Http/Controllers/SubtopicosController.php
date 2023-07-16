<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use Inertia\Inertia;

use App\Models\Subtopico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubtopicoRequest;
use App\Models\Materia;
use App\Models\Unidad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class subtopicosController extends Controller
{

    // - MapearClasePP, Filtros, losSelect

    public function MapearClasePP(&$subtopicos, $numberPermissions) {
        if ($numberPermissions < 2) {
            $subtopicos = Auth::user()->materias->flatMap(function ($materia) {
                return collect($materia->Tsubtemas);
            });
            // dd($subtemasUser);
            // if(!in_array($subtopico->id,$subtemasUser)) return null;
        } else {
            $subtopicos = $subtopicos->get()->map(function ($subtopico) {
                $subtopico->hijo = $subtopico->tema_nombre();
                return $subtopico;
            })->filter();
        }
    }
    public function Filtros($request, &$subtopicos,&$showMateria) {
        if ($request->has('selectedUnidadID') && $request->selectedUnidadID != 0) {
            $showMateria = Materia::find(Unidad::find($request->selectedUnidadID)->materia_id);
            // dd($request->selectedUni);
            $unidadsid = Unidad::has('subtopicos')->where('id', $request->selectedUnidadID)->pluck('id')->toArray();
            $subtopicos->whereIn('unidad_id', $unidadsid);
        }

        if ($request->has('search')) {
            $subtopicos->where('descripcion', 'LIKE', "%" . $request->search . "%");
            // $subtopicos->whereMonth('descripcion', $request->search);
            // $subtopicos->OrwhereMonth('fecha_fin', $request->search);
            $subtopicos->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $subtopicos->orderBy($request->field, $request->order);
        } else {
            $subtopicos->orderBy('nombre');
        }
    }
        public function losSelect($numberPermissions) {
            if($numberPermissions < intval(env('PERMISS_VER_FILTROS_SELEC'))){ //coorPrograma,profe,estudiante
                $UnidadsSelect = Auth::user()->unidads();
            }else{
                $UnidadsSelect = Unidad::all();
            }
            return [
                'UnidadsSelect' => $UnidadsSelect
            ];
        }

    // -fin : MapearClasePP, Filtros

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, 'subtopico');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.subtopicos');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $subtopicos = Subtopico::query();
        $showMateria = null; //muestra la materia a la que pertenece la unidad seleccionada

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        if ($permissions === "estudiante") {
        } else { // not estudiante
            $this->Filtros($request, $subtopicos,$showMateria);
        }
        $this->MapearClasePP($subtopicos, $numberPermissions);
        // dd($subtopicos);
        $page = request('page', 1); // Current page number
        $total = $subtopicos->count();
        $paginated = new LengthAwarePaginator(
            $subtopicos->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $Select = $this->losSelect($numberPermissions);

        return Inertia::render('subtopico/Index', [ //carpeta
            'breadcrumbs'       =>  [[ 'label' => __('app.label.subtopicos'), 'href' => route('subtopico.index') ]],
            'title'             =>  $titulo,
            'filters'           =>  $request->all(['search', 'field', 'order']),
            'perPage'           =>  (int) $perPage,
            'fromController'    =>  $paginated,
            'UnidadsSelect'     =>  $Select['UnidadsSelect'],
            'numberPermissions' =>  $numberPermissions,
            'showMateria'       =>  $showMateria,
        ]);
    } //fin index

    public function create() { }
    public function store(SubtopicoRequest $request) {
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||subtopico|| ');

        try {
            $subtopico = Subtopico::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'unidad_id' => $request->unidad_id,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo subtopico " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $subtopico->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar subtopico " . $request->nombre . " - " . $th->getMessage());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.subtopico')]) . $th->getMessage());
        }
    }

    public function show(subtopico $subtopico) { }
    public function edit(subtopico $subtopico) { }

    public function update(Request $request, $id) {
        $subtopico = Subtopico::find($id);
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||subtopico|| ');
        try {
            // dd($subtopico,$request->nombre);
            $subtopico->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'unidad_id' => $request->unidad_id,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo subtopico " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $subtopico->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar subtopico " . $request->nombre . " - " . $th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $subtopico->nombre]) . $th->getMessage());
        }
    }

    // public function destroy(subtopico $subtopico)
    public function destroy($id) {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();
        try {
            $subtopicos = Subtopico::findOrFail($id);
            $subtopicos->delete();
            Myhelp::EscribirEnLog($this, get_called_class(), "La subtopico id:" . $id . " y nombre:" . $subtopicos->nombre . " ha sido borrada correctamente", false);
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $subtopicos->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar subtopico " . $id . " - " . $th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.subtopicos')]) . $th->getMessage());
        }
    }
}
