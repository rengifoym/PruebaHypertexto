<?php

namespace App\Http\Controllers;
use App\Models\Materia;

use Illuminate\Http\Request;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Materia::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $materia = Materia::create($request->all());
        return response()->json($materia, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Materia::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $materia = Materia::findOrFail($id);
        $materia->update($request->all());
        return response()->json($materia, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Materia::destroy($id);
        return response()->json(null, 204);
    }
}
