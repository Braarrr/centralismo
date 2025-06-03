<?php
include 'koneksi.php';

// Validasi ID transaksi
$id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_transaksi <= 0) {
    die("ID transaksi tidak valid");
}

// Ambil data transaksi utama
$transaksi = $conn->query("SELECT * FROM detail_transaksi WHERE id = $id_transaksi")->fetch_assoc();
if (!$transaksi) {
    die("Transaksi tidak ditemukan");
}

// Ambil semua item transaksi
$items = $conn->query("
    SELECT m.nama_menu, d.jumlah, d.total_harga 
    FROM detail_transaksi d
    JOIN menu m ON d.id_menu = m.id_menu
    WHERE d.nama_pembeli = '".$conn->real_escape_string($transaksi['nama_pembeli'])."'
    AND ABS(TIMESTAMPDIFF(SECOND, d.created_at, '".$conn->real_escape_string($transaksi['created_at'])."')) < 60
    ORDER BY d.created_at DESC
");

// Hitung total
$total = 0;
while ($item = $items->fetch_assoc()) {
    $total += $item['total_harga'];
}
$items->data_seek(0); // Reset pointer result
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Invoice Pembelian</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Nama:</strong> <?= htmlspecialchars($transaksi['nama_pembeli']) ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($transaksi['created_at'])) ?></p>
                    <p><strong>No. Transaksi:</strong> <?= $id_transaksi ?></p>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Menu</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_menu']) ?></td>
                        <td>Rp<?= number_format($item['total_harga'] / $item['jumlah'], 0, ',', '.') ?></td>
                        <td><?= $item['jumlah'] ?></td>
                        <td>Rp<?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th>Rp<?= number_format($total, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>
