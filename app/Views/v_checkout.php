<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
    <h1>Checkout</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item active">Checkout</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body pt-3">
        <h5 class="card-title">Checkout</h5>
        
        <div class="row">
            
            <div class="col-lg-6">
                <?php echo form_open('buy') ?>
                    <input type="hidden" name="username" value="<?= session()->get('username') ?>">
                    <input type="hidden" name="total_transaksi" id="input-total-transaksi" value="<?= $total ?>">

                    <div class="mb-3">
                        <label class="form-label">Nama Pembeli</label>
                        <input type="text" class="form-control" name="nama" value="<?= session()->get('username') ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <input type="text" class="form-control" name="alamat" placeholder="Nama Jalan, No. Rumah, RT/RW..." required>
                    </div>

                    <div class="mb-3" style="position: relative;">
                        <label class="form-label">Kelurahan</label>
                        <input type="text" class="form-control" id="search-kelurahan" placeholder="Ketik nama kelurahan..." autocomplete="off" required>
                        <select class="form-select mt-1" id="select-kelurahan" style="display:none; position: absolute; z-index: 999; width: 100%;" size="5"></select>
                    </div>

                    <div class="mb-3" id="box-layanan" style="display:none;">
                        <label class="form-label">Pilihan Layanan</label>
                        <select class="form-select" id="select-layanan" name="layanan_ongkir"></select>
                    </div>

                    <div class="mb-3" id="box-ongkir" style="display:none;">
                        <label class="form-label">Biaya Ongkir</label>
                        <input type="text" class="form-control" id="input-ongkir" name="harga_ongkir" readonly>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mt-3">Buat Pesanan (Buy)</button>
                <?php echo form_close() ?>
            </div>

            <div class="col-lg-6">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($items)) :
                                foreach ($items as $index => $item) :
                            ?>
                                    <tr>
                                        <td><?= $item['name'] ?></td>
                                        <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                                        <td><?= $item['qty'] ?></td>
                                        <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                                    </tr>
                            <?php
                                endforeach;
                            endif;
                            ?>
                            <tr>
                                <td colspan="2"></td>
                                <td>Subtotal</td>
                                <td><?= number_to_currency($total, 'IDR') ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td>Total</td>
                                <td><span id="total"><?= number_to_currency($total, 'IDR') ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Fitur AJAX Pencarian Kelurahan
    $('#search-kelurahan').on('keyup', function() {
        let keyword = $(this).val();
        if (keyword.length >= 3) {
            $.ajax({
                url: '<?= base_url('checkout/searchDestination') ?>',
                type: 'GET',
                data: { search: keyword },
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        $('#select-kelurahan').empty().show();
                        response.data.forEach(function(dest) {
                            let text = `${dest.subdistrict_name.toUpperCase()}, ${dest.city_name.toUpperCase()}, ${dest.province_name.toUpperCase()}, ${dest.postal_code}`;
                            $('#select-kelurahan').append(`<option value="${dest.id}">${text}</option>`);
                        });
                    }
                }
            });
        } else {
            $('#select-kelurahan').hide();
        }
    });

    // 2. Aksi saat kelurahan dipilih
    $('#select-kelurahan').on('change', function() {
        let destId = $(this).val();
        let destText = $("#select-kelurahan option:selected").text();
        $('#search-kelurahan').val(destText);
        $('#select-kelurahan').hide();

        $.ajax({
            url: '<?= base_url('checkout/calculateCost') ?>',
            type: 'POST',
            data: { destination: destId },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    $('#select-layanan').empty();
                    $('#box-layanan').show();
                    
                    let jneCosts = response.data[0].costs;
                    jneCosts.forEach(function(cost) {
                        let textOption = `${response.data[0].name} ${cost.label} (${cost.service}) : estimasi ${cost.cost[0].etd} day`;
                        $('#select-layanan').append(`<option value="${cost.cost[0].value}">${textOption}</option>`);
                    });

                    $('#select-layanan').trigger('change');
                }
            }
        });
    });

    // 3. Merubah Nilai ID Total Spanduk Secara Realtime sesuai Ongkir
    $('#select-layanan').on('change', function() {
        let ongkirVal = parseInt($(this).val());
        $('#input-ongkir').val(ongkirVal);
        $('#box-ongkir').show();

        let subtotal = <?= $total ?>;
        let grandTotal = subtotal + ongkirVal;
        
        // Sinkronisasi ke input hidden untuk database
        $('#input-total-transaksi').val(grandTotal);
        
        // Tampilkan format currency Rp ke elemen span id="total"
        let formattedTotal = 'Rp ' + grandTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ',00';
        $('#total').text(formattedTotal);
    });
});
</script>

<?= $this->endSection() ?>