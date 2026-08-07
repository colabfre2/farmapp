<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        // validate() otomatis lempar ValidationException kalau gagal.
        // Laravel otomatis convert exception itu jadi response 422 JSON
        // kalau request minta JSON (fetch/AJAX), atau redirect back
        // dengan session errors kalau request form biasa.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'buyer',
        ]);

        event(new Registered($user));

        Auth::login($user);

        $role = $user->role;

        if ($role === 'admin') {
            $redirectUrl = route('admin.dashboard');
        } elseif ($role === 'buyer') {
            $redirectUrl = route('buyer.home');
        } else {
            $redirectUrl = route('login');
        }

        // Kalau request dari fetch/AJAX (welcome page modal), balikin JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Registrasi berhasil!',
                'redirect' => $redirectUrl,
            ]);
        }

        // Fallback untuk submit form biasa (non-AJAX)
        return redirect($redirectUrl);
    }
}