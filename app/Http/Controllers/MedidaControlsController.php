<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\MedidaControl;
use App\Http\Requests\MedidaControlRequest;

class MedidaControlsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $medidacontrols= MedidaControl::all();
        return view('medidacontrols.index', ['medidacontrols'=>$medidacontrols]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('medidacontrols.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  MedidaControlRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(MedidaControlRequest $request)
    {
        $medidacontrol = new MedidaControl;
		$medidacontrol->tokens_usados = $request->input('tokens_usados');
		$medidacontrol->user_id = $request->input('user_id');
        $medidacontrol->save();

        return to_route('medidacontrols.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $medidacontrol = MedidaControl::findOrFail($id);
        return view('medidacontrols.show',['medidacontrol'=>$medidacontrol]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $medidacontrol = MedidaControl::findOrFail($id);
        return view('medidacontrols.edit',['medidacontrol'=>$medidacontrol]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  MedidaControlRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MedidaControlRequest $request, $id)
    {
        $medidacontrol = MedidaControl::findOrFail($id);
		$medidacontrol->tokens_usados = $request->input('tokens_usados');
		$medidacontrol->user_id = $request->input('user_id');
        $medidacontrol->save();

        return to_route('medidacontrols.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $medidacontrol = MedidaControl::findOrFail($id);
        $medidacontrol->delete();

        return to_route('medidacontrols.index');
    }
}
