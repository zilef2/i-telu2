<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Archivo;
use App\Http\Requests\ArchivoRequest;

class ArchivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $archivos= Archivo::all();
        return view('archivos.index', ['archivos'=>$archivos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('archivos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ArchivoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ArchivoRequest $request)
    {
        $archivo = new Archivo;
		$archivo->nombre = $request->input('nombre');
		$archivo->peso = $request->input('peso');
		$archivo->nick = $request->input('nick');
        $archivo->save();

        return to_route('archivos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $archivo = Archivo::findOrFail($id);
        return view('archivos.show',['archivo'=>$archivo]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $archivo = Archivo::findOrFail($id);
        return view('archivos.edit',['archivo'=>$archivo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ArchivoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ArchivoRequest $request, $id)
    {
        $archivo = Archivo::findOrFail($id);
		$archivo->nombre = $request->input('nombre');
		$archivo->peso = $request->input('peso');
		$archivo->nick = $request->input('nick');
        $archivo->save();

        return to_route('archivos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $archivo = Archivo::findOrFail($id);
        $archivo->delete();

        return to_route('archivos.index');
    }
}
