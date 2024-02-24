<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Grupo;
use App\Http\Requests\GrupoRequest;

class GruposController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $grupos= Grupo::all();
        return view('grupos.index', ['grupos'=>$grupos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('grupos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  GrupoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GrupoRequest $request)
    {
        $grupo = new Grupo;
		$grupo->nombre = $request->input('nombre');
		$grupo->codigo = $request->input('codigo');
        $grupo->save();

        return to_route('grupos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $grupo = Grupo::findOrFail($id);
        return view('grupos.show',['grupo'=>$grupo]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $grupo = Grupo::findOrFail($id);
        return view('grupos.edit',['grupo'=>$grupo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  GrupoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GrupoRequest $request, $id)
    {
        $grupo = Grupo::findOrFail($id);
		$grupo->nombre = $request->input('nombre');
		$grupo->codigo = $request->input('codigo');
        $grupo->save();

        return to_route('grupos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $grupo = Grupo::findOrFail($id);
        $grupo->delete();

        return to_route('grupos.index');
    }
}
