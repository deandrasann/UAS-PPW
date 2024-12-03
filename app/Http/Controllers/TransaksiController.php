<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        // Get all transactions ordered by tanggal_pembelian in descending order
        $transaksi = Transaksi::orderBy('tanggal_pembelian', 'DESC')->get();

        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        // Return the create view
        return view('transaksi.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'tanggal_pembelian' => 'required|date',
            'bayar' => 'required|numeric',
            'nama_produk1' => 'required|string',
            'harga_satuan1' => 'required|numeric',
            'jumlah1' => 'required|numeric',
            'nama_produk2' => 'required|string',
            'harga_satuan2' => 'required|numeric',
            'jumlah2' => 'required|numeric',
            'nama_produk3' => 'required|string',
            'harga_satuan3' => 'required|numeric',
            'jumlah3' => 'required|numeric',
        ]);

        // Start a database transaction
        DB::beginTransaction();
        try {
            // Create the transaksi record
            $transaksi = new Transaksi();
            $transaksi->tanggal_pembelian = $request->input('tanggal_pembelian');
            $transaksi->total_harga = 0;
            $transaksi->bayar = $request->input('bayar');
            $transaksi->kembalian = 0;
            $transaksi->save();

            // Initialize the total price
            $total_harga = 0;

            // Create the transaksi detail records
            for ($i = 1; $i <= 3; $i++) {
                $transaksiDetail = new TransaksiDetail();
                $transaksiDetail->id_transaksi = $transaksi->id;
                $transaksiDetail->nama_produk = $request->input('nama_produk' . $i);
                $transaksiDetail->harga_satuan = $request->input('harga_satuan' . $i);
                $transaksiDetail->jumlah = $request->input('jumlah' . $i);
                $transaksiDetail->subtotal = $transaksiDetail->harga_satuan * $transaksiDetail->jumlah;
                $total_harga += $transaksiDetail->subtotal;
                $transaksiDetail->save();
            }

            // Update the transaksi with the total price and calculate the change
            $transaksi->total_harga = $total_harga;
            $transaksi->kembalian = $transaksi->bayar - $total_harga;
            $transaksi->save();

            // Commit the transaction
            DB::commit();

            // Redirect to the transaksi detail page
            return redirect('transaksidetail/' . $transaksi->id)->with('pesan', 'Berhasil menambahkan data');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Return with error message
            return redirect()->back()->withErrors(['Transaction' => $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        // Find the transaksi record
        $transaksi = Transaksi::findOrFail($id);
        return view('transaksi.edit', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'bayar' => 'required|numeric'
        ]);

        // Find the transaksi record to update
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->bayar = $request->input('bayar');
        $transaksi->kembalian = $transaksi->bayar - $transaksi->total_harga;
        $transaksi->save();

        return redirect('/transaksi')->with('pesan', 'Berhasil mengubah data');
    }

    public function destroy($id)
    {
        // Find the transaksi record to delete
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect('/transaksi')->with('pesan', 'Data berhasil dihapus');
    }
}
