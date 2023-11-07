<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

use App\Models\Parametro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ParametrosController extends Controller
{

    public $respuestaLimite = 'Limite de tokens';
    public $respuestaLarga = 'La respuesta es demasiado extensa';
    public $MAX_USAGE_RESPUESTA = 550;
    public $MAX_USAGE_TOTAL = 600;

    public function fNombresTabla() {
        $nombresTabla = [ //0: como se ven //1 como es la BD //2 ordenables
            ["Acciones"],
            [],
            ["Acciones"]
        ];
        array_push($nombresTabla[0], "prompEjercicios", "prompObjetivos", "NumeroTicketDefecto");
        $nombresTabla[2][] = ["s_prompEjercicios", "s_prompObjetivos", "i_NumeroTicketDefecto"];
        return $nombresTabla;
    }

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this,' parametro');

        $titulo = __('app.label.parametros');
        if($permissions == 'admin' || $permissions == 'superadmin'){
            $nombresTabla = $this->fNombresTabla();

            return Inertia::render('parametro/Index', [ //carpeta
                'title'          =>  $titulo,
                'filters'        =>  $request->all(['search', 'field', 'order']),
                'perPage'        =>  1,
                'fromController' =>  Parametro::all(),
                'breadcrumbs'    =>  [['label' => __('app.label.parametros'), 'href' => route('parametro.index')]],
                'nombresTabla'   =>  $nombresTabla,
            ]);
        }else{
            Myhelp::EscribirEnLog($this,' parametro','en el index nos hackearon ',false,true); //!usefull
        }

    } //fin index

    public function create() { }
    public function store(Request $request) {
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        try {
            $parametro = Parametro::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => $request->descripcion,
                'materia_id' => $request->materia_id,
            ]);
            DB::commit();
            Log::info("U -> " . Auth::user()->name . " Guardo parametro " . $request->nombre . " correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $parametro->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en Guardar parametro " . $request->nombre . " - " . $th->getMessage().' L:'.$th->getLine());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.parametro')]) . $th->getMessage().' L:'.$th->getLine());
        }
    }

    public function show(parametro $parametro) { }
    public function edit(parametro $parametro) { }

    public function update(Request $request, $id) {
        $parametro = Parametro::find($id);
        DB::beginTransaction();
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);
        try {
            // dd($parametro,$request->nombre);
            $numticke = $request->NumeroTicketDefecto < 0 ? $request->NumeroTicketDefecto * -1 : $request->NumeroTicketDefecto;
            $parametro->update([
                'prompEjercicios' => $request->prompEjercicios,
                'prompObjetivos' => $request->prompObjetivos,
                'pMejoraContinua' => $request->pMejoraContinua,
                'NumeroTicketDefecto' => $numticke
            ]);

            DB::commit();
            Log::info("U -> " . Auth::user()->name . " actualizo parametro " . $request->id . " correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $parametro->id]));
        } catch (\Throwable $th) {

            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en actualizar parametro " . $request->nombre . " - " . $th->getMessage().' L:'.$th->getLine());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $parametro->nombre]) . $th->getMessage().' L:'.$th->getLine());
        }
    }

    // public function destroy(parametro $parametro)
    public function destroy($id) {
        Myhelp::EscribirEnLog($this, get_called_class(), '', false);

        DB::beginTransaction();

        try {
            $parametros = Parametro::findOrFail($id);
            Myhelp::EscribirEnLog($this, get_called_class(), "La parametro id:" . $id . " y nombre:" . $parametros->nombre . " ha sido borrada correctamente", false);


            // $parametros->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2', ['nombre' => $parametros->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> " . Auth::user()->name . " fallo en borrar parametro " . $id . " - " . $th->getMessage().' L:'.$th->getLine());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.parametros')]) . $th->getMessage().' L:'.$th->getLine());
        }
    }
}
