<?php

namespace App\Http\Controllers;

use App\Models\Mustahik;
use App\Models\Province;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MustahikController extends Controller
{
    public function active()
    {
        return $this->renderIndex(true);
    }

    public function index()
    {
        return $this->renderIndex(false);
    }

    private function renderIndex(bool $activeOnly)
    {
        $mustahiks = Mustahik::with([
            'village.district.regency.province',
            'transactions.machine',
        ]);

        if ($activeOnly) {
            $mustahiks->where('status', 'aktif');
        }

        $mustahiks = $mustahiks
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $transactions = Transaction::with([
            'mustahik',
            'machine',
        ])
            ->latest('tanggal_pengambilan')
            ->get();

        $provinces = Province::all();

        $mustahikStats = [
            'total' => Mustahik::count(),
            'aktif' => Mustahik::where('status', 'aktif')->count(),
            'nonaktif' => Mustahik::where('status', 'nonaktif')->count(),
        ];

        $pageTitle = $activeOnly
            ? 'Daftar Mustahik Aktif'
            : 'Daftar Mustahik';

        return view(
            'admin.mustahik.index',
            compact(
                'mustahiks',
                'provinces',
                'transactions',
                'mustahikStats',
                'activeOnly',
                'pageTitle'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'rfid_uid' => 'required|unique:mustahiks',
            'nik' => 'required|unique:mustahiks',
            'no_hp' => 'nullable',
            'alamat' => 'required',
            'village_id' => 'required',
            'jatah_beras_gram' => 'required|integer|min:1',
        ]);

        Mustahik::create([
            'nama' => $request->nama,
            'rfid_uid' => $request->rfid_uid,
            'nik' => $request->nik,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'village_id' => $request->village_id,
            'jatah_beras_gram' => $request->jatah_beras_gram,
            'status' => 'aktif',
        ]);

        return back()->with(
            'success',
            'Mustahik berhasil ditambahkan'
        );
    }

    public function update(
        Request $request,
        Mustahik $mustahik
    ) {
        $request->validate([

            'nama' => 'required',

            'rfid_uid' => 'required|unique:mustahiks,rfid_uid,'.
                $mustahik->id,

            'nik' => 'required',

            'no_hp' => 'required',

            'alamat' => 'required',

            'village_id' => 'required',

            // 'jatah_beras_gram' => 'required'
        ]);

        $mustahik->update(

            $request->only([
                'nama',
                'rfid_uid',
                'nik',
                'no_hp',
                'alamat',
                'village_id',
                // 'jatah_beras_gram'
            ])

        );

        return back()->with(
            'success',
            'Data mustahik berhasil diperbarui'
        );
    }

    public function destroy(Mustahik $mustahik)
    {
        $mustahik->delete();

        return back()->with(
            'success',
            'Data mustahik berhasil dihapus'
        );
    }

    public function updateStatus(
        Request $request,
        Mustahik $mustahik
    ) {
        $request->validate([
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $mustahik->update([
            'status' => $request->status,
        ]);

        $message = $request->status === 'aktif'
            ? 'Mustahik berhasil diaktifkan'
            : 'Mustahik berhasil dinonaktifkan';

        return back()->with(
            'success',
            $message
        );
    }

    public function tambahJatah(
        Request $request,
        Mustahik $mustahik
    ) {

        $request->validate([
            'jumlah' => 'required|numeric|min:1',
        ]);

        $mustahik->increment(
            'jatah_beras_gram',
            $request->jumlah
        );

        return back()->with(
            'success',
            'Jatah berhasil ditambahkan'
        );
    }

    public function downloadRiwayat(
        Mustahik $mustahik,
        Request $request
    ) {
        $filter = $request->filter;

        $transactions =
            $mustahik->transactions()
                ->with('machine');

        if ($filter == 7) {

            $transactions->where(
                'tanggal_pengambilan',
                '>=',
                Carbon::now()->subDays(7)
            );
        } elseif ($filter == 30) {

            $transactions->where(
                'tanggal_pengambilan',
                '>=',
                Carbon::now()->subDays(30)
            );
        } elseif ($filter == 180) {

            $transactions->where(
                'tanggal_pengambilan',
                '>=',
                Carbon::now()->subDays(180)
            );
        }

        $transactions =
            $transactions
                ->latest(
                    'tanggal_pengambilan'
                )
                ->get();

        $pdf = Pdf::loadView(
            'admin.mustahik.pdf-riwayat',
            compact(
                'mustahik',
                'transactions',
                'filter'
            )
        );

        return $pdf->download(
            'Riwayat_'.
                $mustahik->nama.
                '.pdf'
        );
    }

    public function downloadLaporanPengambilan(
        Request $request
    ) {

        $tanggalAwal =
            $request->tanggal_awal;

        $tanggalAkhir =
            $request->tanggal_akhir;

        $transactions =
            Transaction::with([
                'mustahik',
                'machine',
            ]);

        if ($tanggalAwal) {

            $transactions->whereDate(
                'tanggal_pengambilan',
                '>=',
                $tanggalAwal
            );
        }

        if ($tanggalAkhir) {

            $transactions->whereDate(
                'tanggal_pengambilan',
                '<=',
                $tanggalAkhir
            );
        }

        $transactions =
            $transactions
                ->latest(
                    'tanggal_pengambilan'
                )
                ->get();

        $totalTransaksi =
            $transactions->count();

        $totalBeras =
            $transactions->sum(
                'jumlah_ambil_gram'
            );

        $pdf = Pdf::loadView(
            'admin.mustahik.pdf-laporan-pengambilan',
            compact(
                'transactions',
                'tanggalAwal',
                'tanggalAkhir',
                'totalTransaksi',
                'totalBeras'
            )
        );

        return $pdf->download(
            'Laporan_Pengambilan.pdf'
        );
    }
}
