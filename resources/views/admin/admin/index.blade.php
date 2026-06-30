@extends('layouts.admin')

@section('content')
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

        /* .btn-tambah:focus,
                .btn-tambah:active {
                    background: #c9670d !important;
                    border-color: #c9670d !important;
                    box-shadow: 0 0 0 0.2rem rgba(240, 133, 25, 0.25);
                } */
    </style>
    <div class="content-card">

        <div class="card-header-admin">

            <h5>
                Manajemen Admin
            </h5>

            <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">

                Tambah

            </button>

        </div>

        <div class="table-responsive">

            <table class="table align-middle">

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
                                {{ $admin->nama }}
                            </td>

                            <td>
                                {{ $admin->username }}
                            </td>

                            <td>
                                {{ $admin->email }}
                            </td>

                            <td>

                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditAdmin{{ $admin->id }}">
                                    Edit
                                </button>

                                <form action="{{ route('admin-management.destroy', $admin->id) }}" method="POST"
                                    class="d-inline delete-form">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="py-4 text-center">

                                Belum ada data admin

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

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
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Username
                                </label>

                                <input type="text" name="username" class="form-control" value="{{ $admin->username }}"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input type="email" name="email" class="form-control" value="{{ $admin->email }}">

                            </div>

                            <div class="mb-2">

                                <label class="form-label">
                                    Password Baru
                                </label>

                                <input type="password" name="password" class="form-control">

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

                            <input type="text" name="nama" class="form-control" required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Username
                            </label>

                            <input type="text" name="username" class="form-control" required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input type="email" name="email" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input type="password" name="password" class="form-control" required>

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
