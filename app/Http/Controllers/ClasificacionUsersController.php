<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\ClasificacionUser;
use App\Http\Requests\ClasificacionUserRequest;

class ClasificacionUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $clasificacionusers= ClasificacionUser::all();
        return view('clasificacionusers.index', ['clasificacionusers'=>$clasificacionusers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('clasificacionusers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ClasificacionUserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ClasificacionUserRequest $request)
    {
        $clasificacionuser = new ClasificacionUser;
		$clasificacionuser->nombre = $request->input('nombre');
		$clasificacionuser->descripcion = $request->input('descripcion');
        $clasificacionuser->save();

        return to_route('clasificacionusers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $clasificacionuser = ClasificacionUser::findOrFail($id);
        return view('clasificacionusers.show',['clasificacionuser'=>$clasificacionuser]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $clasificacionuser = ClasificacionUser::findOrFail($id);
        return view('clasificacionusers.edit',['clasificacionuser'=>$clasificacionuser]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ClasificacionUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ClasificacionUserRequest $request, $id)
    {
        $clasificacionuser = ClasificacionUser::findOrFail($id);
		$clasificacionuser->nombre = $request->input('nombre');
		$clasificacionuser->descripcion = $request->input('descripcion');
        $clasificacionuser->save();

        return to_route('clasificacionusers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $clasificacionuser = ClasificacionUser::findOrFail($id);
        $clasificacionuser->delete();

        return to_route('clasificacionusers.index');
    }
}
