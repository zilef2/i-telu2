<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Ejercicio;
use App\Http\Requests\EjercicioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EjerciciosController extends Controller
{
    public function index(Request $request)
    {
        $titulo = __('app.label.Ejercicios');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Ejercicios = Ejercicio::query();
        
        if($permissions === "operator") { //admin | validador
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD //2??
                ["Acciones","#","Centro costo","inicio", "fin", "horas trabajadas", "valido", "observaciones"],
                ["t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "b_valido", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,"t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "b_valido", "s_observaciones"] //campos ordenables
            ];

            // $startDate = Carbon::now()->startOfWeek();
            // $endDate = Carbon::now()->endOfWeek();
        }else{ // not operator
            $titulo = $this->CalcularTituloQuincena();
            
            if ($request->has('search')) {
                $Ejercicios->whereMonth('fecha_ini', $request->search);
                $Ejercicios->OrwhereMonth('fecha_fin', $request->search);
                $Ejercicios->OrwhereYear('fecha_ini', $request->search);
                $Ejercicios->OrwhereYear('fecha_fin', $request->search);
                $Ejercicios->OrwhereDay('fecha_ini', $request->search);
                $Ejercicios->OrwhereDay('fecha_fin', $request->search);
                // $Ejercicios->orWhere('fecha_fin', 'LIKE', "%" . $request->search . "%");
            }
            if ($request->has(['field', 'order'])) {
                $Ejercicios->orderBy($request->field, $request->order);
            }else{
                $Ejercicios->orderBy('fecha_ini');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio",       "fin",        "horas trabajadas",   "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,"b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean 
            ];
        }


        return Inertia::render('Ejercicios/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Ejercicios->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Ejercicios'), 'href' => route('Ejercicios.index')]],
            'nombresTabla'   =>  $nombresTabla,

        ]);
    }//fin index

    public function create() { }

    public function store(EjercicioRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $Ejercicio = Ejercicio::create([
                'nombre' => $request->nombre
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." Guardo el ejercicio ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.created_successfully', ['nombre' => $Ejercicio->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Ejercicio')]) . $th->getMessage());
        }
    }

    public function show(Ejercicio $Ejercicio) { }
    public function edit(Ejercicio $Ejercicio) { }
    public function update(Request $request, Ejercicio $Ejercicio)
    {
        DB::beginTransaction();
        try {
            $Ejercicio->update([
                'nombre' => $request->nombre
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['nombre' => $Ejercicio->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Ejercicio->nombre]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ejercicio  $Ejercicio
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Ejercicio $Ejercicio)
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // si se la rechazaron, tendra que hacer uno nuevo
            $Ejercicios = Ejercicio::findOrFail($id);
            if($Ejercicios->valido == 0){
                $Ejercicios->delete();
                DB::commit();
                return back()->with('success', __('app.label.deleted_successfully'));
            }else{
                DB::commit();
                return back()->with('warning', __('app.label.not_deleted', ['name' => $Ejercicios->nombre]));
            }
            
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Ejercicios')]) . $th->getMessage());
        }
    }
}
