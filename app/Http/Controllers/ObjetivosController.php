<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Objetivo;
use App\Http\Requests\ObjetivoRequest;

class ObjetivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $objetivos = Objetivo::all();
        return view('objetivos.index', ['objetivos' => $objetivos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('objetivos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ObjetivoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ObjetivoRequest $request)
    {
        $objetivo = new Objetivo;
        $objetivo->nombre = $request->input('nombre');
        $objetivo->descripcion = $request->input('descripcion');
        $objetivo->save();

        return to_route('objetivos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $objetivo = Objetivo::findOrFail($id);
        return view('objetivos.show', ['objetivo' => $objetivo]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $objetivo = Objetivo::findOrFail($id);
        return view('objetivos.edit', ['objetivo' => $objetivo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ObjetivoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ObjetivoRequest $request, $id)
    {
        $objetivo = Objetivo::findOrFail($id);
        $objetivo->nombre = $request->input('nombre');
        $objetivo->descripcion = $request->input('descripcion');
        $objetivo->save();

        return to_route('objetivos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $objetivo = Objetivo::findOrFail($id);
        $objetivo->delete();

        return to_route('objetivos.index');
    }
}
