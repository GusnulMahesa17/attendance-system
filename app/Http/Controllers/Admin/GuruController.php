<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $validated = $request->validate
        (
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'nip' => 'required|unique:users,nip',
                'phone' => 'nullable|string',
                'password' => 'required|min:6'
            ]
        );

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'guru';

        User::create($validated);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(User $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, User $guru)
    {
        $validated = $request->validate
        (
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|unique:users,email,' . $guru->id,
                'nip' => 'required|unique:users,nip,' . $guru->id,
                'phone' => 'nullable|string|max:15',
                'password' => 'nullable|min:6'
            ]
        );

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $guru->update($validated);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(User $guru)
    {
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus.');
    }

}
