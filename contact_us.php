<?php
include 'koneksi.php';

$nama = $_POST['nama']?? '';
$email = $_POST['email']?? '';
$pesan = $_POST['pesan']??'';

if (!$nama || !$email || !$pesan) {
    echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO contact_us (nama, email, pesan) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $email, $pesan);


if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesan.']);
}
?>