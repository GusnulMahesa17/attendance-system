<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pertemuan;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
        if (Pertemuan::where('jadwal_id', $jadwal->id)->whereDate('tanggal', $validated['tanggal'])->exists()) {
            return back()->withInput()->withErrors(['tanggal' => 'Sudah ada pertemuan pada tanggal ini!']);
        }

        // Cek duplikasi nomor pertemuan
        if (
            Pertemuan::where('jadwal_id', $jadwal->id)
                ->where('semester', $validated['semester'])
                ->where('tahun_ajaran', $validated['tahun_ajaran'])
                ->where('pertemuan_ke', $validated['pertemuan_ke'])->exists()
        ) {
            return back()->withInput()->withErrors(['pertemuan_ke' => 'Nomor pertemuan ini sudah ada di semester tersebut!']);
        }

        $validated['jadwal_id'] = $jadwal->id;
        $pertemuan = Pertemuan::create($validated);

        // Buat absensi
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

    public function laporanHarian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelas_id = $request->input('kelas_id');

        $query = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', function ($q) {
                $q->where('guru_id', auth()->id());
            })
            ->whereDate('tanggal', $tanggal);

        if ($kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }

        $pertemuans = $query->get();

        // Ambil daftar kelas untuk filter
        $kelas_list = \App\Models\Kelas::whereHas('jadwals', function ($q) {
            $q->where('guru_id', auth()->id());
        })->get();

        return view('guru.laporan.harian', compact('pertemuans', 'tanggal', 'kelas_list', 'kelas_id'));
    }

    public function laporanBulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $jadwal_id = $request->input('jadwal_id');

        // Ambil semua jadwal guru yang login
        $jadwal_list = Jadwal::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', auth()->id())
            ->get();

        if ($jadwal_id) {
            // Jika ada jadwal dipilih, ambil data spesifik
            $jadwal = Jadwal::with(['kelas', 'mataPelajaran'])->find($jadwal_id);

            $pertemuans = Pertemuan::with(['absensis.siswa'])
                ->where('jadwal_id', $jadwal_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            $rekap = $this->hitungRekapPerJadwal($pertemuans, $jadwal);

            return view('guru.laporan.bulanan', compact('rekap', 'bulan', 'tahun', 'jadwal_list', 'jadwal_id', 'jadwal', 'pertemuans'));
        } else {
            // Jika tidak ada dipilih, tampilkan semua
            $allRekap = [];

            foreach ($jadwal_list as $jadwal) {
                $pertemuans = Pertemuan::with(['absensis.siswa'])
                    ->where('jadwal_id', $jadwal->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->get();

                if ($pertemuans->count() > 0) {
                    $allRekap[] = [
                        'jadwal' => $jadwal,
                        'rekap' => $this->hitungRekapPerJadwal($pertemuans, $jadwal),
                        'pertemuans' => $pertemuans
                    ];
                }
            }

            return view('guru.laporan.bulanan', compact('allRekap', 'bulan', 'tahun', 'jadwal_list', 'jadwal_id'));
        }
    }

    public function laporanSemester(Request $request)
    {
        $semester = $request->input('semester', 'ganjil');
        $tahunAjaran = $request->input('tahun_ajaran', date('Y'));
        $jadwal_id = $request->input('jadwal_id');

        // Ambil semua jadwal guru yang login
        $jadwal_list = Jadwal::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', auth()->id())
            ->get();

        if ($jadwal_id) {
            // Jika ada jadwal dipilih, ambil data spesifik
            $jadwal = Jadwal::with(['kelas', 'mataPelajaran'])->find($jadwal_id);

            $pertemuans = Pertemuan::with(['absensis.siswa'])
                ->where('jadwal_id', $jadwal_id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->get();

            $rekap = $this->hitungRekapPerJadwal($pertemuans, $jadwal);

            return view('guru.laporan.semester', compact('rekap', 'semester', 'tahunAjaran', 'jadwal_list', 'jadwal_id', 'jadwal', 'pertemuans'));
        } else {
            // Jika tidak ada dipilih, tampilkan semua
            $allRekap = [];

            foreach ($jadwal_list as $jadwal) {
                $pertemuans = Pertemuan::with(['absensis.siswa'])
                    ->where('jadwal_id', $jadwal->id)
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->get();

                if ($pertemuans->count() > 0) {
                    $allRekap[] = [
                        'jadwal' => $jadwal,
                        'rekap' => $this->hitungRekapPerJadwal($pertemuans, $jadwal),
                        'pertemuans' => $pertemuans
                    ];
                }
            }

            return view('guru.laporan.semester', compact('allRekap', 'semester', 'tahunAjaran', 'jadwal_list', 'jadwal_id'));
        }
    }

    private function hitungRekapPerJadwal($pertemuans, $jadwal)
    {
        $rekap = [];

        // Ambil semua siswa di kelas ini
        $siswas = $jadwal->kelas->siswas;

        foreach ($siswas as $siswa) {
            $rekap[$siswa->id] = [
                'siswa' => $siswa,
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpa' => 0,
                'total' => 0
            ];

            // Hitung dari pertemuan yang ada
            foreach ($pertemuans as $pertemuan) {
                $absensi = $pertemuan->absensis->where('siswa_id', $siswa->id)->first();

                if ($absensi) {
                    $rekap[$siswa->id][$absensi->status]++;
                    $rekap[$siswa->id]['total']++;
                }
            }
        }

        return $rekap;
    }

    // PDF Export

    public function exportPdfHarian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelas_id = $request->input('kelas_id');

        $query = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', function ($q) {
                $q->where('guru_id', auth()->id());
            })
            ->whereDate('tanggal', $tanggal);

        if ($kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }

        $pertemuans = $query->get();
        $kelas = $kelas_id ? \App\Models\Kelas::find($kelas_id) : null;

        $pdf = Pdf::loadView('guru.laporan.pdf.harian', [
            'pertemuans' => $pertemuans,
            'tanggal' => $tanggal,
            'kelas' => $kelas,
            'guru' => auth()->user()
        ]);

        $filename = 'Laporan_Harian_' . date('Y-m-d', strtotime($tanggal));
        if ($kelas) {
            $filename .= '_' . str_replace(' ', '_', $kelas->nama_kelas);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }

    public function exportPdfBulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $jadwal_id = $request->input('jadwal_id');

        if (!$jadwal_id) {
            return back()->with('error', 'Silakan pilih jadwal terlebih dahulu untuk export PDF');
        }

        $jadwal = Jadwal::with(['kelas', 'mataPelajaran'])->find($jadwal_id);

        $pertemuans = Pertemuan::with(['absensis.siswa'])
            ->where('jadwal_id', $jadwal_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $rekap = $this->hitungRekapPerJadwal($pertemuans, $jadwal);

        $pdf = PDF::loadView('guru.laporan.pdf.bulanan', [
            'rekap' => $rekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jadwal' => $jadwal,
            'pertemuans' => $pertemuans,
            'guru' => auth()->user()
        ])->setPaper('a4', 'landscape');

        $bulan_nama = \Carbon\Carbon::create()->month((int) $bulan)->format('F');
        $filename = 'Laporan_Bulanan_' . $bulan_nama . '_' . $tahun . '_' .
            str_replace(' ', '_', $jadwal->mataPelajaran->nama_mapel) . '_' .
            str_replace(' ', '_', $jadwal->kelas->nama_kelas) . '.pdf';

        return $pdf->download($filename);
    }

    // Export PDF Semester
    public function exportPdfSemester(Request $request)
    {
        $semester = $request->input('semester', 'ganjil');
        $tahunAjaran = $request->input('tahun_ajaran', date('Y'));
        $jadwal_id = $request->input('jadwal_id');

        if (!$jadwal_id) {
            return back()->with('error', 'Silakan pilih jadwal terlebih dahulu untuk export PDF');
        }

        $jadwal = Jadwal::with(['kelas', 'mataPelajaran'])->find($jadwal_id);

        $pertemuans = Pertemuan::with(['absensis.siswa'])
            ->where('jadwal_id', $jadwal_id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        $rekap = $this->hitungRekapPerJadwal($pertemuans, $jadwal);

        $pdf = PDF::loadView('guru.laporan.pdf.semester', [
            'rekap' => $rekap,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'jadwal' => $jadwal,
            'pertemuans' => $pertemuans,
            'guru' => auth()->user()
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Semester_' . ucfirst($semester) . '_' . $tahunAjaran . '_' .
            str_replace(' ', '_', $jadwal->mataPelajaran->nama_mapel) . '_' .
            str_replace(' ', '_', $jadwal->kelas->nama_kelas) . '.pdf';

        return $pdf->download($filename);
    }

}