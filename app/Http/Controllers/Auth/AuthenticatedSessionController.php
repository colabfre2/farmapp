<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        // Kalau email/password salah, LoginRequest::authenticate() otomatis
        // lempar ValidationException. Laravel otomatis convert exception itu
        // jadi response 422 JSON kalau request minta JSON (fetch/AJAX),
        // atau redirect back dengan session errors kalau request form biasa.
        $request->authenticate();

        $request->session()->regenerate();

        $role = auth()->user()->role;

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
                'message'  => 'Login berhasil!',
                'redirect' => $redirectUrl,
            ]);
        }

        // Fallback untuk submit form biasa (non-AJAX)
        return redirect($redirectUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}