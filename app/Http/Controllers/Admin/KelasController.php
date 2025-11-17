<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

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

    public function show(Kelas $kela)
    {
        $kela->load('siswas');
        return view('admin.kelas.show', compact('kela'));
    }

    public function edit(Kelas $kela)
    {
        return view('admin.kelas.edit', compact('kela'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string',
            'jurusan' => 'nullable|string',
            'tahun_ajaran' => 'required|digits:4'
        ]);

        $kela->update($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus');
    }

    // Manage siswa dalam kelas
    public function addSiswa(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'kelas_id' => 'exists:kelas,id',
            'nis' => 'required|unique:siswas,nis',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'phone' => 'nullable|string'
        ]);

        $validated['kelas_id'] = $kela->id;
        Siswa::create($validated);

        return redirect()->route('admin.kelas.show', $kela)
            ->with('success', 'Siswa berhasil ditambahkan');
    }

    public function updateSiswa(Request $request, Kelas $kela, Siswa $siswa)
    {
        $validated = $request->validate([
            'nis' => 'required|unique:siswas,nis,' . $siswa->id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'phone' => 'nullable|string'
        ]);

        $siswa->update($validated);

        return redirect()->route('admin.kelas.show', $kela)
            ->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroySiswa(Kelas $kela, Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.kelas.show', $kela)
            ->with('success', 'Siswa berhasil dihapus');
    }
}
