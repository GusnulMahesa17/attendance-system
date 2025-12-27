<?php 

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pertemuan;
use App\Services\RekapAbsensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportAbsensiController extends Controller
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
        $kelas = $kelas_id ? \App\Models\Kelas::find($kelas_id) : null;

        $pdf = Pdf::loadView('guru.laporan.pdf.harian', [
            'pertemuans' => $pertemuans,
            'tanggal' => $tanggal,
            'kelas' => $kelas,
            'guru' => auth()->user()
        ]);

        $filename = $this->generateFilename('Harian', $tanggal, $kelas);
        
        return $pdf->download($filename);
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $jadwal_id = $request->input('jadwal_id');

        if (!$jadwal_id) {
            return back()->with('error', 'Silakan pilih jadwal terlebih dahulu');
        }

        $jadwal = Jadwal::with(['kelas', 'mataPelajaran'])->find($jadwal_id);

        $pertemuans = Pertemuan::with(['absensis.siswa'])
            ->where('jadwal_id', $jadwal_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $rekap = $this->rekapService->hitungRekap($pertemuans, $jadwal);

        $pdf = PDF::loadView('guru.laporan.pdf.bulanan', [
            'rekap' => $rekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jadwal' => $jadwal,
            'pertemuans' => $pertemuans,
            'guru' => auth()->user()
        ])->setPaper('a4', 'landscape');

        $bulan_nama = \Carbon\Carbon::create()->month((int) $bulan)->format('F');
        $filename = "Laporan_Bulanan_{$bulan_nama}_{$tahun}_" .
            str_replace(' ', '_', $jadwal->mataPelajaran->nama_mapel) . '_' .
            str_replace(' ', '_', $jadwal->kelas->nama_kelas) . '.pdf';

        return $pdf->download($filename);
    }

    public function semester(Request $request)
    {
        $semester = $request->input('semester', 'ganjil');
        $tahunAjaran = $request->input('tahun_ajaran', date('Y'));
        $jadwal_id = $request->input('jadwal_id');

        if (!$jadwal_id) {
            return back()->with('error', 'Silakan pilih jadwal terlebih dahulu');
        }

        $jadwal = Jadwal::with(['kelas', 'mataPelajaran'])->find($jadwal_id);

        $pertemuans = Pertemuan::with(['absensis.siswa'])
            ->where('jadwal_id', $jadwal_id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        $rekap = $this->rekapService->hitungRekap($pertemuans, $jadwal);

        $pdf = PDF::loadView('guru.laporan.pdf.semester', [
            'rekap' => $rekap,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'jadwal' => $jadwal,
            'pertemuans' => $pertemuans,
            'guru' => auth()->user()
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Semester_' . ucfirst($semester) . "_{$tahunAjaran}_" .
            str_replace(' ', '_', $jadwal->mataPelajaran->nama_mapel) . '_' .
            str_replace(' ', '_', $jadwal->kelas->nama_kelas) . '.pdf';

        return $pdf->download($filename);
    }

    private function generateFilename($type, $tanggal, $kelas = null)
    {
        $filename = "Laporan_{$type}_" . date('Y-m-d', strtotime($tanggal));
        
        if ($kelas) {
            $filename .= '_' . str_replace(' ', '_', $kelas->nama_kelas);
        }
        
        return $filename . '.pdf';
    }
}