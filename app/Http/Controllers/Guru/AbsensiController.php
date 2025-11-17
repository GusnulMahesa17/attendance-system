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

        $validated['jadwal_id'] = $jadwal->id;
        
        $pertemuan = Pertemuan::create($validated);

        // Auto create absensi untuk semua siswa di kelas
        $siswas = $jadwal->kelas->siswas;
        foreach ($siswas as $siswa) {
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

    public function laporanHarian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        
        $pertemuans = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis'])
            ->whereHas('jadwal', function($q) {
                $q->where('guru_id', auth()->id());
            })
            ->whereDate('tanggal', $tanggal)
            ->get();

        return view('guru.laporan.harian', compact('pertemuans', 'tanggal'));
    }

    public function laporanBulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        $pertemuans = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', function($q) {
                $q->where('guru_id', auth()->id());
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $rekap = $this->hitungRekap($pertemuans);

        return view('guru.laporan.bulanan', compact('pertemuans', 'rekap', 'bulan', 'tahun'));
    }

    public function laporanSemester(Request $request)
    {
        $semester = $request->input('semester', 'ganjil');
        $tahunAjaran = $request->input('tahun_ajaran', date('Y'));
        
        $pertemuans = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', function($q) {
                $q->where('guru_id', auth()->id());
            })
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        $rekap = $this->hitungRekap($pertemuans);

        return view('guru.laporan.semester', compact('pertemuans', 'rekap', 'semester', 'tahunAjaran'));
    }

    private function hitungRekap($pertemuans)
    {
        $rekap = [];
        
        foreach ($pertemuans as $pertemuan) {
            foreach ($pertemuan->absensis as $absensi) {
                $siswaId = $absensi->siswa_id;
                
                if (!isset($rekap[$siswaId])) {
                    $rekap[$siswaId] = [
                        'siswa' => $absensi->siswa,
                        'hadir' => 0,
                        'izin' => 0,
                        'sakit' => 0,
                        'alpa' => 0,
                        'total' => 0
                    ];
                }
                
                $rekap[$siswaId][$absensi->status]++;
                $rekap[$siswaId]['total']++;
            }
        }

        return $rekap;
    }
}