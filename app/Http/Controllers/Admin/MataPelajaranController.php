<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mapels = MataPelajaran::with('guru')->paginate(10);
        return view('admin.mapel.index', compact('mapels'));
    }

    public function create()
    {
        $gurus = User::where('role', 'guru')->get();
        return view('admin.mapel.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|unique:mata_pelajarans,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'guru_id' => 'nullable|exists:users,id',
            'durasi_menit' => 'required|integer|min:30',
            'hari' => 'nullable|string',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i'
        ]);

        MataPelajaran::create($validated);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan');
    }

    public function edit(MataPelajaran $mapel)
    {
        $gurus = User::where('role', 'guru')->get();
        return view('admin.mapel.edit', compact('mapel', 'gurus'));
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|unique:mata_pelajarans,kode_mapel,' . $mapel->id,
            'nama_mapel' => 'required|string|max:255',
            'guru_id' => 'nullable|exists:users,id',
            'durasi_menit' => 'required|integer|min:30',
            'hari' => 'nullable|string',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i'
        ]);

        $mapel->update($validated);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui');
    }

    public function destroy(MataPelajaran $mapel)
    {
        $mapel->delete();
        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil dihapus');
    }
}
