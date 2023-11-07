<?php

namespace App\Http\Controllers;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Http\Requests\PendienteRequest;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Imports\PendienteImport;
use App\Imports\PersonalImport;
use App\Imports\PersonalUniversidadImport;
use App\Models\Pendiente;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Universidad;
use App\Models\User;
use App\Models\UsuarioPendientesPago;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class UsuarioPendientesPagosController extends Controller
{

    private $modelName = 'UsuarioPendientesPago';
    //! funciones del index
    public function MapearClasePP(&$pendientes, $numberPermissions) {
        $pendientes = $pendientes->get()->map(function ($pendiente) use ($numberPermissions) {
            $pendiente->nombre_user = $pendiente->nombre_user();
            return $pendiente;
        })->filter();
    }

    public function fNombresTabla($numberPermissions)
    {
        $nombresTabla = [[], [], []];
        array_push($nombresTabla[0], "nombre", "fecha peticion", "fecha aprovacion", "Valor Total");

        //se puede ordenar?
        $nombresTabla[2][] = ["nombre_user","fecha_peticion", "fecha_aprovacion", "valorTotal"];
        return $nombresTabla;
    }


    public function Filtros($request, &$pendientes) {
        if ($request->has('selectedUniID') && $request->selectedUniID != 0) {
            $universidadid = Universidad::has('pendientes')->where('id', $request->selectedUniID)->pluck('id')->toArray();
            $pendientes->whereIn('universidad_id', $universidadid);
        }

        if ($request->has('search')) {
            $pendientes->where('descripcion', 'LIKE', "%" . $request->search . "%");
            // $pendientes->whereMonth('descripcion', $request->search);
            // $pendientes->OrwhereMonth('fecha_fin', $request->search);
            $pendientes->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            // dd($request->field);
            $pendientes->orderBy(($request->field), $request->order);
        } else {
            $pendientes->orderBy('universidad_id')->orderBy('enum')->orderBy('nombre');
        }
    }
    public function losSelect($numberPermissions) {
        if ($numberPermissions < intval(env('PERMISS_VER_FILTROS_SELEC'))) { //coordinador academico, coorPrograma,profe,estudiante
            $UniversidadSelect = Auth::user()->universidades;
        } else {
            $UniversidadSelect = Universidad::has('pendientes')->get();
        }

        return [
            'UniversidadSelect' => $UniversidadSelect
        ];
    }
    //! fin funciones del index

    public function index(Request $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'pendiente');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions); // retorna un numero referente a los permisos(0:error, 1:estudiante,  2: profesor, 3:++ )

        $titulo = __('app.label.Pendientes');
        $pendientes = UsuarioPendientesPago::query();
//        $this->Filtros($request, $pendientes, $permissions);

        $perPage = $request->has('perPage') ? $request->perPage : 10;

        $nombresTabla = $this->fNombresTabla($numberPermissions);
        $this->MapearClasePP($pendientes, $numberPermissions);
//        $Select = $this->losSelect($numberPermissions);

        $page = request('page', 1); // Current page number
        $total = $pendientes->count();

        $paginated = new LengthAwarePaginator(
            $pendientes->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

//        $PapaSelect = Universidad::all();

        return Inertia::render('pendiente/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Pendientes'), 'href' => route('pendiente.index')]],
            'title'             =>  $titulo,
            'filters'           =>  $request->all(['search', 'field', 'order']),
            'perPage'           =>  (int) $perPage,
            'fromController'    =>  $paginated,
            'nombresTabla'      =>  $nombresTabla,
//            'PapaSelect'        => $PapaSelect,
            'numberPermissions' => $numberPermissions,

        ]);
    } //fin index

    public function AceptarUsers($pendienteid)
    {
        try{
            $pendiente = UsuarioPendientesPago::find($pendienteid);
            $usuario = User::find($pendiente->user_id);
            Myhelp::EscribirEnLog($this, 'Confirmacion de pago | U: ' . $usuario->name. ' id'.$usuario->id);
            $plan = Plan::find($pendiente->plan_id);

            $gral = $usuario->limite_token_general + $plan->tokens;
            $leccion = $usuario->limite_token_leccion + $plan->tokens;
            $usuario->update([
                'limite_token_general' => $gral,
                'limite_token_leccion' => $leccion,
            ]);
            $pendiente->delete();

            return redirect()->route('éndiente.index')->with('success', 'usuario actualizado. Ahora tiene '.$leccion.' tokens para gastart');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . $usuario->name . " fallo en aceptar " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.pendiente')]) . '__'. $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    //fin index y sus funciones

    public function create()
    {
    }

    public function store(PendienteRequest $request)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, 'pendiente');

        try {

            $input = $request->all();
            $pendiente = UsuarioPendientesPago::create($input);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo Pendiente " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $pendiente->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar Pendiente " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Pendiente')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }


    public function show(Pendiente $pendiente) {

    }

    public function edit(Pendiente $pendiente) { }
    public function update(PendienteRequest $request, Pendiente $pendiente)
    {
        $request->validate([
            'codigo' => 'required|unique:pendientes,codigo,' . $pendiente->id,
        ]);

        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, ' Update pendiente', '', false);
        try {
            $input = $request->all();
            $pendiente->update($input);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo Pendiente " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $pendiente->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar Pendiente " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $pendiente->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this, 'pendiente');

        DB::beginTransaction();
        try {
            $pendientes = UsuarioPendientesPago::findOrFail($id);
            $pendientesnombre = $pendientes->nombre;
            $pendientesid = $pendientes->id;
            $pendientes->delete();
            Log::info("U -> " . Auth::user()->name . "La pendiente id:" . $pendientesid . " y nombre:" . $pendientesnombre . " ha sido borrada correctamente");
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $pendientesnombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar Pendiente " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Pendientes')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
}
