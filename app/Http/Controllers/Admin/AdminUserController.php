<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $admins = User::where('role', 'admin')
            ->when($query, fn($q) => $q->where(function ($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.admins.index', compact('admins', 'query'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'nullable|string|max:20',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?: '0',
            'password'          => Hash::make($validated['password']),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Akun admin baru berhasil ditambahkan!');
    }

    public function destroy(User $admin)
    {
        abort_if($admin->role !== 'admin', 404);

        // Jangan sampe admin nge-hapus akunnya sendiri lewat halaman ini (biar gak ke-lockout)
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admins.index')->with('error', 'Kamu gak bisa menghapus akunmu sendiri di sini.');
        }

        // Jaga-jaga minimal harus ada 1 admin tersisa
        if (User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.admins.index')->with('error', 'Gagal! Minimal harus ada 1 akun admin yang aktif.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Akun admin berhasil dihapus.');
    }
}
