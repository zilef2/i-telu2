<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Subtopico;
use App\Http\Requests\SubtopicoRequest;

class SubtopicosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $subtopicos= Subtopico::all();
        return view('subtopicos.index', ['subtopicos'=>$subtopicos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('subtopicos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  SubtopicoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SubtopicoRequest $request)
    {
        $subtopico = new Subtopico;
		$subtopico->nombre = $request->input('nombre');
		$subtopico->descripcion = $request->input('descripcion');
        $subtopico->save();

        return to_route('subtopicos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $subtopico = Subtopico::findOrFail($id);
        return view('subtopicos.show',['subtopico'=>$subtopico]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $subtopico = Subtopico::findOrFail($id);
        return view('subtopicos.edit',['subtopico'=>$subtopico]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SubtopicoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SubtopicoRequest $request, $id)
    {
        $subtopico = Subtopico::findOrFail($id);
		$subtopico->nombre = $request->input('nombre');
		$subtopico->descripcion = $request->input('descripcion');
        $subtopico->save();

        return to_route('subtopicos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $subtopico = Subtopico::findOrFail($id);
        $subtopico->delete();

        return to_route('subtopicos.index');
    }
}
