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

    public function laporanBulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $kelas_id = $request->input('kelas_id');

        $query = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', function ($q) {
                $q->where('guru_id', auth()->id());
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }

        $pertemuans = $query->get();
        $rekap = $this->hitungRekap($pertemuans);

        // Ambil daftar kelas untuk filter
        $kelas_list = \App\Models\Kelas::whereHas('jadwals', function ($q) {
            $q->where('guru_id', auth()->id());
        })->get();

        return view('guru.laporan.bulanan', compact('pertemuans', 'rekap', 'bulan', 'tahun', 'kelas_list', 'kelas_id'));
    }

    public function exportPdfBulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $kelas_id = $request->input('kelas_id');

        $query = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', function ($q) {
                $q->where('guru_id', auth()->id());
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }

        $pertemuans = $query->get();
        $rekap = $this->hitungRekap($pertemuans);
        $kelas = $kelas_id ? \App\Models\Kelas::find($kelas_id) : null;

        $pdf = Pdf::loadView('guru.laporan.pdf.bulanan', [
            'pertemuans' => $pertemuans,
            'rekap' => $rekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'kelas' => $kelas,
            'guru' => auth()->user()
        ])->setPaper('a4', 'landscape');

        $bulan_nama = \Carbon\Carbon::create()->month($bulan)->format('F');
        $filename = 'Laporan_Bulanan_' . $bulan_nama . '_' . $tahun;
        if ($kelas) {
            $filename .= '_' . str_replace(' ', '_', $kelas->nama_kelas);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }

    public function laporanSemester(Request $request)
    {
        $semester = $request->input('semester', 'ganjil');
        $tahunAjaran = $request->input('tahun_ajaran', date('Y'));
        $kelas_id = $request->input('kelas_id');

        $query = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', function ($q) {
                $q->where('guru_id', auth()->id());
            })
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran);

        if ($kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }

        $pertemuans = $query->get();
        $rekap = $this->hitungRekap($pertemuans);

        // Ambil daftar kelas untuk filter
        $kelas_list = \App\Models\Kelas::whereHas('jadwals', function ($q) {
            $q->where('guru_id', auth()->id());
        })->get();

        return view('guru.laporan.semester', compact('pertemuans', 'rekap', 'semester', 'tahunAjaran', 'kelas_list', 'kelas_id'));
    }

    public function exportPdfSemester(Request $request)
    {
        $semester = $request->input('semester', 'ganjil');
        $tahunAjaran = $request->input('tahun_ajaran', date('Y'));
        $kelas_id = $request->input('kelas_id');

        $query = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', function ($q) {
                $q->where('guru_id', auth()->id());
            })
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran);

        if ($kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }

        $pertemuans = $query->get();
        $rekap = $this->hitungRekap($pertemuans);
        $kelas = $kelas_id ? \App\Models\Kelas::find($kelas_id) : null;

        $pdf = Pdf::loadView('guru.laporan.pdf.semester', [
            'pertemuans' => $pertemuans,
            'rekap' => $rekap,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'kelas' => $kelas,
            'guru' => auth()->user()
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Semester_' . ucfirst($semester) . '_' . $tahunAjaran;
        if ($kelas) {
            $filename .= '_' . str_replace(' ', '_', $kelas->nama_kelas);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
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