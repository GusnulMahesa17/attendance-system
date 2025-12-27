<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pertemuan;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', auth()->id())
            ->get();

        return view('guru.absensi.index', compact('jadwals'));
    }

    public function createPertemuan(Jadwal $jadwal)
    {
        $lastPertemuan = $jadwal->pertemuans()->latest('pertemuan_ke')->first();
        $pertemuanKe = $lastPertemuan ? $lastPertemuan->pertemuan_ke + 1 : 1;

        return view('guru.absensi.create-pertemuan', compact('jadwal', 'pertemuanKe'));
    }

    public function storePertemuan(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'pertemuan_ke' => 'required|integer|min:1',
            'deskripsi' => 'required|string',
            'semester' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|digits:4'
        ]);

        // Cek duplikasi tanggal
        if (Pertemuan::where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', $validated['tanggal'])
            ->exists()) {
            return back()->withInput()
                ->withErrors(['tanggal' => 'Sudah ada pertemuan pada tanggal ini!']);
        }

        // Cek duplikasi nomor pertemuan
        if (Pertemuan::where('jadwal_id', $jadwal->id)
            ->where('semester', $validated['semester'])
            ->where('tahun_ajaran', $validated['tahun_ajaran'])
            ->where('pertemuan_ke', $validated['pertemuan_ke'])
            ->exists()) {
            return back()->withInput()
                ->withErrors(['pertemuan_ke' => 'Nomor pertemuan ini sudah ada!']);
        }

        $validated['jadwal_id'] = $jadwal->id;
        $pertemuan = Pertemuan::create($validated);

        // Buat absensi otomatis
        foreach ($jadwal->kelas->siswas as $siswa) {
            Absensi::create([
                'pertemuan_id' => $pertemuan->id,
                'siswa_id' => $siswa->id,
                'status' => 'hadir'
            ]);
        }

        return redirect()->route('guru.absensi.input', $pertemuan)
            ->with('success', 'Pertemuan berhasil dibuat');
    }

    public function inputAbsensi(Pertemuan $pertemuan)
    {
        $pertemuan->load(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa']);
        return view('guru.absensi.input', compact('pertemuan'));
    }

    public function updateAbsensi(Request $request, Pertemuan $pertemuan)
    {
        $validated = $request->validate([
            'absensi' => 'required|array',
            'absensi.*.id' => 'required|exists:absensis,id',
            'absensi.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'absensi.*.keterangan' => 'nullable|string',
            'deskripsi' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $pertemuan) {
            foreach ($validated['absensi'] as $data) {
                Absensi::where('id', $data['id'])->update([
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null
                ]);
            }

            if (isset($validated['deskripsi'])) {
                $pertemuan->update(['deskripsi' => $validated['deskripsi']]);
            }
        });

        return redirect()->route('guru.absensi.input', $pertemuan)
            ->with('success', 'Absensi berhasil disimpan');
    }
}