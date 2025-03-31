<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departemen = Departemen::paginate(10);
        return view('admin.departemen.index', compact('departemen'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.departemen.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'nama' => 'required|string|max:50|unique:departemen,nama',
        ]);

        // Create a new departemen
        Departemen::create($request->only('nama'));

        // Redirect to the index page with a success message
        return redirect()->route('admin.departemen.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $departemen_id
     * @return \Illuminate\Http\Response
     */
    public function edit($departemen_id)
    {
        // Find the departemen by id
        $departemen = Departemen::findOrFail($departemen_id);
        return view('admin.departemen.edit', compact('departemen'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $departemen_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $departemen_id)
    {
        // Validate the request data
        $request->validate([
            'nama' => 'required|string|max:50|unique:departemen,nama,' . $departemen_id . ',departemen_id',
        ]);

        // Find the departemen by id
        $departemen = Departemen::findOrFail($departemen_id);

        // Update the departemen
        $departemen->update($request->only('nama'));

        // Redirect to the index page with a success message
        return redirect()->route('admin.departemen.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $departemen_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($departemen_id)
    {
        // Find the departemen by id
        $departemen = Departemen::findOrFail($departemen_id);

        // Delete the departemen
        $departemen->delete();

        // Redirect to the index page with a success message
        return redirect()->route('admin.departemen.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
