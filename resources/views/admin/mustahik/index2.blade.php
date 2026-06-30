@extends('layouts.admin')

@section('content')
    <style>
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .page-title {
            margin: 0;
            font-weight: 700;
        }

        .search-wrapper {
            position: relative;
            width: 350px;
        }

        .search-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .search-input {
            width: 100%;
            height: 45px;
            border: none;
            border-radius: 12px;
            background: #f3f4f6;
            padding-left: 42px;
            padding-right: 15px;
            outline: none;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
        }

        .modal-riwayat table th {
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .modal-riwayat table td {
            vertical-align: middle;
        }

        .modal-riwayat .table {
            margin-bottom: 0;
        }

        /* .header-action {
                                                                                                                                                                    display: flex;
                                                                                                                                                                    gap: 12px;
                                                                                                                                                                }

                                                                                                                                                                .header-action .btn {
                                                                                                                                                                    min-width: 55px;
                                                                                                                                                                    height: 45px;
                                                                                                                                                                    border-radius: 10px;
                                                                                                                                                                } */
    </style>
    <div class="page-card">

        <div class="page-header">

            <h4 class="page-title">
                Daftar Mustahik
            </h4>

            <div class="search-wrapper">

                <i class="bi bi-search"></i>

                <input type="text" id="searchMustahik" placeholder="Pencarian..." class="search-input">

            </div>

            <div class="header-action">

                <a href="{{ route('mustahik.index') }}" class="btn btn-primary">

                    <i class="bi bi-arrow-clockwise"></i>

                </a>

                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambahMustahik">

                    Tambah

                </button>

                <button class="btn btn-secondary">

                    <i class="bi bi-download"></i>

                </button>

            </div>

        </div>

        <div class="table-responsive">

            <table class="table mesin-table">

                <thead>

                    <tr>

                        <th>Nama Mustahik</th>

                        <th>ID Penerima</th>

                        <th>NIK</th>

                        <th>No HP</th>

                        <th>Alamat Lengkap</th>

                        <th>Kecamatan</th>

                        <th>Desa</th>

                        <th>Jatah Beras (Gram)</th>

                        <th width="100">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($mustahiks as $mustahik)
                        <tr>

                            <td>{{ $mustahik->nama }}</td>

                            <td>{{ $mustahik->rfid_uid }}</td>

                            <td>{{ $mustahik->nik }}</td>

                            <td>{{ $mustahik->no_hp }}</td>

                            <td>{{ $mustahik->alamat }}</td>

                            <td>
                                {{ $mustahik->village?->district?->name }}
                            </td>

                            <td>
                                {{ $mustahik->village?->name }}
                            </td>

                            <td>
                                {{ number_format($mustahik->jatah_beras_gram) }}
                            </td>

                            <td>

                                <button class="btn-action" data-bs-toggle="modal"
                                    data-bs-target="#modalMustahik{{ $mustahik->id }}">

                                    <i class="bi bi-three-dots"></i>

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9" class="py-4 text-center">

                                Belum ada data mustahik

                            </td>

                        </tr>
                    @endforelse


                </tbody>

            </table>

        </div>

                    @foreach ($mustahiks as $mustahik)
                        <div class="modal fade" id="modalMustahik{{ $mustahik->id }}" tabindex="-1">

                            <div class="modal-dialog">

                                <div class="modal-content">

                                    <div class="text-center modal-body">

                                        <h4 class="mb-1">
                                            {{ $mustahik->nama }}
                                        </h4>

                                        <p class="text-muted">
                                            {{ $mustahik->rfid_uid }}
                                        </p>

                                        <div class="mb-3 row g-2">

                                            <div class="col-6">

                                                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-dismiss="modal"
                                                    data-bs-target="#modalEditMustahik{{ $mustahik->id }}">

                                                    Edit

                                                </button>

                                            </div>

                                            <div class="col-6">

                                                <form action="{{ route('mustahik.destroy', $mustahik->id) }}"
                                                    method="POST" class="delete-form">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-danger w-100">

                                                        Hapus

                                                    </button>

                                                </form>

                                            </div>

                                        </div>

                                        <button class="mb-3 btn btn-success w-100" data-bs-toggle="modal" data-bs-dismiss="modal"
                                            data-bs-target="#modalJatah{{ $mustahik->id }}">

                                            Tambah Jatah

                                        </button>

                                        <button class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-dismiss="modal"
                                            data-bs-target="#modalRiwayat{{ $mustahik->id }}">

                                            Riwayat Pengambilan

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="modal fade" id="modalRiwayat{{ $mustahik->id }}" tabindex="-1">

                            <div class="modal-dialog modal-xl modal-dialog-centered">

                                <div class="border-0 shadow modal-content modal-riwayat">

                                    <div class="p-4 modal-body">

                                        <div
                                            style="
                    border:1px solid #bdbdbd;
                    border-radius:25px;
                    padding:25px;
                ">

                                            {{-- HEADER --}}

                                            <div class="mb-4 d-flex justify-content-between align-items-center">

                                                <h5 class="mb-0 fw-bold">

                                                    Riwayat Pengambilan

                                                </h5>

                                                <div class="gap-2 d-flex">

                                                    <button class="btn btn-outline-secondary rounded-pill btn-sm">

                                                        1 Minggu

                                                    </button>

                                                    <button class="btn btn-outline-secondary rounded-pill btn-sm">

                                                        1 Bulan

                                                    </button>

                                                    <button class="btn btn-outline-secondary rounded-pill btn-sm">

                                                        6 Bulan

                                                    </button>

                                                    <button class="btn btn-dark rounded-pill btn-sm">

                                                        Semua

                                                    </button>

                                                    <button class="btn btn-light rounded-pill btn-sm">

                                                        <i class="bi bi-download"></i>

                                                    </button>

                                                </div>

                                            </div>

                                            {{-- INFORMASI MUSTAHIK --}}

                                            <div class="mb-4 row">

                                                <div class="col-md-4">

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            Nama

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->nama }}

                                                        </strong>

                                                    </p>

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            RFID

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->rfid_uid }}

                                                        </strong>

                                                    </p>

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            NIK

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->nik }}

                                                        </strong>

                                                    </p>

                                                    <p class="mb-0">

                                                        <small class="text-muted">

                                                            Sisa Jatah (Gram)

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ number_format($mustahik->jatah_beras_gram) }}

                                                        </strong>

                                                    </p>

                                                </div>

                                                <div class="col-md-4">

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            Provinsi

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->village?->district?->regency?->province?->name ?? '-' }}

                                                        </strong>

                                                    </p>

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            Kabupaten

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->village?->district?->regency?->name ?? '-' }}

                                                        </strong>

                                                    </p>

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            Kecamatan

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->village?->district?->name ?? '-' }}

                                                        </strong>

                                                    </p>

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            Desa

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->village?->name ?? '-' }}

                                                        </strong>

                                                    </p>

                                                    <p class="mb-0">

                                                        <small class="text-muted">

                                                            Alamat

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->alamat }}

                                                        </strong>

                                                    </p>

                                                </div>

                                                <div class="col-md-4">

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            Pengambilan Terakhir

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ optional($mustahik->transactions->sortByDesc('tanggal_pengambilan')->first())?->tanggal_pengambilan
                                                                ? \Carbon\Carbon::parse(
                                                                    $mustahik->transactions->sortByDesc('tanggal_pengambilan')->first()->tanggal_pengambilan,
                                                                )->format('d M Y')
                                                                : '-' }}

                                                        </strong>

                                                    </p>

                                                    <p class="mb-3">

                                                        <small class="text-muted">

                                                            Total Pengambilan

                                                        </small>

                                                        <br>

                                                        <strong>

                                                            {{ $mustahik->transactions->count() }}

                                                            Kali

                                                        </strong>

                                                    </p>

                                                </div>

                                            </div> {{-- END ROW INFORMASI --}}
                                            <hr class="my-4">

                                            <div class="table-responsive">

                                                <table class="table align-middle table-hover">

                                                    <thead>

                                                        <tr>

                                                            <th width="35%">Tanggal</th>

                                                            <th width="25%">Mesin</th>

                                                            <th width="40%">Jumlah Ambil</th>

                                                        </tr>

                                                    </thead>

                                                    <tbody>

                                                        @forelse($mustahik->transactions
                                ->sortByDesc('tanggal_pengambilan')
                            as $transaction)
                                                            <tr>

                                                                <td>

                                                                    {{ \Carbon\Carbon::parse($transaction->tanggal_pengambilan)->format('d M Y') }}

                                                                </td>

                                                                <td>

                                                                    {{ $transaction->machine->machine_code ?? '-' }}

                                                                </td>

                                                                <td>

                                                                    {{ number_format($transaction->jumlah_ambil_gram) }}

                                                                    gram

                                                                </td>

                                                            </tr>

                                                        @empty

                                                            <tr>

                                                                <td colspan="3" class="py-4 text-center text-muted">

                                                                    Belum ada riwayat pengambilan

                                                                </td>

                                                            </tr>
                                                        @endforelse

                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="modal fade" id="modalJatah{{ $mustahik->id }}" tabindex="-1">

                            <div class="modal-dialog">

                                <form action="{{ route('mustahik.tambah-jatah', $mustahik->id) }}" method="POST">

                                    @csrf

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5>Tambah Jatah Beras</h5>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">

                                                <label>Nama Mustahik</label>

                                                <input type="text" class="form-control" value="{{ $mustahik->nama }}"
                                                    readonly>

                                            </div>

                                            <div class="mb-3">

                                                <label>Tambah Jatah (Gram)</label>

                                                <input type="number" name="jumlah" class="form-control" required>

                                            </div>

                                            <div class="mb-3">

                                                <label>Keterangan</label>

                                                <textarea name="keterangan" class="form-control"></textarea>

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="submit" class="btn btn-success">

                                                Tambah

                                            </button>

                                        </div>

                                    </div>

                                </form>

                            </div>

                        </div>
                        <div class="modal fade" id="modalEditMustahik{{ $mustahik->id }}" tabindex="-1">

                            <div class="modal-dialog modal-lg">

                                <form action="{{ route('mustahik.update', $mustahik->id) }}" method="POST">

                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Edit Mustahik
                                            </h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="mb-3 col-md-6">

                                                    <label>Nama Mustahik</label>

                                                    <input type="text" name="nama" class="form-control"
                                                        value="{{ $mustahik->nama }}">

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>ID RFID</label>

                                                    <input type="text" name="rfid_uid" class="form-control"
                                                        value="{{ $mustahik->rfid_uid }}">

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>NIK</label>

                                                    <input type="text" name="nik" class="form-control"
                                                        value="{{ $mustahik->nik }}">

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>No HP</label>

                                                    <input type="text" name="no_hp" class="form-control"
                                                        value="{{ $mustahik->no_hp }}">

                                                </div>

                                                <div class="mb-3 col-md-12">

                                                    <label>Alamat Lengkap</label>

                                                    <textarea name="alamat" class="form-control">{{ $mustahik->alamat }}</textarea>

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>Provinsi</label>

                                                    <select class="form-control edit-province-mustahik"
                                                        data-mustahik="{{ $mustahik->id }}">

                                                        @foreach ($provinces as $province)
                                                            <option value="{{ $province->id }}"
                                                                {{ $province->id == $mustahik->village?->district?->regency?->province?->id ? 'selected' : '' }}>

                                                                {{ $province->name }}

                                                            </option>
                                                        @endforeach

                                                    </select>

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>Kabupaten</label>

                                                    <select class="form-control edit-regency-mustahik"
                                                        data-mustahik="{{ $mustahik->id }}">

                                                        <option value="{{ $mustahik->village?->district?->regency?->id }}"
                                                            selected>

                                                            {{ $mustahik->village?->district?->regency?->name ?? '-' }}

                                                        </option>

                                                    </select>

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>Kecamatan</label>

                                                    <select class="form-control edit-district-mustahik"
                                                        data-mustahik="{{ $mustahik->id }}">

                                                        <option value="{{ $mustahik->village?->district?->id }}" selected>

                                                            {{ $mustahik->village?->district?->name ?? '-' }}

                                                        </option>

                                                    </select>

                                                </div>

                                                <div class="mb-3 col-md-6">

                                                    <label>Desa</label>

                                                    <select name="village_id" class="form-control edit-village-mustahik"
                                                        data-mustahik="{{ $mustahik->id }}">

                                                        <option value="{{ $mustahik->village?->id }}" selected>

                                                            {{ $mustahik->village?->name ?? '-' }}

                                                        </option>

                                                    </select>

                                                </div>

                                                {{-- <div class="mb-3 col-md-6">

                                                    <label>Jatah Beras (Gram)</label>

                                                    <input type="number" name="jatah_beras_gram" class="form-control"
                                                        value="{{ $mustahik->jatah_beras_gram }}">

                                                </div> --}}

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="submit" class="btn btn-primary">

                                                Simpan Perubahan

                                            </button>

                                        </div>

                                    </div>

                                </form>

                            </div>

                        </div>
                    @endforeach

    </div>
    <div class="modal fade" id="modalTambahMustahik" tabindex="-1">

        <div class="modal-dialog modal-lg">

            <form action="{{ route('mustahik.store') }}" method="POST">

                @csrf

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Tambah Mustahik
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="row">

                            {{-- Nama --}}
                            <div class="mb-3 col-md-6">

                                <label>Nama Mustahik</label>

                                <input type="text" name="nama" class="form-control" required>

                            </div>

                            {{-- Provinsi --}}
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

                            {{-- RFID --}}
                            <div class="mb-3 col-md-6">

                                <label>ID RFID</label>

                                <input type="text" name="rfid_uid" class="form-control" required>

                            </div>

                            {{-- Kabupaten --}}
                            <div class="mb-3 col-md-6">

                                <label>Kabupaten</label>

                                <select id="regency" class="form-control">

                                    <option value="">
                                        Pilih Kabupaten
                                    </option>

                                </select>

                            </div>

                            {{-- NIK --}}
                            <div class="mb-3 col-md-6">

                                <label>NIK</label>

                                <input type="text" name="nik" class="form-control" required>

                            </div>

                            {{-- Kecamatan --}}
                            <div class="mb-3 col-md-6">

                                <label>Kecamatan</label>

                                <select id="district" class="form-control">

                                    <option value="">
                                        Pilih Kecamatan
                                    </option>

                                </select>

                            </div>

                            {{-- No HP --}}
                            <div class="mb-3 col-md-6">

                                <label>No HP / WhatsApp</label>

                                <input type="text" name="no_hp" class="form-control">

                            </div>

                            {{-- Desa --}}
                            <div class="mb-3 col-md-6">

                                <label>Desa</label>

                                <select id="village" name="village_id" class="form-control">

                                    <option value="">
                                        Pilih Desa
                                    </option>

                                </select>

                            </div>

                            {{-- Alamat --}}
                            <div class="mb-3 col-md-12">

                                <label>Alamat Lengkap</label>

                                <textarea name="alamat" rows="3" class="form-control" required></textarea>

                            </div>

                            {{-- Jatah --}}
                            <div class="mb-3 col-md-12">

                                <label>Jatah Beras (Gram)</label>

                                <input type="number" name="jatah_beras_gram" class="form-control"
                                    placeholder="Contoh : 5000" required>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-primary">

                            Tambah Mustahik

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const setOptions = (select, data, placeholder) => {
                if (!select) return;

                select.innerHTML = '';
                select.appendChild(new Option(placeholder, ''));

                data.forEach(item => {
                    select.appendChild(new Option(item.name, item.id));
                });
            };

            const resetOptions = (select, placeholder) => {
                if (!select) return;

                select.innerHTML = '';
                select.appendChild(new Option(placeholder, ''));
            };

            const loadOptions = async (url, select, placeholder) => {
                try {
                    const response = await fetch(url, {
                        headers: {
                            Accept: 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Gagal memuat data');
                    }

                    const data = await response.json();
                    setOptions(select, data, placeholder);
                } catch (error) {
                    resetOptions(select, 'Data gagal dimuat');
                    console.error(error);
                }
            };

            const province = document.getElementById('province');
            const regency = document.getElementById('regency');
            const district = document.getElementById('district');
            const village = document.getElementById('village');

            province?.addEventListener('change', function() {
                resetOptions(regency, 'Pilih Kabupaten');
                resetOptions(district, 'Pilih Kecamatan');
                resetOptions(village, 'Pilih Desa');

                if (!this.value) return;

                loadOptions(`/regencies/${this.value}`, regency, 'Pilih Kabupaten');
            });

            regency?.addEventListener('change', function() {
                resetOptions(district, 'Pilih Kecamatan');
                resetOptions(village, 'Pilih Desa');

                if (!this.value) return;

                loadOptions(`/districts/${this.value}`, district, 'Pilih Kecamatan');
            });

            district?.addEventListener('change', function() {
                resetOptions(village, 'Pilih Desa');

                if (!this.value) return;

                loadOptions(`/villages/${this.value}`, village, 'Pilih Desa');
            });

            document.querySelectorAll('.edit-province-mustahik').forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.mustahik;
                    const regencySelect = document.querySelector(`.edit-regency-mustahik[data-mustahik="${id}"]`);
                    const districtSelect = document.querySelector(`.edit-district-mustahik[data-mustahik="${id}"]`);
                    const villageSelect = document.querySelector(`.edit-village-mustahik[data-mustahik="${id}"]`);

                    resetOptions(regencySelect, 'Pilih Kabupaten');
                    resetOptions(districtSelect, 'Pilih Kecamatan');
                    resetOptions(villageSelect, 'Pilih Desa');

                    if (!this.value) return;

                    loadOptions(`/regencies/${this.value}`, regencySelect, 'Pilih Kabupaten');
                });
            });

            document.querySelectorAll('.edit-regency-mustahik').forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.mustahik;
                    const districtSelect = document.querySelector(`.edit-district-mustahik[data-mustahik="${id}"]`);
                    const villageSelect = document.querySelector(`.edit-village-mustahik[data-mustahik="${id}"]`);

                    resetOptions(districtSelect, 'Pilih Kecamatan');
                    resetOptions(villageSelect, 'Pilih Desa');

                    if (!this.value) return;

                    loadOptions(`/districts/${this.value}`, districtSelect, 'Pilih Kecamatan');
                });
            });

            document.querySelectorAll('.edit-district-mustahik').forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.mustahik;
                    const villageSelect = document.querySelector(`.edit-village-mustahik[data-mustahik="${id}"]`);

                    resetOptions(villageSelect, 'Pilih Desa');

                    if (!this.value) return;

                    loadOptions(`/villages/${this.value}`, villageSelect, 'Pilih Desa');
                });
            });

            document.getElementById('searchMustahik')?.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();

                document.querySelectorAll('.mesin-table tbody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
                });
            });

            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (typeof Swal === 'undefined') {
                        if (confirm('Hapus Mustahik? Data yang dihapus tidak dapat dikembalikan.')) {
                            form.submit();
                        }

                        return;
                    }

                    Swal.fire({
                        title: 'Hapus Mustahik?',
                        text: 'Data yang dihapus tidak dapat dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
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
        });
    </script>
@endsection
