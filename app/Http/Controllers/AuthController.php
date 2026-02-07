<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // plain PHP view
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user && $request->password === $user->password) {
            // Store user in session
            Session::put('user', $user);

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect('/dashboard/admin');
            } elseif ($user->role === 'supplier') {
                return redirect('/dashboard/supplier');
            } elseif ($user->role === 'pharmacy') {
                return redirect('/dashboard/pharmacy');
            }
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }
}
