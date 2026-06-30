<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Mustahik;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMesin =
            Machine::count();

        $mesinAktif =
            Machine::where(
                'status_mesin',
                'aktif'
            )->count();

        $mesinNonAktif =
            Machine::where(
                'status_mesin',
                'nonaktif'
            )->count();

        $mesinMaintenance =
            Machine::where(
                'status_mesin',
                'maintenance'
            )->count();

        $totalMustahik =
            Mustahik::count();

        $totalTransaksi =
            Transaction::count();

        $totalBerasTersalurkan =
            Transaction::sum(
                'jumlah_ambil_gram'
            );

        $latestTransactions =
            Transaction::with([
                'mustahik',
                'machine'
            ])
            ->latest(
                'tanggal_pengambilan'
            )
            ->take(10)
            ->get();

        return view(
            'admin.dashboard',
            compact(
                'totalMesin',
                'mesinAktif',
                'mesinNonAktif',
                'mesinMaintenance',
                'totalMustahik',
                'totalTransaksi',
                'totalBerasTersalurkan',
                'latestTransactions'
            )
        );
    }
}
