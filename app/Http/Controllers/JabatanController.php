<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatan = Jabatan::paginate(10);
        return view('admin.jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        return view('admin.jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:jabatan,nama',
        ]);

        Jabatan::create($request->only('nama'));

        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit($jabatan_id)
    {
        $jabatan = Jabatan::findOrFail($jabatan_id);
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $jabatan_id)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:jabatan,nama,' . $jabatan_id . ',jabatan_id',
        ]);

        $jabatan = Jabatan::findOrFail($jabatan_id);
        $jabatan->update($request->only('nama'));

        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy($jabatan_id)
    {
        $jabatan = Jabatan::findOrFail($jabatan_id);
        $jabatan->delete();

        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
