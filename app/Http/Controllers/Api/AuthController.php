<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * 🚀 FIX: helper kecil biar URL avatar konsisten dibangun dari host yang
     * BENERAN dipakai buat manggil API (bukan dari APP_URL statis di .env).
     * Ini yang bikin gambar akhirnya nyambung juga di Flutter (emulator/HP),
     * karena "localhost" di .env ga akan pernah kejangkau dari luar server.
     */
    private function avatarUrl(Request $request, ?string $avatar): ?string
    {
        if (!$avatar) return null;
        return $request->getSchemeAndHttpHost() . '/storage/' . $avatar;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah!',
            ], 401);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data'    => [
                'user'  => [
                    'id'      => $user->id,
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'role'    => $user->role,
                    'phone'   => $user->phone,
                    'address' => $user->address,
                    'city'    => $user->city,
                    'avatar'  => $this->avatarUrl($request, $user->avatar),
                ],
                'token' => $token,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'buyer',
        ]);

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil!',
            'data'    => [
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil!',
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'role'    => $user->role,
                'phone'   => $user->phone,
                'address' => $user->address,
                'city'    => $user->city,
                'avatar'  => $this->avatarUrl($request, $user->avatar),
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city'    => 'nullable|string|max:100',
        ]);

        $user->update($request->only('name', 'phone', 'address', 'city'));

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'data'    => $user,
        ]);
    }

    /**
     * 🚀 FIX: method ini sebelumnya BELUM ADA sama sekali, padahal rute
     * POST /profile/avatar udah didaftarin di routes/api.php — jadi kalau
     * dipanggil dari Flutter bakal error "method not found". Sekarang dibikin,
     * pakai ImageCompressor yang sama kayak avatar upload di web app
     * (folder 'avatars', max lebar 400px, kualitas 75, otomatis jadi .webp).
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $avatarPath = ImageCompressor::compressAndStore($request->file('avatar'), 'avatars', 400, 75);

        $user->update(['avatar' => $avatarPath]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui!',
            'data'    => [
                'avatar' => $this->avatarUrl($request, $avatarPath),
            ],
        ]);
    }
}