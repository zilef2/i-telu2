<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Controllers\Controller;

use App\Models\Universidad;
use App\Http\Requests\UniversidadRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UniversidadsController extends Controller
{
    private $modelName = 'Universidad';

    //! index functions
    public function MapearClasePP(&$universidads, $numberPermissions)
    {
        $universidads = $universidads->get()->map(function ($universidad) use ($numberPermissions) {

            if ($numberPermissions < 5) { //everybody menos los admin
                $universidadUser = Auth::user()->universidades()->pluck('universidads.id')->toArray();
                if (!in_array($universidad->id, $universidadUser)) return null;
            }

            $universidad->tresPrimeros = Myhelp::ArrayInString($universidad->users->pluck('name'));

            $universidad->cuantosUs = $universidad->users->count();
            $universidad->cuantasCarreras = intval($universidad->carreras->count());

            return $universidad;
        })->filter();
        // dd($universidads);
    }

    public function fNombresTabla($numberPermissions)
    {
        if ($numberPermissions < 5) { //coor_academico TO estudiante
            $nombresTabla[0] = ["#", "nombre", "codigo", "# Inscritos"];
            $nombresTabla[2] = ["enum", "nombre", "codigo", null];
            return $nombresTabla;
        }

        $nombresTabla = [ //0: como se ven //1 como es la BD //2 orden
            ["Acciones"],
            [],
            [null]
        ];
        $nombresTabla[0] = array_merge($nombresTabla[0], ["#", "nombre", "codigo", "# Inscritos", "Inscritos"]);
        $nombresTabla[2] = array_merge($nombresTabla[2], ["enum", "nombre", "codigo", null, null]);
        return $nombresTabla;
    }

    public function Filtros($request, &$Universidads)
    {
        if ($request->has('search')) {
            // $Universidads->whereMonth('descripcion', $request->search);
            // $Universidads->OrwhereMonth('fecha_fin', $request->search);
            $Universidads->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $Universidads->orderBy($request->field, $request->order);
        } else {
            $Universidads->orderBy('nombre');
        }
    }
    // public function losSelect() {}

    public function index(Request $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'INDEX:universidad');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.Universidads');
        $Universidads = Universidad::query();

        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $nombresTabla = $this->fNombresTabla($numberPermissions);
        if ($permissions === "estudiante") {
        } else { // not estudiante
            $this->Filtros($request, $Universidads);
        }

        $this->MapearClasePP($Universidads, $numberPermissions);

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $Universidads->forPage($page, $perPage),
            $Universidads->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('universidad/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [[
                'label' => __('app.label.Universidads'),
                'href' => route('universidad.index')
            ]],
            'nombresTabla'   =>  $nombresTabla,
            'numberPermissions'   =>  $numberPermissions,
        ]);
    } //fin index

    public function AsignarUsers(Request $request, $universidadid)
    { //get

        $titulo = 'Seleccione el personal a matricular/desvincular';
        $permissions = Myhelp::EscribirEnLog($this, 'universidad');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        if ($numberPermissions < 3) { //profesor o estudiante
            Myhelp::EscribirEnLog($this, 'Criticou');

            return back()->with('error', __('app.label.no_permission'));
        } else { // not estudiante
            // $nombresTabla = $this->laTabla(0);
        }

        $universidad = universidad::find($universidadid);
        $filtroUser = $this->UsuariosSinLosInscritos($universidad, $request);

        return Inertia::render('universidad/AsignarUsers', [ //carpeta
            'title'          =>  $titulo,
            'breadcrumbs'    =>  [['label' => __('app.label.universidad'), 'href' => route('universidad.index')]],
            'filters'       => $request->all(['search']),

            'usuariosPorInscribir' =>  $filtroUser->no->get(),
            'inscritos' => $filtroUser->si->get(),
            'profesinscritos' =>  $filtroUser->profesors->get(),
            'profesPorInscribir' => $filtroUser->noprofesors->get(),

            'universidad' =>  $universidad,
            'numberPermissions' =>  $numberPermissions,
            // 'UniversidadSelect' => $UniversidadSelect,
        ]);
    }


    public function UsuariosSinLosInscritos($modelo, $request)
    {
        $estudiantesDeLaU = $modelo->estudiantesMuchosRoles($modelo->id, true, ['estudiante']); //->pluck('users.id');
        $estudiantesDeOtraU = User::WhereHas('roles', function ($query) {
            $query->where('name', 'estudiante');
        })
            ->whereNotIn('id', $estudiantesDeLaU->pluck('users.id'));

        $profDeLaU = $modelo->estudiantesMuchosRoles($modelo->id, true, ['profesor', 'coordinador_de_programa', 'coordinador_academico']);

        $profDeOtraU = User::WhereHas('roles', function ($query) {
            $query->whereNotIn('name', ['estudiante'])
                ->WhereNotIn('name', ['superadmin', 'admin']);
        })->whereNotIn('id', $profDeLaU->pluck('users.id'));


        if ($request->has('search')) {
            $estudiantesDeLaU->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%");
                // $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            });
            $estudiantesDeOtraU->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%");
                // $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            });
            $profDeLaU->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%");
                // $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            });
            $profDeOtraU->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%");
                // $query->orWhere('email', 'LIKE', "%" . $request->search . "%");
                $query->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            });
        }
        return (object) [
            'si' => $estudiantesDeLaU,
            'no' => $estudiantesDeOtraU,
            'profesors' => $profDeLaU,
            'noprofesors' => $profDeOtraU,
        ];
    }

    public function create()
    {
    }

    public function store(UniversidadRequest $request)
    {
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||Universidad|| ');

        try {
            if ($request->enum === null) {
                // dd($request->enum);
                $modelInstance = resolve('App\\Models\\' . $this->modelName);
                $enum = $modelInstance::latest('enum')->first()->enum ?? 1;
                $enum++;
            } else {
                $enum = $request->enum;
            }

            $Universidad = Universidad::create([
                'nombre' => $request->nombre,
                'enum' => $enum,
                'codigo' => $request->codigo,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo Universidad " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $Universidad->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar Universidad " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function SubmitAsignarUsers(Request $request)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, ' universidad');

        try {
            $Universidad = Universidad::find($request->universidadid);
            // dd($request->selectedId);
            $Universidad->users()->attach(
                $request->selectedId
            );

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " matriculo a la universidad " . count($request->selectedId) . " estudiantes correctamente");

            return back()->with('success', 'Usuarios asignados correctamente');
            // return redirect()->route('universidad.index')->with('success', __('app.label.created_success'));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en matricular(universidad) - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }


    public function toEraseId(Request $request)
    {
        DB::beginTransaction();
        $permission = Myhelp::EscribirEnLog($this, ' universidad', 'Inicio de quitar estudiante de universidad');

        try {
            if ($permission == 'coordinador_academico' || $permission == 'coordinador_academico' || $permission == 'admin' || $permission = 'superadmin') {

                $Universidad = Universidad::find($request->universidadid);
                // dd($request->selectedId);
                $Universidad->users()->detach(
                    $request->toEraseId
                );

                DB::commit();
                Log::info("U -> " . Auth::user()->name . " desmatriculo a la universidad " . count($request->selectedId) . " estudiantes correctamente");

                return back()->with('success', 'Usuarios desvinculados correctamente');
                // return redirect()->route('universidad.index')->with('success', 'Usuarios desmatriculados con exito');
            }
            Log::critical("U -> " . Auth::user()->name . " desmatriculo a la universidad " . count($request->selectedId) . " estudiantes. Fallo en seguridad.");
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en matricular(universidad) - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Universidad')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function show(Universidad $Universidad)
    {
    }
    public function edit(Universidad $Universidad)
    {
    }
    public function update(UniversidadRequest $request, Universidad $Universidad)
    {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, ' UPDATE:universidad ', '', false);

        $request->validate([
            'codigo' => 'required|unique:universidads,codigo,' . $Universidad->id,
        ]);

        try {
            $Universidad->update([
                'nombre' => $request->nombre,
                'enum' => $request->enum,
                'codigo' => $request->codigo,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo Universidad " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Universidad->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar Universidad " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Universidad->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Universidad  $Universidad
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Universidad $Universidad)
    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this, 'DELETE:universidad', '', false);
        DB::beginTransaction();

        try {
            // si se la rechazaron, tendra que hacer uno nuevo
            $Universidads = Universidad::findOrFail($id);
            $Universidads->delete();
            DB::commit();
            Myhelp::EscribirEnLog($this, 'universidad', 'borro Universidad id:' . $id . ' correctamente', false);
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $Universidads->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar Universidad " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Universidads')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
}
