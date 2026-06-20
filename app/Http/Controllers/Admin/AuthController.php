<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin()
    {
        if (session('is_admin')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /**
     * Handle admin login attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // Get passwords from environment
        $adminPassword = env('ADMIN_PASSWORD', 'admin123');
        $clientPassword = env('CLIENT_PASSWORD', 'client123');

        if ($request->password === $adminPassword) {
            session([
                'is_admin' => true,
                'admin_role' => 'admin'
            ]);
            return redirect()->route('admin.dashboard');
        } elseif ($request->password === $clientPassword) {
            session([
                'is_admin' => true,
                'admin_role' => 'client'
            ]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['password' => 'Incorrect password. Please try again.'])->withInput();
    }

    /**
     * Log the admin out.
     */
    public function logout()
    {
        session()->forget('is_admin');
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
