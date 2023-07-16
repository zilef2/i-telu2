<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\RespuestaEjercicio;
use App\Http\Requests\RespuestaEjercicioRequest;

class RespuestaEjerciciosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $respuestaejercicios = RespuestaEjercicio::all();
        return view('respuestaejercicios.index', ['respuestaejercicios' => $respuestaejercicios]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('respuestaejercicios.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RespuestaEjercicioRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RespuestaEjercicioRequest $request)
    {
        $respuestaejercicio = new RespuestaEjercicio;
        $respuestaejercicio->core = $request->input('core');
        $respuestaejercicio->precisa = $request->input('precisa');
        $respuestaejercicio->save();

        return to_route('respuestaejercicios.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $respuestaejercicio = RespuestaEjercicio::findOrFail($id);
        return view('respuestaejercicios.show', ['respuestaejercicio' => $respuestaejercicio]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $respuestaejercicio = RespuestaEjercicio::findOrFail($id);
        return view('respuestaejercicios.edit', ['respuestaejercicio' => $respuestaejercicio]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RespuestaEjercicioRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RespuestaEjercicioRequest $request, $id)
    {
        $respuestaejercicio = RespuestaEjercicio::findOrFail($id);
        $respuestaejercicio->core = $request->input('core');
        $respuestaejercicio->precisa = $request->input('precisa');
        $respuestaejercicio->save();

        return to_route('respuestaejercicios.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $respuestaejercicio = RespuestaEjercicio::findOrFail($id);
        $respuestaejercicio->delete();

        return to_route('respuestaejercicios.index');
    }
}
