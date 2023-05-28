<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Carrera;
use App\Http\Requests\CarreraRequest;

class CarrerasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $carreras= Carrera::all();
        return view('carreras.index', ['carreras'=>$carreras]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('carreras.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CarreraRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CarreraRequest $request)
    {
        $carrera = new Carrera;
		$carrera->nombre = $request->input('nombre');
		$carrera->descripcion = $request->input('descripcion');
        $carrera->save();

        return to_route('carreras.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $carrera = Carrera::findOrFail($id);
        return view('carreras.show',['carrera'=>$carrera]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $carrera = Carrera::findOrFail($id);
        return view('carreras.edit',['carrera'=>$carrera]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CarreraRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CarreraRequest $request, $id)
    {
        $carrera = Carrera::findOrFail($id);
		$carrera->nombre = $request->input('nombre');
		$carrera->descripcion = $request->input('descripcion');
        $carrera->save();

        return to_route('carreras.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $carrera = Carrera::findOrFail($id);
        $carrera->delete();

        return to_route('carreras.index');
    }
}
