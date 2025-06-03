<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama  = $_POST['nama'];
    $email = $_POST['email'];
    $pesan = $_POST['pesan'];

    mysqli_query($conn, "INSERT INTO kontak (nama, email, pesan) VALUES ('$nama', '$email', '$pesan')");
    echo "<script>alert('Pesan berhasil dikirim!'); window.location.href='index.php';</script>";
}
?>
