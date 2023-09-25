<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\RespuestaPDf;
use App\Http\Requests\RespuestaPDfRequest;

class RespuestaPDfsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $respuestapdfs= RespuestaPDf::all();
        return view('respuestapdfs.index', ['respuestapdfs'=>$respuestapdfs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('respuestapdfs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RespuestaPDfRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RespuestaPDfRequest $request)
    {
        $respuestapdf = new RespuestaPDf;
		$respuestapdf->guardar_pdf = $request->input('guardar_pdf');
		$respuestapdf->resumen = $request->input('resumen');
		$respuestapdf->nivel = $request->input('nivel');
		$respuestapdf->precisa = $request->input('precisa');
		$respuestapdf->idExistente = $request->input('idExistente');
        $respuestapdf->save();

        return to_route('respuestapdfs.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $respuestapdf = RespuestaPDf::findOrFail($id);
        return view('respuestapdfs.show',['respuestapdf'=>$respuestapdf]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $respuestapdf = RespuestaPDf::findOrFail($id);
        return view('respuestapdfs.edit',['respuestapdf'=>$respuestapdf]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RespuestaPDfRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RespuestaPDfRequest $request, $id)
    {
        $respuestapdf = RespuestaPDf::findOrFail($id);
		$respuestapdf->guardar_pdf = $request->input('guardar_pdf');
		$respuestapdf->resumen = $request->input('resumen');
		$respuestapdf->nivel = $request->input('nivel');
		$respuestapdf->precisa = $request->input('precisa');
		$respuestapdf->idExistente = $request->input('idExistente');
        $respuestapdf->save();

        return to_route('respuestapdfs.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $respuestapdf = RespuestaPDf::findOrFail($id);
        $respuestapdf->delete();

        return to_route('respuestapdfs.index');
    }
}
