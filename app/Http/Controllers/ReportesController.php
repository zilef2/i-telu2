<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Reporte;
use App\Http\Requests\ReporteRequest;
use Illuminate\Support\Facades\Auth;

class ReportesController extends Controller
{
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * valido states = {0 defect | 1 validada | 2 rechazada}
     */
    public function index(Request $request)
    {
        // if(Auth::user()->)
        $Authuser = Auth::user();
        $permissions = $Authuser->getRoleNames()->first();
        $Reportes = Reporte::query();
        if($permissions === "operator") { //admin | validador
            $Reportes->whereUser_id($Authuser->id);

            if ($request->has(['field', 'order'])) {
                $Reportes->orderBy($request->field, $request->order);
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","inicio", "fin", "horas trabajadas", "valido", "observaciones"],
                ["t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas", "b_valido", "s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean 
            ];
        }else{

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
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD
                ["Acciones","#","valido","inicio", "fin", "horas trabajadas", "Trabajador","observaciones"],
                ["b_valido","t_fecha_ini", "t_fecha_fin", "i_horas_trabajadas",  "i_user_id","s_observaciones"] //m for money || t for datetime || d date || i for integer || s string || b boolean 
            ];
        }

        return Inertia::render('Reportes/Index', [ //carpeta
            'title'          =>  __('app.label.Reportes'),
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $Reportes->paginate($perPage),
            'breadcrumbs'    =>  [['label' => __('app.label.Reportes'), 'href' => route('Reportes.index')]],
            'nombresTabla'   =>  $nombresTabla,
        ]);
    }

    public function create() { }
    public function store(ReporteRequest $request)
    {
        DB::beginTransaction();
        try {
            $Reportes = new Reporte;
            $Reportes->fecha_ini = $request->fecha_ini;
            $Reportes->fecha_fin = $request->fecha_fin;
            $Reportes->horas_trabajadas = $request->horas_trabajadas;
            $Reportes->valido = 0;
            $Reportes->observaciones = $request->observaciones;
            $Reportes->centro_costo_id = 1;
            $Reportes->user_id = Auth::user()->id;

            $Reportes->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $Reportes->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) { }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Reportes = Reporte::findOrFail($id);
        return Inertia::render('Reportes.edit',['Reportes'=>$Reportes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ReportesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReporteRequest $request, $id)
    {
         DB::beginTransaction();

        try {
            $Reportes = Reporte::findOrFail($id);
            $Reportes->fecha_ini = $request->input('fecha_ini');
            $Reportes->fecha_fin = $request->input('fecha_fin');
            $Reportes->horas_trabajadas = $request->input('horas_trabajadas');
            $Reportes->valido = $request->input('valido');
            $Reportes->observaciones = $request->input('observaciones');
            $Reportes->save();
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $Reportes->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $Reportes = Reporte::findOrFail($id);
            $Reportes->delete();

            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $Reportes->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.Reportes')]) . $th->getMessage());
        }
    }
}
