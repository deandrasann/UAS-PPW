<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Bezhanov\Faker\Provider\Commerce;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TransaksiDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $faker->addProvider(new Commerce($faker));

        $transaksi = Transaksi::all();

        foreach ($transaksi as $t) {
            // Buat jumlah detail antara 5-15 menggunakan faker
            $numberOfDetails = $faker->numberBetween(5, 15);
            $total_harga = 0;

            for ($j = 0; $j < $numberOfDetails; $j++) {
                // Generate harga satuan dan jumlah produk
                $hargaSatuan = $faker->numberBetween(10, 500) * 100; // Harga satuan kelipatan 100
                $jumlah = $faker->numberBetween(1, 5); // Jumlah produk
                $subtotal = $hargaSatuan * $jumlah; // Subtotal
                $total_harga += $subtotal; // Tambahkan subtotal ke total harga

                // Buat data transaksi detail
                TransaksiDetail::create([
                    'id_transaksi' => $t->id,
                    'nama_produk' => $faker->productName,
                    'harga_satuan' => $hargaSatuan,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total harga di tabel transaksi
            $t->update([
                'total_harga' => $total_harga,
                'bayar' => ceil($total_harga / 50000) * 50000, // Pembulatan ke atas kelipatan 50.000
                'kembalian' => ceil($total_harga / 50000) * 50000 - $total_harga,
            ]);
        }
    }
}
