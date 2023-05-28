<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Http\Requests\StoreReporteRequest;
use App\Http\Requests\UpdateReporteRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $titulo = __('app.label.Reportes');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $Reportes = Reporte::query();

        
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
                $Reportes->whereMonth('fecha_ini', $request->search);
                $Reportes->OrwhereMonth('fecha_fin', $request->search);
                $Reportes->OrwhereYear('fecha_ini', $request->search);
                $Reportes->OrwhereYear('fecha_fin', $request->search);
                $Reportes->OrwhereDay('fecha_ini', $request->search);
                $Reportes->OrwhereDay('fecha_fin', $request->search);
                // $Reportes->orWhere('fecha_fin', 'LIKE', "%" . $request->search . "%");
            }
            if ($request->has(['field', 'order'])) {
                $Reportes->orderBy($request->field, $request->order);
            }else{
                $Reportes->orderBy('fecha_ini');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","Centro costo","Trabajador", "valido",   "inicio",       "fin",        "horas trabajadas",   "observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"], //m for money || t for datetime || d date || i for integer || s string || b boolean 
                [null,null,null,null,"b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean 
            ];
        }


        return Inertia::render('Reportes/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Reportes->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,

        ]);
    }//fin index

    public function create() { }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $Reporte = Reporte::create([
                'nombre' => $request->nombre
            ]);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['nombre' => $Reporte->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.Reporte')]) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reporte  $Reporte
     * @return \Illuminate\Http\Response
     */
    public function show(Reporte $Reporte)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reporte  $Reporte
     * @return \Illuminate\Http\Response
     */
    public function edit(Reporte $Reporte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateReporteRequest  $request
     * @param  \App\Models\Reporte  $Reporte
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reporte $Reporte)
    {
        DB::beginTransaction();
        try {
            $Reporte->update([
                'nombre' => $request->nombre
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['nombre' => $Reporte->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['nombre' => $Reporte->nombre]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reporte  $Reporte
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Reporte $Reporte)
    public function destroy($id)
    {
        //
    }
}
