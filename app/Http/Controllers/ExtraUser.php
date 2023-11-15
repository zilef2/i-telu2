<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Models\MedidaControl;
use App\Models\Role;
use App\Models\Subtopico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExtraUser extends Controller {
    public function Filtros($request, &$users) {
        if ($request->has('search')) {
            $users->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('email', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                    ;
            })
            ->where('name', '!=', 'Admin IntelU')
            ->where('name', '!=', 'Superadmin');
            // $users->where('name', 'LIKE', "%" . $request->search . "%");
        }
        $users->whereHas('roles', function ($query) {
            return $query->whereIn('name', ['estudiante']);
        })->with('roles');

    }


    public function MapearClasePP($request, &$users, $numberPermissions) {

        if ($request->has(['field', 'order'])) {
            $users = $users->orderBy($request->field, $request->order);
        }
        $users = $users->get()->map(function ($user) use ($numberPermissions) {
            $rol = $user->roles->pluck('name')[0];
            if($rol === 'superadmin' || $rol === 'admin') return null;

            $user->MedidaControl = (int)($user->MedidaControl->count());
            return $user;
        })->filter();
    }

    public function VerTiemposEstudiantes(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, ' extrausers');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $users = User::query();
        $this->Filtros($request, $users);
        $this->MapearClasePP($request, $users,$numberPermissions);



        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $total = $users->count();

        $paginated = new LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        $roles = Role::get();

        return Inertia::render('ExtraUser/Index', [
            'breadcrumbs'   => [
                ['label' => __('app.label.user'), 'href' => route('user.index')],
                ['label' => __('app.label.extrauser'), 'href' => route('VerTiemposEstudiantes')]
            ],
            'title'         => __('app.label.extrauser'),
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'users'         => $paginated,
            'roles'         => $roles,
            'numberPermissions'         => $numberPermissions,
        ]);
    }


    //show
    public function verEstudiante($id) {

        $permissions = Myhelp::EscribirEnLog($this, ' extrausers');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $user = User::find($id);
        $medida = MedidaControl::Where('user_id',$id)
            ->orderByDesc('subtopico_id')
            // ->having('respuesta_guardada', '>', 100)
            ->get()
            ;

            $medida = $medida->map(function($medi) {
                $medi->subtopic = $medi->subtopico_nombre();
                return $medi;
            });

            $idsubtopicos = $medida->unique('subtopico_id')->pluck('subtopico_id');
            $medida = $medida->groupBy('subtopico_id');
            $CountMedida = 0;
            foreach ($medida as $key => $value) {
                $CountMedida += count($value);

            }
            // $CountSubtopicos = count($medida);

            $subtopicos = Subtopico::Wherein('id',$idsubtopicos)->get();


// dd($medida);

        $roles = Role::get();
        //todo: que materia,

        return Inertia::render('ExtraUser/ver', [
            'breadcrumbs'   => [
                ['label' => __('app.label.user'), 'href' => route('user.index')],
                ['label' => __('app.label.extrauser'), 'href' => route('VerTiemposEstudiantes')],
                ['label' => __('app.label.verEstudiante'), 'href' => route('verEstudiante',$id)]
            ],

            'title'              => __('app.label.verEstudiante'),
            'user'               => $user,
            'roles'              => $roles,
            'medida'             => $medida,
            'numberPermissions'  => $numberPermissions,
            'countmedida'        => $CountMedida,
            'subtopicos'         => $subtopicos,
        ]);
    }
}
