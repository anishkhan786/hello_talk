<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class adminController extends Controller
{

public function login(Request $request)
{
    if ($request->isMethod('post')) {
        // ✅ Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // ✅ Check if user is admin
            if (Auth::user()->type !== 'admin') {
                Auth::logout();
                return redirect()->back()->with('error', 'You are not authorized to access the admin panel.');
            }

            // ✅ Success — redirect to dashboard
            return redirect()->route('descbord')->with('success', 'Login successful 🙂');
        }

        // ❌ Wrong credentials
        return redirect()->back()->with('error', 'Incorrect email or password 😕');
    }

    // 📃 For GET request, return the login view
    return view('login');
}

public function dashboard(){
    return view('/dashboard');
}

public function logout(Request $request)
{
    Auth::logout();

    // Optional: invalidate session and regenerate CSRF token
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin.login')->with('success', 'Logged out successfully 🙂');
}

}
