<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Step 1: Check if user exists and is active
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if (!$user->is_active) {
            return back()->with('error', 'Your account is inactive. Please contact administrator.');
        }

        // Step 2: Attempt authentication
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // ✅ Redirect to dashboard after login
            return redirect()->intended(route('dashboard.index'));
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
