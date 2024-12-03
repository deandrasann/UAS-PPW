@extends('layouts')
@section('content')
    <h2>Tambah Transaksi</h2>
    <div class="card">
        <div class="card-header bg-white">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-danger">Kembali</a>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action={{ route('transaksi.store') }}>
                @csrf
                <div class="d-flex flex-column gap-4 mb-4">
                    <div class="form-group">
                        <label>Tanggal Pembelian</label>
                        <input type="date" class="form-control" name="tanggal_pembelian" value="{{ old('tanggal_pembelian') }}" required>
                    </div>
                </div>
                <h6>Produk yang dibeli</h6>
                <div class="accordion mb-4" id="accordionItem">
                    @for ($i = 1; $i <= 3; $i++)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#item{{ $i }}" aria-expanded="true">
                                Item #{{ $i }}
                            </button>
                        </h2>
                        <div id="item{{ $i }}" class="accordion-collapse collapse show" data-bs-parent="#accordionItem">
                            <div class="accordion-body">
                                <div class="d-flex flex-column gap-4 mb-4">
                                    <div class="form-group">
                                        <label>Nama Produk</label>
                                        <input type="text" class="form-control" name="nama_produk{{ $i }}" value="{{ old('nama_produk'.$i) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Harga Satuan</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" name="harga_satuan{{ $i }}" value="{{ old('harga_satuan'.$i) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Jumlah</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" name="jumlah{{ $i }}" value="{{ old('jumlah'.$i) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Subtotal</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" name="subtotal{{ $i }}" value="{{ old('subtotal'.$i) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
                <div class="form-group">
                    <label>Harga Total</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" name="total_harga" value="{{ old('total_harga') }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label>Bayar</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" name="bayar" value="{{ old('bayar') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Kembalian</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" name="kembalian" value="{{ old('kembalian') }}" readonly>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

{{-- Custom JavaScript --}}
<script>
    $(document).ready(function() {
        function calculateSubtotal(item) {
            const hargaSatuan = parseInt($('input[name="harga_satuan'+item+'"]').val()) || 0;
            const jumlah = parseInt($('input[name="jumlah'+item+'"]').val()) || 0;
            const subtotal = hargaSatuan * jumlah;
            $('input[name="subtotal'+item+'"]').val(subtotal);
            updateTotalHarga();
        }

        function updateTotalHarga() {
            let totalHarga = 0;
            for (let i = 1; i <= 3; i++) {
                totalHarga += parseInt($('input[name="subtotal'+i+'"]').val()) || 0;
            }
            $('input[name="total_harga"]').val(totalHarga);
            updateKembalian();
        }

        function updateKembalian() {
            const totalHarga = parseInt($('input[name="total_harga"]').val()) || 0;
            const bayar = parseInt($('input[name="bayar"]').val()) || 0;
            const kembalian = bayar - totalHarga;
            $('input[name="kembalian"]').val(kembalian);
        }

        // Listen for changes in harga_satuan and jumlah inputs
        $('input[name^="harga_satuan"]').on('input', function() {
            const item = $(this).attr('name').replace('harga_satuan', '');
            calculateSubtotal(item);
        });

        $('input[name^="jumlah"]').on('input', function() {
            const item = $(this).attr('name').replace('jumlah', '');
            calculateSubtotal(item);
        });

        $('input[name="bayar"]').on('input', function() {
            updateKembalian();
        });
    });
</script>
@endsection
