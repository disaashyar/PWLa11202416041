<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
    <h1>Keranjang</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item active">Keranjang</li>
        </ol>
    </nav>
</div>

<?php
if (session()->getFlashData('success')) {
?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
}
?> 

<?php echo form_open('keranjang/edit') ?>

<div class="card">
    <div class="card-body pt-3">
        <h5 class="card-title">Keranjang</h5>
        
        <table class="table datatable">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Harga</th> 
                    <th scope="col">Jumlah</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1; 
                foreach ($items as $item) : 
                ?>
                    <tr>
                        <td><?php echo $item['name'] ?></td>
                        <td>
                            <img src="<?php echo base_url() . "img/" . $item['options']['foto'] ?>" width="100px">
                        </td>
                        <td><?= "IDR " . number_format($item['price'], 0, ',', ',') ?></td> 
                        
                        <td>
                            <input type="number" min="1" name="qty<?= $i++ ?>" class="form-control" value="<?php echo $item['qty'] ?>" style="width: 80px;">
                        </td>
                        
                        <td><?= "IDR " . number_format($item['subtotal'], 0, ',', ',') ?></td>
                        
                        <td>
                            <a href="<?php echo base_url('keranjang/delete/' . $item['rowid'] . '') ?>" class="btn btn-danger">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table> 

        <div class="alert alert-info">
            <?= "Total = IDR " . number_format($total, 0, ',', ',') ?>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Perbarui Keranjang</button>
            <a class="btn btn-warning" href="<?php echo base_url('keranjang/clear') ?>">Kosongkan Keranjang</a>
            <a class="btn btn-success" href="<?php echo base_url('checkout') ?>">Selesai Belanja</a>
        </div>
    </div>
</div>
 
<?php echo form_close() ?>
<?= $this->endSection() ?>