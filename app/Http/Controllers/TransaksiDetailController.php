<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class TransaksiDetailController extends Controller
{
    public function index()
    {
        // Retrieve transaksi details with the related transaksi, ordered by ID
        $transaksidetail = TransaksiDetail::with('transaksi')->orderBy('id', 'DESC')->get();

        return view('transaksidetail.index', compact('transaksidetail'));
    }

    public function detail(Request $request)
    {
        // Retrieve the transaksi by its ID, including its details
        $transaksi = Transaksi::with('transaksidetail')->findOrFail($request->id_transaksi);

        return view('transaksidetail.detail', compact('transaksi'));
    }

    public function edit($id)
    {
        // Retrieve the specific transaksi detail
        $transaksidetail = TransaksiDetail::findOrFail($id);

        return view('transaksidetail.edit', compact('transaksidetail'));
    }

    public function update(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'nama_produk' => 'required|string',
            'harga_satuan' => 'required|numeric',
            'jumlah' => 'required|numeric',
        ]);

        DB::beginTransaction(); // Start transaction to ensure atomicity

        try {
            $transaksidetail = TransaksiDetail::findOrFail($id);

            // Update the transaksi detail
            $transaksidetail->nama_produk = $request->input('nama_produk');
            $transaksidetail->harga_satuan = $request->input('harga_satuan');
            $transaksidetail->jumlah = $request->input('jumlah');
            $transaksidetail->subtotal = $transaksidetail->harga_satuan * $transaksidetail->jumlah; // Correct subtotal calculation
            $transaksidetail->save();

            // Recalculate the total price for the associated transaksi
            $transaksi = Transaksi::findOrFail($transaksidetail->id_transaksi);
            $transaksi->total_harga = $transaksi->transaksidetail->sum('subtotal'); // Recalculate total_harga
            $transaksi->kembalian = $transaksi->bayar - $transaksi->total_harga; // Recalculate kembalian
            $transaksi->save();

            DB::commit(); // Commit the transaction

            return redirect('transaksidetail/' . $transaksidetail->id_transaksi)->with('pesan', 'Berhasil mengubah data');
        } catch (\Exception $e) {
            DB::rollback(); // Rollback in case of error
            return redirect()->back()->withErrors(['Transaction' => 'Gagal mengubah data'])->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction(); // Start transaction to ensure atomicity

        try {
            // Find the transaksi detail and the associated transaksi
            $transaksidetail = TransaksiDetail::findOrFail($id);
            $transaksi = Transaksi::with('transaksidetail')->findOrFail($transaksidetail->id_transaksi);

            // Delete the transaksi detail
            $transaksidetail->delete();

            // Recalculate the total price for the associated transaksi
            $transaksi->total_harga = $transaksi->transaksidetail->sum('subtotal'); // Recalculate total_harga
            $transaksi->kembalian = $transaksi->bayar - $transaksi->total_harga; // Recalculate kembalian
            $transaksi->save();

            DB::commit(); // Commit the transaction

            return redirect('transaksidetail/' . $transaksi->id)->with('pesan', 'Berhasil menghapus data');
        } catch (\Exception $e) {
            DB::rollback(); // Rollback in case of error
            return redirect()->back()->withErrors(['Transaction' => 'Gagal menghapus data']);
        }
    }
}
