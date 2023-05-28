<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Materium;
use App\Http\Requests\MateriumRequest;

class MateriasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $materias= Materium::all();
        return view('materias.index', ['materias'=>$materias]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('materias.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  MateriumRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(MateriumRequest $request)
    {
        $materium = new Materium;
		$materium->nombre = $request->input('nombre');
		$materium->descripcion = $request->input('descripcion');
        $materium->save();

        return to_route('materias.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $materium = Materium::findOrFail($id);
        return view('materias.show',['materium'=>$materium]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $materium = Materium::findOrFail($id);
        return view('materias.edit',['materium'=>$materium]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  MateriumRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MateriumRequest $request, $id)
    {
        $materium = Materium::findOrFail($id);
		$materium->nombre = $request->input('nombre');
		$materium->descripcion = $request->input('descripcion');
        $materium->save();

        return to_route('materias.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $materium = Materium::findOrFail($id);
        $materium->delete();

        return to_route('materias.index');
    }
}
