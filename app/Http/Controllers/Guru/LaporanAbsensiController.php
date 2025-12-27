<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pertemuan;
use App\Services\RekapAbsensiService;
use Illuminate\Http\Request;

class LaporanAbsensiController extends Controller
{
    protected $rekapService;

    public function __construct(RekapAbsensiService $rekapService)
    {
        $this->rekapService = $rekapService;
    }

    public function harian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kelas_id = $request->input('kelas_id');

        $query = Pertemuan::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'absensis.siswa'])
            ->whereHas('jadwal', fn($q) => $q->where('guru_id', auth()->id()))
            ->whereDate('tanggal', $tanggal);

        if ($kelas_id) {
            $query->whereHas('jadwal', fn($q) => $q->where('kelas_id', $kelas_id));
        }

        $pertemuans = $query->get();

        $kelas_list = \App\Models\Kelas::whereHas('jadwals', function ($q) {
            $q->where('guru_id', auth()->id());
        })->get();

        return view('guru.laporan.harian', compact('pertemuans', 'tanggal', 'kelas_list', 'kelas_id'));
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $jadwal_id = $request->input('jadwal_id');

        $jadwal_list = Jadwal::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', auth()->id())
            ->get();

        if ($jadwal_id) {
            $jadwal = Jadwal::with(['kelas', 'mataPelajaran'])->find($jadwal_id);
            
            $pertemuans = Pertemuan::with(['absensis.siswa'])
                ->where('jadwal_id', $jadwal_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            $rekap = $this->rekapService->hitungRekap($pertemuans, $jadwal);

            return view('guru.laporan.bulanan', compact(
                'rekap', 'bulan', 'tahun', 'jadwal_list', 'jadwal_id', 'jadwal', 'pertemuans'
            ));
        }

        $allRekap = $this->rekapService->getAllRekap($jadwal_list, $bulan, $tahun);

        return view('guru.laporan.bulanan', compact(
            'allRekap', 'bulan', 'tahun', 'jadwal_list', 'jadwal_id'
        ));
    }

    public function semester(Request $request)
    {
        $semester = $request->input('semester', 'ganjil');
        $tahunAjaran = $request->input('tahun_ajaran', date('Y'));
        $jadwal_id = $request->input('jadwal_id');

        $jadwal_list = Jadwal::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', auth()->id())
            ->get();

        if ($jadwal_id) {
            $jadwal = Jadwal::with(['kelas', 'mataPelajaran'])->find($jadwal_id);
            
            $pertemuans = Pertemuan::with(['absensis.siswa'])
                ->where('jadwal_id', $jadwal_id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->get();

            $rekap = $this->rekapService->hitungRekap($pertemuans, $jadwal);

            return view('guru.laporan.semester', compact(
                'rekap', 'semester', 'tahunAjaran', 'jadwal_list', 'jadwal_id', 'jadwal', 'pertemuans'
            ));
        }

        $allRekap = $this->rekapService->getAllRekapSemester(
            $jadwal_list, $semester, $tahunAjaran
        );

        return view('guru.laporan.semester', compact(
            'allRekap', 'semester', 'tahunAjaran', 'jadwal_list', 'jadwal_id'
        ));
    }
}