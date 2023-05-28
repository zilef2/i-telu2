<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Tema;
use App\Http\Requests\TemaRequest;

class TemasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $temas= Tema::all();
        return view('temas.index', ['temas'=>$temas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('temas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TemaRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TemaRequest $request)
    {
        $tema = new Tema;
		$tema->nombre = $request->input('nombre');
		$tema->descripcion = $request->input('descripcion');
        $tema->save();

        return to_route('temas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $tema = Tema::findOrFail($id);
        return view('temas.show',['tema'=>$tema]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $tema = Tema::findOrFail($id);
        return view('temas.edit',['tema'=>$tema]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TemaRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TemaRequest $request, $id)
    {
        $tema = Tema::findOrFail($id);
		$tema->nombre = $request->input('nombre');
		$tema->descripcion = $request->input('descripcion');
        $tema->save();

        return to_route('temas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $tema = Tema::findOrFail($id);
        $tema->delete();

        return to_route('temas.index');
    }
}
