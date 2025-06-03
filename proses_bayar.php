<?php
include 'koneksi.php';
header('Content-Type: application/json');

// display error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// resuest method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Hanya POST yang diperbolehkan.'
    ]);
    exit;
}

// ambil data json
$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Format JSON tidak valid: ' . json_last_error_msg()
    ]);
    exit;
}

//cek data kosong apa ngga
if (empty($data['nama']) || empty($data['items']) || !is_array($data['items'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap. Nama pembeli dan items wajib diisi.'
    ]);
    exit;
}

$response = ['success' => false];
$conn->begin_transaction();

try {
    $waktu_transaksi = date('Y-m-d H:i:s');
    $total_harga = 0;
    $first_insert_id = null;

    //itung total harga
    foreach ($data['items'] as $item) {
        if (empty($item['id_menu']) || empty($item['jumlah']) || empty($item['harga'])) {
            throw new Exception('Data item tidak lengkap. Pastikan id_menu, jumlah, dan harga ada.');
        }

        // validasiin tipe data
        if (!is_numeric($item['id_menu']) || !is_numeric($item['jumlah']) || !is_numeric($item['harga'])) {
            throw new Exception('Data item harus berupa angka.');
        }

        $item['id_menu'] = (int)$item['id_menu'];
        $item['jumlah'] = (int)$item['jumlah'];
        $item['harga'] = (float)$item['harga'];

        // Validasi nilai positif
        if ($item['jumlah'] <= 0 || $item['harga'] <= 0) {
            throw new Exception('Jumlah dan harga harus lebih besar dari 0.');
        }

        $total_harga += $item['harga'] * $item['jumlah'];
    }

    // Proses setiap item
    foreach ($data['items'] as $item) {
        // 1. Cek stok tersedia
        $check_stock = $conn->prepare("SELECT stok, nama_menu FROM menu WHERE id_menu = ? FOR UPDATE");
        $check_stock->bind_param("i", $item['id_menu']);
        $check_stock->execute();
        $stock_result = $check_stock->get_result();
        
        if ($stock_result->num_rows === 0) {
            throw new Exception('Menu dengan ID ' . $item['id_menu'] . ' tidak ditemukan.');
        }

        $menu_data = $stock_result->fetch_assoc();
        if ($menu_data['stok'] < $item['jumlah']) {
            throw new Exception('Stok ' . $menu_data['nama_menu'] . ' tidak mencukupi. Stok tersedia: ' . $menu_data['stok']);
        }

        //simpan transaksi
        $subtotal = $item['harga'] * $item['jumlah'];
        $stmt = $conn->prepare("INSERT INTO detail_transaksi 
            (nama_pembeli, id_menu, jumlah, total_harga, created_at) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siids", 
            $data['nama'],
            $item['id_menu'],
            $item['jumlah'],
            $subtotal,
            $waktu_transaksi
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan transaksi: ' . $stmt->error);
        }

        if ($first_insert_id === null) {
            $first_insert_id = $conn->insert_id;
        }

        //update stok
        $update_stmt = $conn->prepare("UPDATE menu SET stok = stok - ? WHERE id_menu = ?");
        $update_stmt->bind_param("ii", $item['jumlah'], $item['id_menu']);
        
        if (!$update_stmt->execute()) {
            throw new Exception('Gagal update stok: ' . $update_stmt->error);
        }
    }

    $conn->commit();
    
    $response = [
        'success' => true,
        'id_transaksi' => $first_insert_id,
        'nama' => $data['nama'],
        'waktu' => $waktu_transaksi,
        'total' => $total_harga,
        'message' => 'Transaksi berhasil diproses'
    ];

} catch (Exception $e) {
    $conn->rollback();
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ];
}

echo json_encode($response);
?>