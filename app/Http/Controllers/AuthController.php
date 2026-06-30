<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $admin = Admin::where(
            'username',
            $request->username
        )->first();

        if (!$admin) {
            return back()->with(
                'error',
                'Username tidak ditemukan'
            );
        }

        if (!Hash::check(
            $request->password,
            $admin->password
        )) {
            return back()->with(
                'error',
                'Password salah'
            );
        }

        session([
            'admin_id' => $admin->id,
            'admin_name' => $admin->nama
        ]);

        return redirect('/dashboard');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/login');
    }
}
