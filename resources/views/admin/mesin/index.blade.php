@extends('layouts.admin')

@section('content')
    @php
        $machineStats = [
            'total' => $machines->count(),
            'aktif' => $machines->where('status_mesin', 'aktif')->count(),
            'nonaktif' => $machines->where('status_mesin', 'nonaktif')->count(),
            'maintenance' => $machines->where('status_mesin', 'maintenance')->count(),
        ];
    @endphp

    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .spinning {
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        .table-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99;
            border-radius: 14px;
            backdrop-filter: blur(2px);
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

        .machine-code {
            color: #111827;
            font-weight: 500;
        }

        .machine-location {
            max-width: 260px;
            white-space: normal;
        }

        .table-responsive {
            border: 1px solid #edf0f4;
            border-radius: 14px;
            max-height: 60vh;
            overflow-y: auto;
            overflow-x: auto;
        }

        .mesin-table {
            margin-bottom: 0;
        }

        .mesin-table thead th {
            background: #f8fafc;
            padding-top: 14px;
            padding-bottom: 14px;
            white-space: nowrap;
            font-weight: 600;
            border: none;
        }

        .mesin-table tbody td {
            padding-top: 14px;
            padding-bottom: 14px;
            vertical-align: middle;
        }

        .mesin-table tbody tr:hover {
            background: #f9fafb;
        }

        /* Custom Toggle Switch */
        .custom-switch {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            gap: 12px;
            margin: 10px auto;
        }

        .custom-switch input {
            display: none;
        }

        .switch-slider {
            position: relative;
            width: 50px;
            height: 26px;
            background-color: #e5e7eb;
            border-radius: 30px;
            transition: 0.3s all ease-in-out;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .switch-slider::before {
            content: "";
            position: absolute;
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            border-radius: 50%;
            transition: 0.3s all ease-in-out;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        .custom-switch input:checked + .switch-slider {
            background-color: #10b981;
        }

        .custom-switch input:checked + .switch-slider::before {
            transform: translateX(24px);
        }

        .switch-label {
            font-size: 15px;
            font-weight: 500;
            color: #6b7280;
            user-select: none;
        }

        .custom-switch input:checked ~ .switch-label {
            color: #10b981;
            font-weight: 600;
        }

        /* Action Button Styling */
        .btn-action {
            background: #ffffff;
            border: 1px solid #edf0f4;
            color: #4b5563;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .btn-action:hover {
            background: #111827;
            color: #ffffff;
            border-color: #111827;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 24, 39, 0.15);
        }

        .btn-action:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
    </style>
    <div class="page-card">

        <div class="admin-page-header">

            <div class="admin-title-block">
                <h4 class="admin-page-title">Daftar Mesin</h4>
                <p class="admin-page-subtitle">
                    Kelola perangkat ATM, lokasi, stok, dan jadwal operasional.
                </p>

                <div id="mesinSummary" class="admin-summary">
                    <span class="summary-pill">
                        Total
                        <strong>{{ number_format($machineStats['total']) }}</strong>
                    </span>
                    <span class="summary-pill">
                        Aktif
                        <strong>{{ number_format($machineStats['aktif']) }}</strong>
                    </span>
                    <span class="summary-pill">
                        Nonaktif
                        <strong>{{ number_format($machineStats['nonaktif']) }}</strong>
                    </span>
                    <!-- <span class="summary-pill">
                        Maintenance
                        <strong>{{ number_format($machineStats['maintenance']) }}</strong>
                    </span> -->
                </div>
            </div>

            <div class="admin-actions">

                <a id="btnRefreshMesin" href="{{ route('mesin.index') }}" class="btn btn-primary btn-icon">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>

                <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">

                    <i class="bi bi-plus-lg"></i>

                    Tambah

                </button>

            </div>

        </div>

        @php
            $lowStockMachines = $machines->filter(function($machine) {
                return $machine->stok_beras_kg <= 10;
            });
        @endphp

        @if($lowStockMachines->isNotEmpty())
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; border: none; background-color: #fde8e8; color: #9b1c1c; padding: 15px 20px;">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4" style="color: #e02424;"></i>
                <div>
                    <span class="fw-bold">Peringatan Stok Kritis!</span> Mesin berikut memiliki stok beras 10 Kg atau kurang:
                    <ul class="mb-0 mt-1 pl-4" style="padding-left: 20px;">
                        @foreach($lowStockMachines as $lowMachine)
                            <li><strong>{{ $lowMachine->machine_code }}</strong> ({{ $lowMachine->lokasi_penempatan }}) - Sisa Stok: <strong>{{ number_format($lowMachine->stok_beras_kg) }} Kg</strong></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="position-relative">
            <div id="mesinTableWrapper">
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
                                        <span class="machine-code">
                                            {{ $machine->machine_code }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $machine->village?->district?->name ?? '-' }}
                                    </td>

                                    <td>

                                        @if ($machine->status_mesin == 'aktif')
                                            <span class="badge-on status-badge">
                                                <i class="bi bi-circle-fill"></i>
                                                Aktif
                                            </span>
                                        @elseif($machine->status_mesin == 'maintenance')
                                            <span class="badge-warning status-badge">
                                                <i class="bi bi-circle-fill"></i>
                                                Maintenance
                                            </span>
                                        @else
                                            <span class="badge-off status-badge">
                                                <i class="bi bi-circle-fill"></i>
                                                Nonaktif
                                            </span>
                                        @endif

                                    </td>

                                    <td class="machine-location">
                                        {{ $machine->lokasi_penempatan }}
                                    </td>

                                     <td>
                                         @if($machine->stok_beras_kg <= 10)
                                             <span class="text-danger fw-bold d-inline-flex align-items-center">
                                                 <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                                                 {{ number_format($machine->stok_beras_kg) }} Kg
                                             </span>
                                         @else
                                             {{ number_format($machine->stok_beras_kg) }} Kg
                                         @endif
                                     </td>

                                    <td>
                                        {{ $machine->jadwal_mulai ? $machine->jadwal_mulai->format('d M Y H:i') : '-' }}
                                    </td>

                                    <td>
                                        {{ $machine->jadwal_selesai ? $machine->jadwal_selesai->format('d M Y H:i') : '-' }}
                                    </td>

                                    <td>

                                        @if ($machine->status_penjadwalan == 'aktif')
                                            <span class="badge-jadwal status-badge">
                                                <i class="bi bi-circle-fill"></i>
                                                Aktif
                                            </span>
                                        @elseif($machine->status_penjadwalan == 'belum dijadwalkan')
                                            <span class="badge-warning status-badge">
                                                <i class="bi bi-circle-fill"></i>
                                                Belum Dijadwalkan
                                            </span>
                                        @else
                                            <span class="badge-off status-badge">
                                                <i class="bi bi-circle-fill"></i>
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

                                                    <div class="d-flex justify-content-center">
                                                        <label class="custom-switch">
                                                            <input type="checkbox" onchange="this.form.submit()"
                                                                {{ $machine->status_mesin == 'aktif' ? 'checked' : '' }}>
                                                            <span class="switch-slider"></span>
                                                            <span class="switch-label">Aktifkan Mesin</span>
                                                        </label>
                                                    </div>

                                                </form>

                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="modalEdit{{ $machine->id }}" tabindex="-1">

                                    <div class="modal-dialog modal-lg">

                                        <form action="{{ route('mesin.update', $machine->id) }}" method="POST">

                                            @csrf
                                            @method('PUT')

                                            <div class="modal-content">

                                                <div class="modal-header">

                                                    <h5>Edit Mesin</h5>

                                                </div>

                                                <div class="modal-body">

                                                    <div class="row">

                                                        <div class="mb-3 col-md-6">

                                                            <label>ID Mesin</label>

                                                            <input type="text" name="machine_code" class="form-control"
                                                                value="{{ $machine->machine_code }}" required
                                                                placeholder="Masukkan ID Mesin">

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
                                                                value="{{ $machine->stok_beras_kg }}" required
                                                                min="0" placeholder="Contoh: 50">

                                                        </div>

                                                        <div class="mb-3 col-md-12">

                                                            <label>Lokasi Mesin</label>

                                                            <textarea name="lokasi_penempatan" class="form-control" required
                                                                placeholder="Masukkan alamat lengkap lokasi penempatan mesin (Contoh: Masjid Al-Ikhlas RT 01/RW 02)">{{ $machine->lokasi_penempatan }}</textarea>

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

                                <!-- Modal Jadwal -->
                                <div class="modal fade" id="modalJadwal{{ $machine->id }}" tabindex="-1">

                                    <div class="modal-dialog">

                                        <form action="{{ route('mesin.update-jadwal', $machine->id) }}" method="POST">

                                            @csrf
                                            @method('PATCH')

                                            <div class="modal-content">

                                                <div class="modal-header">

                                                    <h5>Jadwal Operasional</h5>

                                                </div>

                                                <div class="modal-body">

                                                    <div class="mb-3">

                                                        <label>Mulai</label>

                                                        <input type="datetime-local" name="jadwal_mulai" class="form-control"
                                                            value="{{ $machine->jadwal_mulai ? $machine->jadwal_mulai->format('Y-m-d\TH:i') : '' }}">

                                                    </div>

                                                    <div class="mb-3">

                                                        <label>Selesai</label>

                                                        <input type="datetime-local" name="jadwal_selesai" class="form-control"
                                                            value="{{ $machine->jadwal_selesai ? $machine->jadwal_selesai->format('Y-m-d\TH:i') : '' }}">

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
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $machines->links('pagination::bootstrap-5') }}
                </div>
            </div>
            <div id="mesinTableSpinner" class="table-loading-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div></div>
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

                                        <input type="text" name="machine_code" class="form-control" required
                                            placeholder="Masukkan ID Mesin">

                                    </div>

                                    <div class="mb-3 col-md-12">

                                        <label>Lokasi Mesin</label>

                                        <textarea name="lokasi_penempatan" class="form-control" required
                                            placeholder="Masukkan alamat lengkap lokasi penempatan mesin (Contoh: Masjid Al-Ikhlas RT 01/RW 02)"></textarea>

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
    <script>
        document.getElementById('btnRefreshMesin').addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const icon = btn.querySelector('i');
            const wrapper = document.getElementById('mesinTableWrapper');
            const summary = document.getElementById('mesinSummary');
            const spinner = document.getElementById('mesinTableSpinner');
            
            if (icon) icon.classList.add('spinning');
            if (wrapper) wrapper.style.opacity = '0.5';
            if (summary) summary.style.opacity = '0.5';
            if (spinner) spinner.classList.remove('d-none');
            
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newWrapper = doc.getElementById('mesinTableWrapper');
                    if (newWrapper && wrapper) {
                        wrapper.innerHTML = newWrapper.innerHTML;
                    }
                    
                    const newSummary = doc.getElementById('mesinSummary');
                    if (newSummary && summary) {
                        summary.innerHTML = newSummary.innerHTML;
                    }
                })
                .catch(err => console.error('Gagal menyegarkan data:', err))
                .finally(() => {
                    if (icon) icon.classList.remove('spinning');
                    if (spinner) spinner.classList.add('d-none');
                    if (wrapper) {
                        wrapper.style.opacity = '1';
                        wrapper.style.transition = 'opacity 0.2s';
                    }
                    if (summary) {
                        summary.style.opacity = '1';
                        summary.style.transition = 'opacity 0.2s';
                    }
                });
        });
    </script>
@endsection
