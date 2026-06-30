@extends('layouts.admin')

@section('content')
    <style>
        .btn-tambah {
            background: #F08519;
            border-color: #F08519;
            color: #fff;
        }

        .btn-tambah:hover {
            background: #d9720f;
            border-color: #d9720f;
            color: #fff;
        }
    </style>
    <div class="page-card">

        <div class="page-header">

            <h4>
                Kelola Berita
            </h4>

            <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahBerita">

                Tambah

            </button>

        </div>

        <table class="table mesin-table">

            <thead>

                <tr>

                    <th width="80">
                        Gambar
                    </th>

                    <th>
                        Judul
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Dibuat
                    </th>

                    <th width="120">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($news as $item)
                    <tr>

                        <td>

                            @if ($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" width="60">
                            @endif

                        </td>

                        <td>

                            {{ $item->judul }}

                        </td>

                        <td>

                            @if ($item->status == 'publish')
                                <span class="badge bg-success">
                                    Publish
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    Draft
                                </span>
                            @endif

                        </td>

                        <td>

                            {{ $item->created_at->format('d M Y') }}

                        </td>

                        <td>

                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalEdit{{ $item->id }}">

                                Edit

                            </button>

                            <form action="{{ route('berita.destroy', $item->id) }}" method="POST"
                                class="d-inline form-delete">

                                @csrf
                                @method('DELETE')

                                <button type="button" class="btn btn-danger btn-sm btn-delete">
                                    Hapus
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5">

                            Belum ada berita

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    @include('admin.berita.modal-tambah')

    @include('admin.berita.modal-edit')

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {

                const form = this.closest('form');

                Swal.fire({
                    title: 'Hapus Berita?',
                    text: 'Data berita yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });
        });
    </script>
@endsection
