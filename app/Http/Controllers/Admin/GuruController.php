<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Hash;
use App\Http\Requests\GuruRequest;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = User::where('role', 'guru')->paginate(10);
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(GuruRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            //code...
            $validated['password'] = Hash::make($validated['password']);
            $validated['role'] = 'guru';

            User::create($validated);

            DB::commit();

            return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan.');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan data guru: ' . $th->getMessage());
        }
    }

    public function edit(User $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(GuruRequest $request, User $guru)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            //code...
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $guru->update($validated);
            DB::commit();

            return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data guru: ' . $th->getMessage());
        }
    }

    public function destroy(User $guru)
    {
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus.');
    }

}
