<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ImageCompressor;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'public_email' => 'nullable|email|max:255', // Validasi email publik
            'phone'        => 'nullable|string|max:20',
            'city'         => 'nullable|string|max:100',
            'address'      => 'nullable|string',
            'whatsapp'     => 'nullable|string|max:20',
            'instagram'    => 'nullable|string|max:255',
            'facebook'     => 'nullable|string|max:255',
            'avatar'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $avatarPath = $user->avatar;
        
        // EKSEKUSI KOMPRESI KE WEBP
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama di storage kalau ada
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Panggil helper: parameter (file, nama_folder, max_lebar_px, kualitas_0-100)
            $avatarPath = ImageCompressor::compressAndStore($request->file('avatar'), 'avatars', 400, 75);
        }

        $user->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'public_email' => $request->public_email, // Simpan email publik
            'phone'        => $request->phone,
            'city'         => $request->city,
            'address'      => $request->address,
            'whatsapp'     => $request->whatsapp,
            'instagram'    => $request->instagram,
            'facebook'     => $request->facebook,
            'avatar'       => $avatarPath,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profil dan informasi kontak berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            // Menggunakan withBag agar error lemparan password nyangkut dengan pas di form password
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai!'], 'updatePassword');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diubah!');
    }
}