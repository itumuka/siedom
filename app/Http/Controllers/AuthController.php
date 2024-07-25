<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');


        $admin = Admin::where('username', $username)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            Session::put('admin', $admin);
            return response()->json(['success' => 'Admin']);
        }


        $mahasiswa = Mahasiswa::where('nim', $username)->first();
        if ($mahasiswa && Hash::check($password, $mahasiswa->password_mhs)) {
            Session::put('mahasiswa', $mahasiswa);
            return response()->json(['success' => 'Mahasiswa']);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}

