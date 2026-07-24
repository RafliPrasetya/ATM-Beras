@extends('layouts.admin')

@section('content')
    @php
        $newsStats = [
            'total' => $news->count(),
            'publish' => $news->where('status', 'publish')->count(),
            'draft' => $news->where('status', 'draft')->count(),
        ];
    @endphp

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

        .news-title-cell {
            max-width: 420px;
            color: #111827;
            font-weight: 500;
            white-space: normal;
        }

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
                    Kelola Berita
                </h4>
                <p class="admin-page-subtitle">
                    Atur konten landing page, status publikasi, dan gambar berita.
                </p>

                <div class="admin-summary">
                    <span class="summary-pill">
                        Total
                        <strong>{{ number_format($newsStats['total']) }}</strong>
                    </span>
                    <span class="summary-pill">
                        Publish
                        <strong>{{ number_format($newsStats['publish']) }}</strong>
                    </span>
                    <span class="summary-pill">
                        Draft
                        <strong>{{ number_format($newsStats['draft']) }}</strong>
                    </span>
                </div>
            </div>

            <div class="admin-actions">
                <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahBerita">

                    <i class="bi bi-plus-lg"></i>

                    Tambah

                </button>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table mesin-table">

                <thead>

                    <tr>

                        <th width="90">
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
                                    <img src="{{ asset('storage/' . $item->gambar) }}" class="table-thumb"
                                        alt="{{ $item->judul }}">
                                @else
                                    <span class="table-thumb-placeholder">
                                        <i class="bi bi-image"></i>
                                    </span>
                                @endif

                            </td>

                            <td class="news-title-cell">

                                {{ $item->judul }}

                            </td>

                            <td>

                                @if ($item->status == 'publish')
                                    <span class="badge-on status-badge">
                                        <i class="bi bi-circle-fill"></i>
                                        Publish
                                    </span>
                                @else
                                    <span class="badge-off status-badge">
                                        <i class="bi bi-circle-fill"></i>
                                        Draft
                                    </span>
                                @endif

                            </td>

                            <td>

                                {{ $item->created_at->format('d M Y') }}

                            </td>

                            <td>
                                <div class="action-group">
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $item->id }}">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>

                                    <form action="{{ route('berita.destroy', $item->id) }}" method="POST"
                                        class="d-inline form-delete">

                                        @csrf
                                        @method('DELETE')

                                        <button type="button" class="btn btn-danger btn-sm btn-delete">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>
                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="table-empty">

                                Belum ada berita

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $news->links('pagination::bootstrap-5') }}
        </div>

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
