<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\PosicionUser;
use App\Http\Requests\PosicionUserRequest;

class PosicionUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $posicionusers= PosicionUser::all();
        return view('posicionusers.index', ['posicionusers'=>$posicionusers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('posicionusers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PosicionUserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PosicionUserRequest $request)
    {
        $posicionuser = new PosicionUser;
		$posicionuser->nombre = $request->input('nombre');
		$posicionuser->importancia = $request->input('importancia');
        $posicionuser->save();

        return to_route('posicionusers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $posicionuser = PosicionUser::findOrFail($id);
        return view('posicionusers.show',['posicionuser'=>$posicionuser]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $posicionuser = PosicionUser::findOrFail($id);
        return view('posicionusers.edit',['posicionuser'=>$posicionuser]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PosicionUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PosicionUserRequest $request, $id)
    {
        $posicionuser = PosicionUser::findOrFail($id);
		$posicionuser->nombre = $request->input('nombre');
		$posicionuser->importancia = $request->input('importancia');
        $posicionuser->save();

        return to_route('posicionusers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $posicionuser = PosicionUser::findOrFail($id);
        $posicionuser->delete();

        return to_route('posicionusers.index');
    }
}
