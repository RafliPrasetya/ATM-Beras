<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::with([
            'village.district.regency.province'
        ])->latest()->get();

        $provinces = Province::orderBy('name')->get();

        return view(
            'admin.mesin.index',
            compact(
                'machines',
                'provinces'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_code' => 'required|unique:machines',
            'village_id' => 'required',
            'lokasi_penempatan' => 'required',
        ]);

        Machine::create([
            'machine_code' => $request->machine_code,
            'village_id' => $request->village_id,
            'lokasi_penempatan' => $request->lokasi_penempatan,

            'status_mesin' => 'nonaktif',
            'stok_beras_kg' => 0,

            'jadwal_mulai' => null,
            'jadwal_selesai' => null,

            'status_penjadwalan' => 'nonaktif'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Mesin berhasil ditambahkan');
    }

    public function update(
        Request $request,
        Machine $machine
    ) {

        $request->validate([
            'machine_code' =>
            'required|unique:machines,machine_code,' . $machine->id,

            'village_id' => 'required',
            'lokasi_penempatan' => 'required',
            'stok_beras_kg' => 'required|integer|min:0',
        ]);

        $machine->update([
            'machine_code' => $request->machine_code,
            'village_id' => $request->village_id,
            'lokasi_penempatan' => $request->lokasi_penempatan,
            'stok_beras_kg' => $request->stok_beras_kg,
        ]);

        return back()->with(
            'success',
            'Mesin berhasil diperbarui'
        );
    }

    public function destroy(
        Machine $machine
    ) {

        $machine->delete();

        return back()
            ->with('success', 'Mesin berhasil dihapus');
    }
    public function getRegencies($provinceId)
    {
        return Regency::where(
            'province_id',
            $provinceId
        )->orderBy('name')->get();
    }

    public function getDistricts($regencyId)
    {
        return District::where(
            'regency_id',
            $regencyId
        )->orderBy('name')->get();
    }

    public function getVillages($districtId)
    {
        return Village::where(
            'district_id',
            $districtId
        )->orderBy('name')->get();
    }
    public function toggleStatus(Machine $machine)
    {
        $machine->update([
            'status_mesin' =>
            $machine->status_mesin === 'aktif'
                ? 'nonaktif'
                : 'aktif'
        ]);

        return back()->with(
            'success',
            'Status mesin berhasil diubah'
        );
    }

    public function updateJadwal(
        Request $request,
        Machine $machine
    ) {

        $request->validate([

            'jadwal_mulai' => 'required|date',

            'jadwal_selesai' =>
            'required|date|after:jadwal_mulai'

        ]);

        $machine->update([

            'jadwal_mulai' =>
            $request->jadwal_mulai,

            'jadwal_selesai' =>
            $request->jadwal_selesai,

        ]);

        return back()->with(
            'success',
            'Jadwal mesin berhasil diperbarui'
        );
    }
}
