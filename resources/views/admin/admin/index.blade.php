@extends('layouts.admin')

@section('content')
    @php
        $adminCount = $admins->total();
    @endphp

    <style>
        .content-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
        }

        .card-header-admin {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header-admin h5 {
            margin: 0;
            font-weight: 600;
        }

        .table th {
            font-size: 14px;
            font-weight: 600;
            color: #555;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-primary {
            background: #007dea;
            border: none;
        }

        .btn-danger {
            border: none;
        }

        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        }

        .modal-header {
            border-bottom: 1px solid #eee;
        }

        .modal-footer {
            border-top: 1px solid #eee;
        }

        .form-control {
            border-radius: 10px;
            min-height: 45px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #F08519;
        }

        .btn-tambah {
            background: #F08519;
            border-color: #F08519;
            color: #fff;
            font-weight: 500;
            transition: all .2s ease;
        }

        .btn-tambah:hover {
            background: #d9720f;
            border-color: #d9720f;
            color: #fff;
        }

        .admin-name-cell {
            color: #111827;
            font-weight: 500;
        }

        .admin-username {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 8px;
            background: #f8fafc;
            color: #4b5563;
            font-size: 13px;
            font-weight: 500;
        }

        /* .btn-tambah:focus,
                .btn-tambah:active {
                    background: #c9670d !important;
                    border-color: #c9670d !important;
                    box-shadow: 0 0 0 0.2rem rgba(240, 133, 25, 0.25);
                } */

        .table-responsive {
            border: 1px solid #edf0f4;
            border-radius: 14px;
            max-height: 65vh;
            overflow-y: auto;
            overflow-x: auto;
            position: relative;
        }

        .mesin-table {
            margin-bottom: 0;
        }

        .mesin-table thead th {
            background-color: #f8fafc !important;
            padding-top: 14px;
            padding-bottom: 14px;
            white-space: nowrap;
            font-weight: 600;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: inset 0 -1px 0 #edf0f4;
        }

        .mesin-table tbody td {
            padding-top: 14px;
            padding-bottom: 14px;
            vertical-align: middle;
        }

        .mesin-table tbody tr:hover {
            background: #f9fafb;
        }
    </style>
    <div class="page-card">

        <div class="admin-page-header">

            <div class="admin-title-block">
                <h4 class="admin-page-title">
                    Kelola Admin
                </h4>
                <p class="admin-page-subtitle">
                    Atur akun pengelola yang memiliki akses ke dashboard.
                </p>

                <div class="admin-summary">
                    <span class="summary-pill">
                        Total Admin
                        <strong>{{ number_format($adminCount) }}</strong>
                    </span>
                    <span class="summary-pill">
                        Halaman
                        <strong>{{ $admins->currentPage() }}/{{ $admins->lastPage() }}</strong>
                    </span>
                </div>
            </div>

            <div class="admin-actions">
                <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">

                    <i class="bi bi-plus-lg"></i>

                    Tambah

                </button>
            </div>

        </div>

        <div class="table-responsive">

            <table class="table mesin-table align-middle">

                <thead>

                    <tr>

                        <th width="80">
                            No
                        </th>

                        <th>
                            Nama
                        </th>

                        <th>
                            Username
                        </th>

                        <th>
                            Email
                        </th>

                        <th width="180">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($admins as $admin)
                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <span class="admin-name-cell">
                                    {{ $admin->nama }}
                                </span>
                            </td>

                            <td>
                                <span class="admin-username">
                                    {{ $admin->username }}
                                </span>
                            </td>

                            <td>
                                {{ $admin->email ?? '-' }}
                            </td>

                            <td>
                                <div class="action-group">

                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalEditAdmin{{ $admin->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <form action="{{ route('admin-management.destroy', $admin->id) }}" method="POST"
                                        class="d-inline delete-form">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>
                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="table-empty">

                                Belum ada data admin

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $admins->links('pagination::bootstrap-5') }}
        </div>

    </div>
    @foreach ($admins as $admin)
        <div class="modal fade" id="modalEditAdmin{{ $admin->id }}" tabindex="-1">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <form action="{{ route('admin-management.update', $admin->id) }}" method="POST">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">

                            <h5 class="modal-title">

                                Edit Admin

                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body">

                            <div class="mb-3">

                                <label class="form-label">
                                    Nama
                                </label>

                                <input type="text" name="nama" class="form-control" value="{{ $admin->nama }}"
                                    required placeholder="Masukkan nama lengkap admin">

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Username
                                </label>

                                <input type="text" name="username" class="form-control" value="{{ $admin->username }}"
                                    required placeholder="Masukkan username admin">

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input type="email" name="email" class="form-control" value="{{ $admin->email }}"
                                    placeholder="Masukkan email admin">

                            </div>

                            <div class="mb-2">

                                <label class="form-label">
                                    Password Baru
                                </label>

                                <input type="password" name="password" class="form-control"
                                    minlength="6" placeholder="Masukkan password baru (min. 6 karakter)">

                            </div>

                            <small class="text-muted">

                                *Kosongkan password jika tidak ingin mengubah password.

                            </small>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                                Batal

                            </button>

                            <button type="submit" class="btn btn-primary">

                                Update

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    @endforeach
    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="modalTambahAdmin" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form action="{{ route('admin-management.store') }}" method="POST">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Tambah Admin
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">
                                Nama
                            </label>

                            <input type="text" name="nama" class="form-control" required
                                placeholder="Masukkan nama lengkap admin">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Username
                            </label>

                            <input type="text" name="username" class="form-control" required
                                placeholder="Masukkan username admin">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input type="email" name="email" class="form-control"
                                placeholder="Masukkan email admin">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input type="password" name="password" class="form-control" required
                                minlength="6" placeholder="Masukkan password (minimal 6 karakter)">

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit" class="btn btn-tambah">

                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    <script>
        document.querySelectorAll(
            '.delete-form'
        ).forEach(form => {

            form.addEventListener(
                'submit',
                function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Admin?',
                        text: 'Data tidak dapat dikembalikan',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545'
                    }).then(result => {

                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            );
        });
    </script>
@endsection
