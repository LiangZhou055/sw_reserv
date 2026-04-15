<?php

namespace App\Http\Controllers;

use App\Models\StoreUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('store')->check() && Auth::guard('store')->user()->role === 'super') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 只按 role=super + email 过滤，不再强制要求 store_code 为 NULL
        $user = StoreUser::where('role', 'super')
            ->where('email', $credentials['email'])
            ->first();

        if ($user && $user->is_active && Hash::check($credentials['password'], $user->password)) {
            Auth::guard('store')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => __('These credentials do not match our records.'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('store')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $user = Auth::guard('store')->user();

        if (! $user || $user->role !== 'super') {
            abort(403, 'Not allowed');
        }

        return view('admin.dashboard', ['user' => $user]);
    }
}

