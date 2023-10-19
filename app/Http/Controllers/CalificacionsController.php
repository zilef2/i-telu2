<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Calificacion;
use App\Http\Requests\CalificacionRequest;

class CalificacionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $calificacions= Calificacion::all();
        return view('calificacions.index', ['calificacions'=>$calificacions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('calificacions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CalificacionRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CalificacionRequest $request)
    {
        $calificacion = new Calificacion;
		$calificacion->TipoPrueba = $request->input('TipoPrueba');
		$calificacion->prompUsado = $request->input('prompUsado');
		$calificacion->valor = $request->input('valor');
		$calificacion->tokens = $request->input('tokens');
        $calificacion->save();

        return to_route('calificacions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $calificacion = Calificacion::findOrFail($id);
        return view('calificacions.show',['calificacion'=>$calificacion]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $calificacion = Calificacion::findOrFail($id);
        return view('calificacions.edit',['calificacion'=>$calificacion]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CalificacionRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CalificacionRequest $request, $id)
    {
        $calificacion = Calificacion::findOrFail($id);
		$calificacion->TipoPrueba = $request->input('TipoPrueba');
		$calificacion->prompUsado = $request->input('prompUsado');
		$calificacion->valor = $request->input('valor');
		$calificacion->tokens = $request->input('tokens');
        $calificacion->save();

        return to_route('calificacions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $calificacion = Calificacion::findOrFail($id);
        $calificacion->delete();

        return to_route('calificacions.index');
    }
}
