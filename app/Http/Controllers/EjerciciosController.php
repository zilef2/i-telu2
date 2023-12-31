<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use Inertia\Inertia;

use App\Models\Ejercicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\EjercicioRequest;
use App\Models\Subtopico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EjerciciosController extends Controller
{
    private $modelName = 'Ejercicio';

    // - MapearClasePP, Filtros

    public function MapearClasePP(&$ejercicios, $numberPermissions)
    {
        if ($numberPermissions < 4) {
            $subtemas = Auth::user()->materias->flatMap(function ($materia) {
                return collect($materia->Tsubtemas);
            });
            $ejercicios = $subtemas->flatMap(function ($subtema) {
                return collect($subtema->ejercicios);
            });
        } else {
            $ejercicios = $ejercicios->get()->map(function ($ejercicio) {
                $ejercicio->hijo = $ejercicio->subtopico_nombre();
                return $ejercicio;
            })->filter();
        }
    }
    public function Filtros($request, &$ejercicios)
    {
        if ($request->has('search')) {
            $ejercicios->where('descripcion', 'LIKE', "%" . $request->search . "%");
            // $ejercicios->whereMonth('descripcion', $request->search);
            // $ejercicios->OrwhereMonth('fecha_fin', $request->search);
            $ejercicios->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $ejercicios->orderBy($request->field, $request->order);
        } else {
            $ejercicios->orderBy('subtopico_id')->orderBy('enum')->orderBy('nombre');
        }
    }


    public function index(Request $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'ejercicio');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $titulo = __('app.label.ejercicios');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $ejercicios = Ejercicio::query();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        if ($permissions === "estudiante") {
            //todo: validar que no pueda buscar
        } else { // not estudiante

            $this->Filtros($request, $ejercicios);
        }

        $this->MapearClasePP($ejercicios, $numberPermissions);
        // dd($ejercicios);
        $page = request('page', 1); // Current page number
        $total = $ejercicios->count();
        $paginated = new LengthAwarePaginator(
            $ejercicios->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('ejercicio/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [[
                'label' => __('app.label.ejercicios'),
                'href' => route('ejercicio.index')
            ]],
            'subUnidadsSelect'   =>  Subtopico::all(),
            'numberPermissions'   =>  $numberPermissions,

        ]);
    } //fin index


    public function create()
    {
    }

    public function store(EjercicioRequest $request)
    {
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||ejercicio|| ');
        $modelInstance = resolve('App\\Models\\' . $this->modelName);
        $ultima = $modelInstance::latest('enum')->first();
        try {
            $ejercicio = Ejercicio::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'subtopico_id' => $request->subtopico_id,
                'enum' => $ultima->enum + 1
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo ejercicio " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $ejercicio->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar ejercicio " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.ejercicio')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function show(ejercicio $ejercicio)
    {
    }
    public function edit(ejercicio $ejercicio)
    {
    }

    public function update(Request $request, $id)
    {
        $ejercicio = Ejercicio::find($id);
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||ejercicio|| ');
        try {
            // dd($ejercicio,$request->nombre);
            $ejercicio->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'subtopico_id' => $request->subtopico_id,
                'enum' => $request->enum,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo ejercicio " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $ejercicio->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar ejercicio " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $ejercicio->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    // public function destroy(ejercicio $ejercicio)
    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();

        try {
            $ejercicios = Ejercicio::findOrFail($id);

            Myhelp::EscribirEnLog($this, get_called_class(), "el ejercicio id:" . $id . " y nombre:" . $ejercicios->nombre . " ha sido borrada correctamente", false);

            $ejercicios->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $ejercicios->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar ejercicio " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.ejercicios')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
}
