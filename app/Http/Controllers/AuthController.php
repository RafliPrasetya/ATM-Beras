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
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Kolom Username tidak boleh kosong.',
            'password.required' => 'Kolom Password tidak boleh kosong.',
        ]);

        $admin = Admin::where(
            'username',
            $request->username
        )->first();

        if (!$admin) {
            if ($request->expectsJson() || $request->ajax() || $request->input('ajax')) {
                return response()->json([
                    'errors' => [
                        'username' => ['Username tidak ditemukan dalam sistem.']
                    ]
                ], 422);
            }
            return back()->withErrors([
                'username' => 'Username tidak ditemukan dalam sistem.'
            ])->withInput($request->only('username'));
        }

        if (!Hash::check(
            $request->password,
            $admin->password
        )) {
            if ($request->expectsJson() || $request->ajax() || $request->input('ajax')) {
                return response()->json([
                    'errors' => [
                        'password' => ['Password yang Anda masukkan salah.']
                    ]
                ], 422);
            }
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.'
            ])->withInput($request->only('username'));
        }

        session([
            'admin_id' => $admin->id,
            'admin_name' => $admin->nama
        ]);

        if ($request->expectsJson() || $request->ajax() || $request->input('ajax')) {
            return response()->json([
                'redirect' => '/dashboard'
            ]);
        }

        return redirect('/dashboard');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/login');
    }
}
