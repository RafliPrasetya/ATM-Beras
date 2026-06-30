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
            'username' => 'required|unique:admins',
            'email' => 'nullable|email',
            'password' => 'required|min:6'
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
            'username' =>
            'required|unique:admins,username,' .
                $adminManagement->id,
            'email' => 'nullable|email'
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
