<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Cuota;
use App\Http\Requests\CuotaRequest;

class CuotasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $cuotas= Cuota::all();
        return view('cuotas.index', ['cuotas'=>$cuotas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('cuotas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CuotaRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CuotaRequest $request)
    {
        $cuota = new Cuota;
		$cuota->numeroDeLaCuota = $request->input('numeroDeLaCuota');
		$cuota->numeroDecuotas = $request->input('numeroDecuotas');
		$cuota->valor = $request->input('valor');
        $cuota->save();

        return to_route('cuotas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $cuota = Cuota::findOrFail($id);
        return view('cuotas.show',['cuota'=>$cuota]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $cuota = Cuota::findOrFail($id);
        return view('cuotas.edit',['cuota'=>$cuota]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CuotaRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CuotaRequest $request, $id)
    {
        $cuota = Cuota::findOrFail($id);
		$cuota->numeroDeLaCuota = $request->input('numeroDeLaCuota');
		$cuota->numeroDecuotas = $request->input('numeroDecuotas');
		$cuota->valor = $request->input('valor');
        $cuota->save();

        return to_route('cuotas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $cuota = Cuota::findOrFail($id);
        $cuota->delete();

        return to_route('cuotas.index');
    }
}
