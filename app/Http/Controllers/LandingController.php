<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Mustahik;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index', [
            'mustahikResult' => null,
            'keyword' => null,
            'notFound' => false,
            'pickupMachine' => null,
            'recentTransactions' => collect(),
            'landingNews' => $this->getLandingNews(),
        ]);
    }

    public function checkRecipient(Request $request)
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'max:50'],
        ], [
            'keyword.required' => 'NIK atau UID RFID wajib diisi.',
        ]);

        $keyword = trim($validated['keyword']);

        $mustahik = Mustahik::with([
            'village.district.regency.province',
            'transactions.machine',
        ])
            ->where(function ($query) use ($keyword) {
                $query->where('nik', $keyword)
                    ->orWhere('rfid_uid', $keyword);
            })
            ->first();

        $pickupMachine = null;
        $recentTransactions = collect();

        if ($mustahik) {
            $now = now();

            $pickupMachine = Machine::with('village.district.regency.province')
                ->whereRaw('LOWER(TRIM(status_mesin)) = ?', ['aktif'])
                ->whereNotNull('jadwal_mulai')
                ->whereNotNull('jadwal_selesai')
                ->where('jadwal_mulai', '<=', $now)
                ->where('jadwal_selesai', '>=', $now)
                ->orderBy('jadwal_mulai', 'asc')
                ->first();

            $recentTransactions = $mustahik->transactions
                ->sortByDesc('tanggal_pengambilan')
                ->take(3);
        }

        return view('landing.index', [
            'mustahikResult' => $mustahik,
            'keyword' => $keyword,
            'notFound' => $mustahik === null,
            'pickupMachine' => $pickupMachine,
            'recentTransactions' => $recentTransactions,
            'landingNews' => $this->getLandingNews(),
        ]);
    }

    private function getLandingNews()
    {
        return News::where('status', 'publish')
            ->latest()
            ->take(6)
            ->get();
    }

    public function showNews($id)
    {
        $news = News::findOrFail($id);

        return view('landing.detail-berita', [
            'news' => $news,
        ]);
    }
}
