<?php

namespace App\Services;

use App\Models\Pertemuan;

class RekapAbsensiService
{
    public function hitungRekap($pertemuans, $jadwal)
    {
        $rekap = [];
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

    public function getAllRekap($jadwal_list, $bulan, $tahun)
    {
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
                    'rekap' => $this->hitungRekap($pertemuans, $jadwal),
                    'pertemuans' => $pertemuans
                ];
            }
        }

        return $allRekap;
    }

    public function getAllRekapSemester($jadwal_list, $semester, $tahunAjaran)
    {
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
                    'rekap' => $this->hitungRekap($pertemuans, $jadwal),
                    'pertemuans' => $pertemuans
                ];
            }
        }

        return $allRekap;
    }
}