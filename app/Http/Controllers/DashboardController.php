<?php

namespace App\Http\Controllers;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $transaksi_count = Transaksi::count();  // Menghitung jumlah transaksi yang ada di tabel 'transaksi'

        // Menampilkan view dengan data transaksi_count
        return view('dashboard', compact('transaksi_count'));
    }
}
