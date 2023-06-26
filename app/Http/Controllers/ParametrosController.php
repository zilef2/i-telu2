<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Parametro;
use App\Http\Requests\ParametroRequest;

class ParametrosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $parametros= Parametro::all();
        return view('parametros.index', ['parametros'=>$parametros]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('parametros.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ParametroRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ParametroRequest $request)
    {
        $parametro = new Parametro;
		$parametro->prompEjercicios = $request->input('prompEjercicios');
		$parametro->NumeroTicketDefecto = $request->input('NumeroTicketDefecto');
        $parametro->save();

        return to_route('parametros.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $parametro = Parametro::findOrFail($id);
        return view('parametros.show',['parametro'=>$parametro]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $parametro = Parametro::findOrFail($id);
        return view('parametros.edit',['parametro'=>$parametro]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ParametroRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ParametroRequest $request, $id)
    {
        $parametro = Parametro::findOrFail($id);
		$parametro->prompEjercicios = $request->input('prompEjercicios');
		$parametro->NumeroTicketDefecto = $request->input('NumeroTicketDefecto');
        $parametro->save();

        return to_route('parametros.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $parametro = Parametro::findOrFail($id);
        $parametro->delete();

        return to_route('parametros.index');
    }
}
