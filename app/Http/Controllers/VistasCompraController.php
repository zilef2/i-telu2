<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Universidad;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class VistasCompraController extends Controller{
    public function VistaPrincipal(){
        $user = Myhelp::AuthU();
        $numberpermissions = Myhelp::getPermissionToNumber();
        if($numberpermissions < 1.5) return redirect('/materia');

//        if($numberpermissions < 8) return redirect('/SeleccioneAsignatura');

        $textoBotones=[
            "Explorar asignaturas",
            "Mis asignaturas",
        ];
        $ExplicacionBotones=[
            "Para empezar a aprender",
            "Listado de tus asignaturas",
        ];
        $linkBotones=[
            "SeleccioneAsignatura",
            "materia.index",
        ];

        $planUser = $user->plan()->first();
        return Inertia::render('Dashboard', [
            'textoBotones'          => $textoBotones,
            'linkBotones'           => $linkBotones,
            'ExplicacionBotones'    => $ExplicacionBotones,
            'plan'                  => $planUser, //si es cero no tiene plan
        ]);
    }
    public function SeleccioneAsignatura(){
        $user = Myhelp::AuthU();
        Myhelp::EscribirEnLog($this, ' SeleccioneAsignatura ');
        $numberpermissions = Myhelp::getPermissionToNumber();
        if($numberpermissions < 1.5) return redirect('/materia');

        $IDmateriasDelUser = $user->materias()->pluck('materias.id');
        $universidadClass = new Universidad();
        $materiasInteluGeneric = $universidadClass->materiasInteluGenerica()->pluck('id');
        $materias = Materia::WhereIn('id',$materiasInteluGeneric)
                    ->WhereNotIn('id',$IDmateriasDelUser);

        $materias = $materias->get()->map(function ($materia) {
            $materia->laCarrera = $materia->carrera()->first()->nombre;
            return $materia;
        });

        $perPage =  10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $materias->forPage($page, $perPage),
            $materias->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('User/Autoasignacion/SeleccioneAsignatura', [
            'title' => 1,
            'perPage' => 1,
            'numberPermissions' => 1,
            'materias' => $paginated,
            'carreras' => Carrera::all(),
        ]);
    }

    public function ComprarAsignatura(Request $request){
        $user = Myhelp::AuthU();
        Myhelp::EscribirEnLog($this, ' ComprarAsignatura ');
        $numberpermissions = Myhelp::getPermissionToNumber();
        if($numberpermissions < 1.5){
            Myhelp::EscribirEnLog($this,'Un estudiante normal intento comprar una asignatura',false,1);
            return redirect('/materia');
        }

        $lasCarreras = Carrera::WhereIn('id',$request->materias)->get();
        $soloIdCarreras = $lasCarreras->pluck('id');
        $soloIdUniversidades = $lasCarreras->pluck('universidad_id');
        foreach ($soloIdUniversidades as $uniID){
            if(!$user->ExistUniversidad($uniID)){
                $user->universidades()->attach($uniID);
            }
        }
        foreach ($soloIdCarreras as $carID){
            if(!$user->ExistCarrera($carID)){
                $user->carreras()->attach($carID);
            }
        }
        foreach ($request->materias as $matID){
            if(!$user->ExistMateria($matID)){
                $user->materias()->attach($matID);
            }
        }
//        $user->carreras()->attach($soloIdMaterias);
//        $user->materias()->attach($request->materias);

        return redirect()->route('materia.index')
            ->with('success',"Usted ha matriculado". count($request->materias)." materias" );
    }
}
