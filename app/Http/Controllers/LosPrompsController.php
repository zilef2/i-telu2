<?php

namespace App\Http\Controllers;

use App\helpers\HelpGPT;
use App\helpers\Myhelp;
use App\Models\LosPromps;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class LosPrompsController extends Controller
{
    const LIMITE_DE_PROMPS_PER_USER = 5;
    // - MapearClasePP, Filtros

    //no necesita esto?
    public function MapearClasePP(&$LosPromps, $numberPermissions) {
        $LosPromps = $LosPromps->get();
    }

    public function Filtros($request, &$LosPromps) {
        if ($request->has('search')) {
            $LosPromps->where('principal', 'LIKE', "%" . $request->search . "%");
            // $LosPromps->whereMonth('teoricaOpractica', $request->search);
            // $LosPromps->OrwhereMonth('fecha_fin', $request->search);
            $LosPromps->orWhere('clasificacion', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $LosPromps = $LosPromps->orderBy($request->field, $request->order);
        } else {
            $LosPromps->orderBy('principal');
        }
    }

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, 'LosPromp');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $titulo = __('app.label.LosPromps');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $LosPromps = LosPromps::query();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        if ($permissions === "estudiante") {
        } else { // not estudiante
            $this->Filtros($request, $LosPromps);
        }

        $this->MapearClasePP($LosPromps, $numberPermissions);
        // dd($LosPromps,$LosPromps->get());

        $page = request('page', 1); // Current page number
        $total = $LosPromps->count();
        $paginated = new LengthAwarePaginator(
            $LosPromps->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        // $limitePromps = self::LimiteDePromps($numberPermissions);

        return Inertia::render('LosPromp/Index', [ //carpeta
            'breadcrumbs'           =>  [['label' => __('app.label.LosPromps'), 'href' => route('LosPromp.index')]],
            'title'                 =>  $titulo,
            'filters'               =>  $request->all(['search', 'field', 'order']),
            'perPage'               =>  (int) $perPage,
            'fromController'        =>  $paginated,
            'numberPermissions'     =>  $numberPermissions,
            // 'limitePromps'          =>  $limitePromps,
        ]);
    } //fin index


    public function LimiteDePromps($numberPermissions) {
        if ($numberPermissions < 5) {
            $prompsUser = Auth::user()->LimiteDePromps()->get()->count();
            return $prompsUser < (int)(self::LIMITE_DE_PROMPS_PER_USER + $numberPermissions * 2);
        }
        return true;
    }

    public function store(Request $request) {
        DB::beginTransaction();
        $permissions = Myhelp::EscribirEnLog($this, 'LosPromp');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        try {
            $IntegerMispromps = 0;
            if ($numberPermissions < 4) {
                $IntegerMispromps = Auth::user()->LosPromps()->count();
                $limitePromps = (int)(env('LIMITEPROMPSPERSONA'));
                if ($IntegerMispromps >= $limitePromps) {
                    return back()->with('warning', __('app.label.created_error', ['nombre' => 'Numero maximo de registros']));
                }
            }

            $promptTemporal = $request->principal;
            $contador = HelpGPT::contarModificarP($promptTemporal);
            if ($contador['corchetes'] != 0 || $contador['parentesis'] != 0) {
                $LosPromp = LosPromps::create([
                    'principal' => $request->principal,
                    'teoricaOpractica' => 'teorica',
                    'clasificacion' => 'General',
                    // 'teoricaOpractica' => $request->teoricaOpractica['value'],
                    // 'clasificacion' => $request->clasificacion['value'],
                    'tokensAproximados' => $request->tokensAproximados,
                ]);
                DB::commit();
                Log::info("U -> " . Auth::user()->name . " Guardo LosPromp " . $request->nombre . " correctamente");
                return back()->with('success', __('app.label.created_successfully2', ['nombre' => $LosPromp->nombre]));
            } else {
                return back()->with('warning', 'Advertencia: La instruccion no tiene variables');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar LosPromp  - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());

            if (config('app.env') === 'production') {
                return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.LosPromp')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            } else {
                return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.LosPromp')]) . $th);
            }
        }
    }

    public function show(LosPromps $LosPromp)
    {
    }
    public function edit(LosPromps $LosPromp)
    {
    }

    public function update(Request $request, $id)
    {
        $LosPromp = LosPromps::find($id);
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        try {
            // dd($LosPromp,$request->nombre);
            $LosPromp->update([
                'principal' => $request->principal,
                // 'teoricaOpractica' => $request->teoricaOpractica,
                // 'clasificacion' => $request->clasificacion,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo LosPromp " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $LosPromp->nombre]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar LosPromp " . $request->nombre . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $LosPromp->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    // public function destroy(LosPromp $LosPromp)
    public function destroy($id)
    {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();

        try {
            $LosPromps = LosPromps::findOrFail($id);

            Myhelp::EscribirEnLog($this, get_called_class(), "el LosPromp id:" . $id . " y nombre:" . $LosPromps->nombre . " ha sido borrada correctamente", false);

            $LosPromps->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $LosPromps->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar LosPromp " . $id . " - " . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.LosPromps')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }
}
