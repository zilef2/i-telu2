<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\TiemposArticulo;
use App\Http\Requests\TiemposArticuloRequest;

class TiemposArticulosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $tiemposarticulos= TiemposArticulo::all();
        return view('tiemposarticulos.index', ['tiemposarticulos'=>$tiemposarticulos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('tiemposarticulos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TiemposArticuloRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TiemposArticuloRequest $request)
    {
        $tiemposarticulo = new TiemposArticulo;
		$tiemposarticulo->startTime = $request->input('startTime');
		$tiemposarticulo->endTime = $request->input('endTime');
		$tiemposarticulo->tiempoEscritura = $request->input('tiempoEscritura');
        $tiemposarticulo->save();

        return to_route('tiemposarticulos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $tiemposarticulo = TiemposArticulo::findOrFail($id);
        return view('tiemposarticulos.show',['tiemposarticulo'=>$tiemposarticulo]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $tiemposarticulo = TiemposArticulo::findOrFail($id);
        return view('tiemposarticulos.edit',['tiemposarticulo'=>$tiemposarticulo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TiemposArticuloRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TiemposArticuloRequest $request, $id)
    {
        $tiemposarticulo = TiemposArticulo::findOrFail($id);
		$tiemposarticulo->startTime = $request->input('startTime');
		$tiemposarticulo->endTime = $request->input('endTime');
		$tiemposarticulo->tiempoEscritura = $request->input('tiempoEscritura');
        $tiemposarticulo->save();

        return to_route('tiemposarticulos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $tiemposarticulo = TiemposArticulo::findOrFail($id);
        $tiemposarticulo->delete();

        return to_route('tiemposarticulos.index');
    }
}
