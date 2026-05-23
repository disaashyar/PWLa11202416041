<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row">
    <?php foreach ($products as $key => $item) : ?>         
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    
                    <img src="<?= base_url('img/' . $item['foto']) ?>" 
                         alt="<?= $item['nama'] ?>" 
                         class="img-fluid mb-3" 
                         style="max-height: 200px; object-fit: cover; border-radius: 8px;">
                    
                    <h5 class="card-title font-weight-bold mb-2"><?= $item['nama'] ?></h5>
                    
                    <p class="card-text text-success font-weight-bold">
                        Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                    </p>
                    
                </div>
            </div>
        </div> 
    <?php endforeach; ?> 
</div>

<?= $this->endSection() ?>