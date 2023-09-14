<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Controllers\Controller;

use App\Models\Carrera;
use App\Http\Requests\CarreraRequest;
use App\Models\Universidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CarrerasController extends Controller {

    private $modelName = 'Carrera';
    //! funciones del index
    public function MapearClasePP(&$Carreras, $numberPermissions) {
        $Carreras = $Carreras->get()->map(function ($carrera) use ($numberPermissions) {

            if ($numberPermissions < 5) { //coordinador_academico = 4
                $universidadUser = Auth::user()->universidades()->pluck('universidad_id')->toArray();
                if (!in_array($carrera->universidad_id, $universidadUser)) return null;

                if ($numberPermissions < 4) { //estudiante o profesor o coordinador_de_programa
                    $carreraUser = Auth::user()->carreras()->pluck('carrera_id')->toArray();
                    if (!in_array($carrera->id, $carreraUser)) return null;
                }
            }
            //# admin no tienen restricciones

            // $carrera->hijoPapaID = $carrera->universidad;
            $carrera->hijo = $carrera->universidad_nombre();
            $CarreraUsers = $carrera->users;
            $carrera->tresPrimeros = Myhelp::ArrayInString($CarreraUsers->pluck('name'));
            $carrera->cuantosUs = $CarreraUsers->count();
            $carrera->cuantasMaterias = $carrera->materias->count();
            return $carrera;
        })->filter();
    }

    public function fNombresTabla($numberPermissions)
    {
        $nombresTabla = [[], [], []];
        if ($numberPermissions <= 3) { //estudiante, prof, coor_prog

            array_push($nombresTabla[0], "#", "nombre", "codigo", "Universidad");
            //se puede ordenar?
            $nombresTabla[2][] = ["enum", "nombre", "codigo", "universidad_id"];
        } else {
            //0: como se ven //1 como es la BD //2orden
            $nombresTabla[0] = array_merge($nombresTabla[0], ["Acciones", "#", "nombre", "codigo", "Universidad", "Inscritos"]);
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2], [null, "enum", "nombre", "codigo", "universidad_id", null]);
        }
        //coordinador_academico
        // coordinador_de_programa
        return $nombresTabla;
    }


    public function Filtros($request, &$Carreras) {
        if ($request->has('selectedUniID') && $request->selectedUniID != 0) {
            $universidadid = Universidad::has('carreras')->where('id', $request->selectedUniID)->pluck('id')->toArray();
            $Carreras->whereIn('universidad_id', $universidadid);
        }

        if ($request->has('search')) {
            $Carreras->where('descripcion', 'LIKE', "%" . $request->search . "%");
            // $Carreras->whereMonth('descripcion', $request->search);
            // $Carreras->OrwhereMonth('fecha_fin', $request->search);
            $Carreras->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            // dd($request->field);
            $Carreras->orderBy(($request->field), $request->order);
        } else {
            $Carreras->orderBy('nombre');
        }
    }
    public function losSelect($numberPermissions) {
        if ($numberPermissions < intval(env('PERMISS_VER_FILTROS_SELEC'))) { //coordinador academico, coorPrograma,profe,estudiante
            $UniversidadSelect = Auth::user()->universidades;
        } else {
            $UniversidadSelect = Universidad::has('carreras')->get();
        }

        return [
            'UniversidadSelect' => $UniversidadSelect
        ];
    }
    //! fin funciones del index

    public function index(Request $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'carrera');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions); // retorna un numero referente a los permisos(0:error, 1:estudiante,  2: profesor, 3:++ )

        $titulo = __('app.label.Carreras');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Carreras = Carrera::query();
        $this->Filtros($request, $Carreras, $permissions);

        $perPage = $request->has('perPage') ? $request->perPage : 10;


        if ($permissions === "estudiante") {
        } else { // not estudiante
            $this->Filtros($request, $Carreras);
            //0: como se ven //1 como es la BD //2 orden
        }
        $nombresTabla = $this->fNombresTabla($numberPermissions);
        $this->MapearClasePP($Carreras, $numberPermissions);
        $Select = $this->losSelect($numberPermissions);

        $page = request('page', 1); // Current page number
        $total = $Carreras->count();
        $paginated = new LengthAwarePaginator(
            $Carreras->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $PapaSelect = Universidad::all();

        return Inertia::render('carrera/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Carreras'), 'href' => route('carrera.index')]],
            'title'             =>  $titulo,
            'filters'           =>  $request->all(['search', 'field', 'order']),
            'perPage'           =>  (int) $perPage,
            'fromController'    =>  $paginated,
            'nombresTabla'      =>  $nombresTabla,
            'PapaSelect'        => $PapaSelect,
            'numberPermissions' => $numberPermissions,
            'UniversidadSelect' => $Select['UniversidadSelect'] ?? null,

        ]);
    } //fin index

    public function AsignarUsers(Request $request, $carreraid)
    {
        $titulo = 'Seleccione los estudiantes a matricular';
        $permissions = Myhelp::EscribirEnLog($this, 'carrera');
        $carrera = Carrera::find($carreraid);
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        if ($numberPermissions < 3) { //profesor o estudiante
            return back()->with('error', __('app.label.no_permission'));
        } else { // not estudiante
            // $nombresTabla = $this->laTabla(0);
        }

        $universidad = Universidad::find($carrera->universidad_id);
        $users = User::query();

        $filtroUser = $this->UsuariosSinLosInscritos($carrera, $universidad, $request);


        // dd($carrera->users);
        return Inertia::render('carrera/AsignarUsers', [ //carpeta
            'title'          =>  $titulo,
            'breadcrumbs'    =>  [['label' => __('app.label.carreras'), 'href' => route('carrera.index')]],
            'filters'       => $request->all(['search']),

            // 'usuariosPorInscribir' =>  $users->get(),
            'usuariosPorInscribir' =>  $filtroUser->no->get(),
            'inscritos' => $filtroUser->si->get(),
            'profesinscritos' =>  $filtroUser->profesors->get(),
            'profesPorInscribir' => $filtroUser->noprofesors->get(),


            'carrera' =>  $carrera,
            'universidad' =>  $universidad,
        ]);
    }

    public function UsuariosSinLosInscritos($modelo, $universidad, $request)
    {

        $usuariosU = $universidad->users->pluck('id');
        // $usuariosDeLaCarrera = $modelo->users->pluck('id');

        $estudiantesDeLaC = $modelo->estudiantesMuchosRoles($modelo->id, true, ['estudiante']); //->pluck('users.id');
        $estudiantesDeOtraC  = User::WhereIn('id', $usuariosU)
            ->WhereHas('roles', function ($query) {
                $query->where('name', 'estudiante');
            })->whereNotIn('id', $estudiantesDeLaC->pluck('users.id'));

        $profDeLaC = $modelo->estudiantesMuchosRoles($modelo->id, true, ['profesor', 'coordinador_de_programa', 'coordinador_academico']);

        $profDeOtraC = User::WhereIn('id', $usuariosU)
            ->WhereHas('roles', function ($query) {
                $query->whereNotIn('name', ['estudiante'])
                    ->WhereNotIn('name', ['superadmin', 'admin']);
            })->whereNotIn('id', $profDeLaC->pluck('users.id'));


        if ($request->has('search')) {
            $estudiantesDeLaC->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%");
                // $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            });
            $estudiantesDeOtraC->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%");
                // $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            });
            $profDeLaC->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%");
                // $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            });
            $profDeOtraC->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%");
                // $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            });
        }
        return (object) [
            'si' => $estudiantesDeLaC,
            'no' => $estudiantesDeOtraC,
            'profesors' => $profDeLaC,
            'noprofesors' => $profDeOtraC,
        ];
    }

    //fin index y sus funciones

    public function create()
    {
    }

    public function store(CarreraRequest $request)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, 'carrera');

        try {

            if ($request->enum) {
                $enum = $request->enum;
            } else {
                $modelInstance = resolve('App\\Models\\' . $this->modelName);
                $enum = intval($modelInstance::latest('enum')->first()->enum) + 1 ?? 1;
            }
            // dd($enum);
            $Carrera = Carrera::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'universidad_id' => $request->universidad_id,
                'enum' => $enum,
                'codigo' => $request->codigo
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo Carrera " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $Carrera->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar Carrera " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Carrera')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function SubmitAsignarUsers(Request $request)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, ' carrera');

        try {
            $carrera = carrera::find($request->carreraid);
            // dd($request->selectedId);
            $carrera->users()->attach(
                $request->selectedId
            );

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " matriculo a la carrera " . count($request->selectedId) . " estudiantes correctamente");

            return redirect()->route('carrera.index')->with('success', __('app.label.created_success'));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en matricular(carrera) - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function show(Carrera $Carrera) { } public function edit(Carrera $Carrera) { }
    public function update(CarreraRequest $request, Carrera $Carrera)
    {
        $request->validate([
            'codigo' => 'required|unique:carreras,codigo,' . $Carrera->id,
        ]);

        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, ' Update carrera', '', false);
        try {
            $Carrera->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'universidad_id' => $request->universidad_id,
                'codigo' => $request->codigo,
                'enum' => $request->enum,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo Carrera " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Carrera->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar Carrera " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Carrera->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this, 'carrera');

        DB::beginTransaction();
        try {
            $Carreras = Carrera::findOrFail($id);
            $Carrerasnombre = $Carreras->nombre;
            $Carrerasid = $Carreras->id;
            $Carreras->delete();
            Log::info("U -> " . Auth::user()->name . "La carrera id:" . $Carrerasid . " y nombre:" . $Carrerasnombre . " ha sido borrada correctamente");
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $Carrerasnombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar Carrera " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Carreras')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function carreraMapa($id)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'carrera');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $Carrera = Carrera::findOrFail($id);
        
        $Unidades =[];
        foreach ($Carrera->materias_enum as $key => $value) {
            $Unidades[] = $value->unidads;
        }
        
        // dd(
        //     $Carrera->materias_enum,
        //     $Carrera,
        //     $Unidades,
        // );

        DB::beginTransaction();
        return Inertia::render('carrera/Mapa', [ //carpeta
            'numberPermissions' => $numberPermissions,
            'Carrera' => $Carrera ?? null,
            'Unidades' => $Unidades ?? null,

        ]);
    }
}
