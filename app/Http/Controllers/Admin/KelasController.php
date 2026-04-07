<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use App\Http\Requests\SiswaRequest;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('siswas')->paginate(10);
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string',
            'jurusan' => 'nullable|string',
            'tahun_ajaran' => 'required|digits:4'
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load('siswas');
        return view('admin.kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string',
            'jurusan' => 'nullable|string',
            'tahun_ajaran' => 'required|digits:4'
        ]);

        $kelas->update($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus');
    }

    // Manage siswa dalam kelas
    public function addSiswa(SiswaRequest $request, Kelas $kelas)
    {
        $validated = $request->validated();

        $validated['kelas_id'] = $kelas->id;
        Siswa::create($validated);

        return redirect()->route('admin.kelas.show', $kelas)
            ->with('success', 'Siswa berhasil ditambahkan');
    }

    public function updateSiswa(SiswaRequest $request, Kelas $kelas, Siswa $siswa)
    {
        $validated = $request->validated();
        $siswa->update($validated);

        return redirect()->route('admin.kelas.show', $kelas)
            ->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroySiswa(Kelas $kelas, Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.kelas.show', $kelas)
            ->with('success', 'Siswa berhasil dihapus');
    }
}
