@extends('layouts.admin')

@section('content')
    <style>
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
    </style>
    <div class="page-card">

        <div class="page-header">

            <h4>Daftar Mesin</h4>

            <div class="header-action">

                <a href="{{ route('mesin.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>

                <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">

                    Tambah

                </button>

            </div>

        </div>

        <div class="table-responsive">

            <table class="table mesin-table">

                <thead>
                    <tr>
                        <th>ID Mesin</th>
                        <th>Kecamatan</th>
                        <th>Status Mesin</th>
                        <th>Lokasi Penempatan</th>
                        <th>Stok Beras (KG)</th>
                        <th>Jadwal Mulai</th>
                        <th>Jadwal Selesai</th>
                        <th>Status Jadwal</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($machines as $machine)
                        <tr>

                            <td>
                                {{ $machine->machine_code }}
                            </td>

                            <td>
                                {{ $machine->village?->district?->name ?? '-' }}
                            </td>

                            <td>

                                @if ($machine->status_mesin == 'aktif')
                                    <span class="badge-on">
                                        Aktif
                                    </span>
                                @elseif($machine->status_mesin == 'maintenance')
                                    <span class="badge-warning">
                                        Maintenance
                                    </span>
                                @else
                                    <span class="badge-off">
                                        Nonaktif
                                    </span>
                                @endif

                            </td>

                            <td>
                                {{ $machine->lokasi_penempatan }}
                            </td>

                            <td>
                                {{ number_format($machine->stok_beras_kg) }} Kg
                            </td>

                            <td>
                                {{ $machine->jadwal_mulai ?? '-' }}
                            </td>

                            <td>
                                {{ $machine->jadwal_selesai ?? '-' }}
                            </td>

                            <td>

                                @if ($machine->status_jadwal == 'aktif')
                                    <span class="badge-jadwal">
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge-off">
                                        Nonaktif
                                    </span>
                                @endif

                            </td>

                            <td>

                                <button class="btn-action" data-bs-toggle="modal"
                                    data-bs-target="#modalMesin{{ $machine->id }}">
                                    <i class="bi bi-three-dots"></i>
                                </button>

                            </td>

                        </tr>
                        <div class="modal fade" id="modalMesin{{ $machine->id }}" tabindex="-1">
                            <div class="modal-dialog">

                                <div class="modal-content">

                                    <div class="text-center modal-body">

                                        <h3 class="mb-4">
                                            {{ $machine->machine_code }}
                                        </h3>

                                        <div class="mb-3 row g-2">

                                            <div class="col-6">
                                                <button type="button" class="btn btn-primary btn-control w-100"
                                                    data-bs-toggle="modal" data-bs-target="#modalEdit{{ $machine->id }}">
                                                    Edit
                                                </button>
                                            </div>

                                            <div class="col-6">
                                                <form action="{{ route('mesin.destroy', $machine->id) }}" method="POST"
                                                    class="delete-form">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-danger btn-control w-100">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>

                                        </div>

                                        <button type="button" class="mb-3 btn btn-dark w-100" data-bs-toggle="modal"
                                            data-bs-target="#modalJadwal{{ $machine->id }}">
                                            Ubah Jadwal
                                        </button>

                                        <form action="{{ route('mesin.toggle-status', $machine->id) }}" method="POST">

                                            @csrf
                                            @method('PATCH')

                                            <div class="form-check form-switch">

                                                <input class="form-check-input" type="checkbox"
                                                    onchange="this.form.submit()"
                                                    {{ $machine->status_mesin == 'aktif' ? 'checked' : '' }}>

                                                <label class="form-check-label">
                                                    Mesin Aktif
                                                </label>

                                            </div>

                                        </form>

                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="modal fade" id="modalEdit{{ $machine->id }}" tabindex="-1">

                            <div class="modal-dialog modal-lg">

                                <form action="{{ route('mesin.update', $machine->id) }}" method="POST">

                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5>Edit Mesin</h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="mb-3 col-md-6">

                                                    <label>ID Mesin</label>

                                                    <input type="text" name="machine_code" class="form-control"
                                                        value="{{ $machine->machine_code }}">

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>Provinsi</label>

                                                    <select class="form-control edit-province"
                                                        data-machine="{{ $machine->id }}">

                                                        @foreach ($provinces as $province)
                                                            <option value="{{ $province->id }}"
                                                                {{ $province->id == $machine->village->district->regency->province->id ? 'selected' : '' }}>

                                                                {{ $province->name }}

                                                            </option>
                                                        @endforeach

                                                    </select>

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>Kabupaten</label>

                                                    <select class="form-control edit-regency"
                                                        data-machine="{{ $machine->id }}">

                                                        <option value="{{ $machine->village->district->regency->id }}"
                                                            selected>

                                                            {{ $machine->village->district->regency->name }}

                                                        </option>

                                                    </select>

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>Kecamatan</label>

                                                    <select class="form-control edit-district"
                                                        data-machine="{{ $machine->id }}">

                                                        <option value="{{ $machine->village->district->id }}" selected>

                                                            {{ $machine->village->district->name }}

                                                        </option>

                                                    </select>

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>Desa</label>

                                                    <select name="village_id" class="form-control edit-village"
                                                        data-machine="{{ $machine->id }}">

                                                        <option value="{{ $machine->village->id }}" selected>

                                                            {{ $machine->village->name }}

                                                        </option>

                                                    </select>

                                                </div>
                                                <div class="mb-3 col-md-6">

                                                    <label>Stok Beras (KG)</label>

                                                    <input type="number" name="stok_beras_kg" class="form-control"
                                                        min="0" value="{{ $machine->stok_beras_kg }}">

                                                </div>

                                                <div class="mb-3 col-md-12">

                                                    <label>Lokasi Mesin</label>

                                                    <textarea name="lokasi_penempatan" class="form-control">{{ $machine->lokasi_penempatan }}</textarea>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button class="btn btn-primary">

                                                Simpan Perubahan

                                            </button>

                                        </div>

                                    </div>

                                </form>

                            </div>

                        </div>
                        <div class="modal fade" id="modalJadwal{{ $machine->id }}" tabindex="-1">

                            <div class="modal-dialog">

                                <form action="{{ route('mesin.update-jadwal', $machine->id) }}" method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5>
                                                Ubah Jadwal Mesin
                                            </h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">

                                                <label>
                                                    Jadwal Mulai
                                                </label>

                                                <input type="datetime-local" name="jadwal_mulai" class="form-control"
                                                    value="{{ $machine->jadwal_mulai ? \Carbon\Carbon::parse($machine->jadwal_mulai)->format('Y-m-d\TH:i') : '' }}">

                                            </div>

                                            <div class="mb-3">

                                                <label>
                                                    Jadwal Selesai
                                                </label>

                                                <input type="datetime-local" name="jadwal_selesai" class="form-control"
                                                    value="{{ $machine->jadwal_selesai ? \Carbon\Carbon::parse($machine->jadwal_selesai)->format('Y-m-d\TH:i') : '' }}">

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button class="btn btn-primary">

                                                Simpan Jadwal

                                            </button>

                                        </div>

                                    </div>

                                </form>

                            </div>

                        </div>

                    @empty

                        <tr>

                            <td colspan="9" class="py-4 text-center">

                                Belum ada data mesin

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>
            <div class="modal fade" id="modalTambah">

                <div class="modal-dialog modal-lg">

                    <form action="{{ route('mesin.store') }}" method="POST">

                        @csrf

                        <div class="modal-content">

                            <div class="modal-header">

                                <h5>Tambah Mesin</h5>

                            </div>

                            <div class="modal-body">

                                <div class="row">

                                    <div class="mb-3 col-md-6">

                                        <label>Provinsi</label>

                                        <select id="province" class="form-control">

                                            <option value="">
                                                Pilih Provinsi
                                            </option>

                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}">
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>Kabupaten</label>

                                        <select id="regency" class="form-control">

                                            <option value="">
                                                Pilih Kabupaten
                                            </option>

                                        </select>

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>Kecamatan</label>

                                        <select id="district" class="form-control">

                                            <option value="">
                                                Pilih Kecamatan
                                            </option>

                                        </select>

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>Desa</label>

                                        <select id="village" name="village_id" class="form-control">

                                            <option value="">
                                                Pilih Desa
                                            </option>

                                        </select>

                                    </div>

                                    <div class="mb-3 col-md-6">

                                        <label>ID Mesin</label>

                                        <input type="text" name="machine_code" class="form-control">

                                    </div>

                                    <div class="mb-3 col-md-12">

                                        <label>Lokasi Mesin</label>

                                        <textarea name="lokasi_penempatan" class="form-control"></textarea>

                                    </div>

                                </div>

                            </div>

                            <div class="modal-footer">

                                <button class="btn btn-tambah">

                                    Simpan

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
    <script>
        document
            .getElementById('province')
            .addEventListener('change', function() {

                fetch('/regencies/' + this.value)

                    .then(res => res.json())

                    .then(data => {

                        let html =
                            '<option value="">Pilih Kabupaten</option>';

                        data.forEach(item => {

                            html +=
                                `<option value="${item.id}">
                ${item.name}
            </option>`;
                        });

                        document.getElementById('regency').innerHTML = html;
                    });

            });

        document
            .getElementById('regency')
            .addEventListener('change', function() {

                fetch('/districts/' + this.value)

                    .then(res => res.json())

                    .then(data => {

                        let html =
                            '<option value="">Pilih Kecamatan</option>';

                        data.forEach(item => {

                            html +=
                                `<option value="${item.id}">
                ${item.name}
            </option>`;
                        });

                        document.getElementById('district').innerHTML = html;
                    });

            });

        document
            .getElementById('district')
            .addEventListener('change', function() {

                fetch('/villages/' + this.value)

                    .then(res => res.json())

                    .then(data => {

                        let html =
                            '<option value="">Pilih Desa</option>';

                        data.forEach(item => {

                            html +=
                                `<option value="${item.id}">
                ${item.name}
            </option>`;
                        });

                        document.getElementById('village').innerHTML = html;
                    });

            });
    </script>
    <script>
        document.querySelectorAll('.edit-province')
            .forEach(function(province) {

                province.addEventListener('change', function() {

                    const machineId =
                        this.dataset.machine;

                    fetch('/regencies/' + this.value)

                        .then(res => res.json())

                        .then(data => {

                            let html =
                                '<option value="">Pilih Kabupaten</option>';

                            data.forEach(item => {

                                html += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;
                            });

                            document.querySelector(
                                '.edit-regency[data-machine="' +
                                machineId +
                                '"]'
                            ).innerHTML = html;
                        });

                });

            });


        document.querySelectorAll('.edit-regency')
            .forEach(function(regency) {

                regency.addEventListener('change', function() {

                    const machineId =
                        this.dataset.machine;

                    fetch('/districts/' + this.value)

                        .then(res => res.json())

                        .then(data => {

                            let html =
                                '<option value="">Pilih Kecamatan</option>';

                            data.forEach(item => {

                                html += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;
                            });

                            document.querySelector(
                                '.edit-district[data-machine="' +
                                machineId +
                                '"]'
                            ).innerHTML = html;
                        });

                });

            });


        document.querySelectorAll('.edit-district')
            .forEach(function(district) {

                district.addEventListener('change', function() {

                    const machineId =
                        this.dataset.machine;

                    fetch('/villages/' + this.value)

                        .then(res => res.json())

                        .then(data => {

                            let html =
                                '<option value="">Pilih Desa</option>';

                            data.forEach(item => {

                                html += `
                    <option value="${item.id}">
                        ${item.name}
                    </option>
                `;
                            });

                            document.querySelector(
                                '.edit-village[data-machine="' +
                                machineId +
                                '"]'
                            ).innerHTML = html;
                        });

                });

            });
    </script>
    <script>
        document.querySelectorAll('.delete-form')
            .forEach(form => {

                form.addEventListener('submit', function(e) {

                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Mesin?',
                        text: 'Data mesin akan dihapus permanen',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });
    </script>
@endsection
