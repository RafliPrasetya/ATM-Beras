<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::latest()
            ->paginate(10);

        return view(
            'admin.admin.index',
            compact('admins')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'username' => 'required|alpha_dash|unique:admins,username',
            'email' => 'nullable|email',
            'password' => 'required|min:6'
        ], [
            'nama.required' => 'Nama tidak boleh kosong.',
            'username.required' => 'Username tidak boleh kosong.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah terdaftar dalam sistem.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        Admin::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make(
                $request->password
            )
        ]);

        return back()
            ->with(
                'success',
                'Admin berhasil ditambahkan'
            );
    }

    public function update(
        Request $request,
        Admin $adminManagement
    ) {
        $request->validate([
            'nama' => 'required',
            'username' => 'required|alpha_dash|unique:admins,username,'.$adminManagement->id,
            'email' => 'nullable|email',
            'password' => 'nullable|min:6'
        ], [
            'nama.required' => 'Nama tidak boleh kosong.',
            'username.required' => 'Username tidak boleh kosong.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah terdaftar dalam sistem.',
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email
        ];

        if ($request->password) {

            $data['password'] =
                Hash::make(
                    $request->password
                );
        }

        $adminManagement->update(
            $data
        );

        return back()
            ->with(
                'success',
                'Admin berhasil diperbarui'
            );
    }

    public function destroy(
        Admin $adminManagement
    ) {

        $adminManagement->delete();

        return back()
            ->with(
                'success',
                'Admin berhasil dihapus'
            );
    }
}
