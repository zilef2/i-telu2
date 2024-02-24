<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\helpers\HelpPlan;
use App\helpers\Myhelp;
use App\Models\UsuarioPendientesPago;
use Carbon\Carbon;
use Inertia\Inertia;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Universidad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class PlansController extends Controller{

    private $modelName = 'Plan';
    private $yaEstaFiltrada = false;

    // - MapearClasePP, Filtros, losSelect

    public function MapearClasePP(&$Plans, $numberPermissions): void {
        $Plans->orderByRaw("nombre = 'demo' DESC");

        $Plans = $Plans->get();

        // $Plans = $Plans->map(function ($Plan) {
            // $Plan->hijo = $Plan->user_name();
        //     return $Plan;
        // })->filter();
    }

    public function losSelect($numberPermissions) {
        if ($numberPermissions < (int)env('PERMISS_VER_FILTROS_SELEC')) { //coorPrograma,profe,estudiante
            $UserSelect = Plan::WhereHas('Plans')->get();
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

        $permissions = Myhelp::EscribirEnLog($this, 'Plan');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $titulo = __('app.label.Plans');

        $Plans = Plan::query();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        /*if ($numberPermissions === 1) {
        } else { // not estudiante
        }*/

        $this->MapearClasePP($Plans, $numberPermissions);
        // $HijoSelec = $this->losSelect($numberPermissions);

        return Inertia::render('Plan/Index', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Plans'), 'href' => route('Plan.index')]],
            'title'             =>  $titulo,
            'filters'           =>  $request->all([
                                        'search',
                                        'field',
                                        'order'
                                    ]),
            'perPage'           =>  (int) $perPage,
            'fromController'    =>  $Plans,
            // 'HijoSelec'         =>  $HijoSelec['HijoSelec'],
            'numberPermissions' =>  $numberPermissions,
        ]);
    } //fin index


    public function create(Request $request) {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Plan'));

        $universidades = Universidad::all();
        // $opcionesU = Myhelp::NEW_turnInSelectID($universidades,' una universidad');

        // //# generar()
        // $ValoresGenerarSeccion = Inertia::lazy(fn () => HelpPlan::OptimizarResumenOIntroduccion(
        //     $request->elTexto,
        //     $request->materia,
        //     $request->tipoTexto
        // ));

        return Inertia::render('Plan/Create', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Plans'), 'href' => route('Plan.create')]],
            'title'             =>  'Creando nuevo Plan',
            // 'Selects'           =>  [
            //     'opcionesU'             =>  $opcionesU,
            //     'opcionesCarreras'      =>  $opcionesCarreras,
            //     'opcionesAsignatura'    =>  $opcionesAsignatura,
            // ],

            'numberPermissions'         =>  $numberPermissions,
        ]);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Plan'));

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
            $finalInput['user_id'] = auth()->user()->id;
            $finalInput['version'] = 1;
            $Plan = Plan::create($finalInput);
            DB::commit();
            $mensaje = "U -> " . Auth::user()->name . " Guardo Plan " . $request->nick[0] . " correctamente";
            Myhelp::EscribirEnLog($this, $mensaje);

            return redirect()->route('Plan.index')->with('success', __('app.label.created_successfully2', ['nombre' => $Plan->nick]));

        } catch (\Throwable $th) {
            DB::rollback();
            $problema = " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar Plan " . $request->nick . $problema);
            return redirect('Plan/create')->with('error', __('app.label.created_error', ['nombre' => __('app.label.Plan')]) . $problema);
        }
    }

    public function show(Plan $Plan) { } public function edit(Plan $Plan) { }

    public function update(Request $request, $id) {
        $Plan = Plan::find($id);
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\', get_class($this)));
        $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC . 'U:' . Auth::user()->name . ' ||Plan|| ');
        try {
            $Plan->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'unidad_id' => $request->unidad_id,
                'enum' => $request->enum,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo Plan " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Plan->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar Plan " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Plan->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function ComprarPlan(Request $request) {
        DB::beginTransaction();
        $Plan = Plan::find($request->planid);
        $userAu = Myhelp::AuthU();
        try {
            $PivotElplanPendiente = UsuarioPendientesPago::Where('user_id', $userAu->id)->get();
            $YaTienePendiente = $PivotElplanPendiente->count();
            $MensajePostCompra =
                'Anteriormente usted solicitó el plan <b> '.$Plan->nombre.'</b>'.
                '.<br><br> Para activar el plan, realize la transferencia a la siguiente cuenta de ahorros 123456, Banco: Su plan sera activado prontamente';//todo: urgente
            $yaTienePlanPedido = $YaTienePendiente === 0;
            session(['YaTienePlan' => $yaTienePlanPedido]);
            if($yaTienePlanPedido){
                UsuarioPendientesPago::create([
                    'fecha_peticion' => Carbon::now(),
                    'fecha_aprovacion' => null,
                    'valorTotal' => $Plan->valor,
                    'tokensComprados' => $Plan->tokens,
                    'user_id' => $userAu->id,
                    'plan_id' => $Plan->id,
                ]);

                Log::info("U -> " . $userAu->name . " esta pendiente por pagar el Plan " . $Plan->nombre . "");
                session(['SuPlan' => $MensajePostCompra]);
            }else{
                Log::info("U -> " . $userAu->name . " Quiso comprar el plan " . $Plan->nombre . ". Pero ya tenia pendiente uno");
                session(['SuPlan' => $MensajePostCompra]);
            }
            DB::commit();

            return redirect()->route('MensajePlanPendiente');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . $userAu->name . " fallo en actualizar Plan " . $Plan->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Plan->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function MensajePlanPendiente(Request $request) {

        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'Plan'));
        $titulo = __('app.label.PlanPendiente');
        $elUsuario = Myhelp::AuthU();

        return Inertia::render('Plan/PlanPendiente', [ //carpeta
            'breadcrumbs'       =>  [['label' => __('app.label.Plans'), 'href' => route('Plan.index')]],
            'title'             =>  $titulo,
            'filters'           =>  $request->all([
                'search',
                'field',
                'order'
            ]),
            'numberPermissions' =>  $numberPermissions,
            'elUsuario'         => $elUsuario,
            'SuPlan'            => session('SuPlan') ?? '',
            'YaTienePlan'       => session('YaTienePlan') ?? false
        ]);
    } //fin MensajePlanPendiente

    public function destroy($id) {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();
        try {
            $Plans = Plan::findOrFail($id);

            $Plans->delete();
            Myhelp::EscribirEnLog($this, get_called_class(), "La Plan id:" . $id . " y nombre:" . $Plans->nombre . " ha sido borrada correctamente", false);
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $Plans->nombre]));
        } catch (\Throwable $th) {
            // 23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row
            if ($th->getCode() == 23000) {
                Log::info("U -> " . Auth::user()->name . " fallo en borrar tema " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
                DB::rollback();
                return back()->with('warning', 'Debe borrar los promps asociados a este tema antes de proceder. ');
            } else {
                DB::rollback();
                $problema = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
                Log::alert("U -> " . Auth::user()->name . " fallo en borrar Plan " . $id . " - " . $problema);
                return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Plans')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            }
        }
    }
    public function destroyBulk(Request $request) {
        try {
            $user = Plan::whereIn('id', $request->id);
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' '. $this->modelName]));
        } catch (\Throwable $th) {
            $problema = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile();
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $problema);
        }
    }
}
