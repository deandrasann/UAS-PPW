<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_detail';

    protected $fillable = [
        'id',
        'nama_produk',
        'harga_satuan',
        'jumlah',
        'subtotal',
        'soft_deletes'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class,'id_transaksi');
    }
}
