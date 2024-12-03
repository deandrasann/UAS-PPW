<?php

namespace App\Http\Controllers;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;

class DashboardController extends Controller
{
    public function index()
    {
        $transaksi_count = Transaksi::count();  // Menghitung jumlah transaksi yang ada di tabel 'transaksi'
        $omzet = Transaksi::sum('total_harga');
        $jumlah_item_terjual = Transaksi::count('id');

        // Menampilkan view dengan data transaksi_count
        return view('dashboard', compact('transaksi_count', 'omzet', 'jumlah_item_terjual'));
    }
}
