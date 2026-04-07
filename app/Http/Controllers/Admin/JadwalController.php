<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use DB;
use App\Http\Requests\JadwalRequest;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::with(['kelas', 'mataPelajaran', 'guru'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(10);

        return view('admin.jadwal.index', compact('jadwals'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $mapels = MataPelajaran::all();
        $gurus = User::where('role', 'guru')->get();

        return view('admin.jadwal.create', compact('kelas', 'mapels', 'gurus'));
    }

    public function store(JadwalRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Cek bentrok jadwal
            $bentrok = Jadwal::where('kelas_id', $validated['kelas_id'])
                ->where('hari', $validated['hari'])
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                        ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                        ->orWhere(function ($q2) use ($validated) {
                            $q2->where('jam_mulai', '<=', $validated['jam_mulai'])
                                ->where('jam_selesai', '>=', $validated['jam_selesai']);
                        });
                })
                ->exists();

            if ($bentrok) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Jadwal bentrok dengan jadwal lain di hari dan waktu yang sama!');
            }

            Jadwal::create($validated);

            DB::commit();

            return redirect()->route('admin.jadwal.index')
                ->with('success', 'Jadwal berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage());
        }
    }

    public function edit(Jadwal $jadwal)
    {
        $kelas = Kelas::all();
        $mapels = MataPelajaran::all();
        $gurus = User::where('role', 'guru')->get();

        return view('admin.jadwal.edit', compact('jadwal', 'kelas', 'mapels', 'gurus'));
    }

    public function update(JadwalRequest $request, Jadwal $jadwal)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Cek bentrok jadwal (exclude jadwal yang sedang diedit)
            $bentrok = Jadwal::where('kelas_id', $validated['kelas_id'])
                ->where('hari', $validated['hari'])
                ->where('id', '!=', $jadwal->id)
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                        ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                        ->orWhere(function ($q2) use ($validated) {
                            $q2->where('jam_mulai', '<=', $validated['jam_mulai'])
                                ->where('jam_selesai', '>=', $validated['jam_selesai']);
                        });
                })
                ->exists();

            if ($bentrok) {
                return back()->withInput()->with('error', 'Jadwal bentrok dengan jadwal lain!');
            }

            $jadwal->update($validated);

            DB::commit();

            return redirect()->route('admin.jadwal.index')
                ->with('success', 'Jadwal berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui jadwal: ' . $e->getMessage());
        }

    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }

    // Lihat jadwal per kelas
    public function byKelas(Kelas $kelas)
    {
        $jadwals = Jadwal::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $kelas->id)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('admin.jadwal.by-kelas', compact('kelas', 'jadwals'));
    }
}
