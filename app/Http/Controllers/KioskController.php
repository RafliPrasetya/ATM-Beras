<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KioskController extends Controller
{
    /**
     * Tampilkan halaman kiosk ATM Beras (fullscreen untuk Raspberry Pi).
     *
     * Route: GET /kiosk
     * Diakses oleh Chromium Kiosk Mode di Raspberry Pi.
     * Tidak memerlukan autentikasi admin.
     */
    public function index(Request $request)
    {
        return view('kiosk.index');
    }
}
