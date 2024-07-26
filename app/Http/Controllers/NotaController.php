<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Nota::with(['estudiante', 'curso', 'materia'])->get();
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
        $nota = Nota::create($request->all());
        return response()->json($nota->load(['estudiante', 'curso', 'materia']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Nota::with(['estudiante', 'curso', 'materia'])->findOrFail($id);
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
        $nota = Nota::findOrFail($id);
        $nota->update($request->all());
        return response()->json($nota->load(['estudiante', 'curso', 'materia']), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Nota::destroy($id);
        return response()->json(null, 204);
    }
}
