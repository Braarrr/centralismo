<?php
include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    
    <link rel="stylesheet" href="fontawesome-free-5.15.4-web\css\fontawesome.min.css">
    <title>Document</title>
</head>
<body>
    <nav id="navbarScroll" class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="img/logo.png" class="rounded-circle " alt="logo" style="width: 40px; height: 40px;">Kantin Sekolah</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                <a class="nav-link" href="#about_kantin">About Kantin</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#cafetaria_list">Cafetaria List</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#buy">How to buy</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#contact">Contact Us</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <section id="about_kantin">
            <h1 style="color: black; text-align: center; margin-top: 70px;">Kantin SMK Telkom Jakarta</h1>
           <div class="container my-5">
                <div class="row">
                    <!-- Gambar -->
                    <div class="col-md-6">
                        <div class="ratio ratio-16x9">
                            <img src="img/kantin.jpg" class="img-fluid" alt="Foto Menu">
                        </div>
                    </div>

                    <!-- Video dari YouTube -->
                    <div class="col-md-6">
                        <div class="ratio ratio-16x9">
                            <iframe 
                            src="https://www.youtube.com/embed/NXy5xdwJaIY?si=03__jxkE16Z4vo8U" 
                            title="YouTube video player"
                            allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-justifyr">
                    Kantin SMK Telkom Jakarta adalah fasilitas yang paling diminati oleh siswa.
                    </p>
                </div>

            </div>

        </section>

        <?php
            $menus = mysqli_query($conn, "SELECT * FROM menu");
            $data_menu = [];
            while($row = mysqli_fetch_assoc($menus)) {
                $data_menu[$row['id_kantin']][] = $row;
            }
        ?>
        <section id="cafetaria_list">
        <h1 style="color: black; text-align: center; margin-top: 70px;">List Menu Kantin</h1>
        <div class="container my-4">
            <?php 
            $i = 0;
            foreach($data_menu as $nama_kantin => $daftar_menu): 
                // buka row setiap 2 kantin
                if ($i % 2 == 0) echo '<div class="row mb-5">';
            ?>
                <div class="col-md-6">
                    <h3 class="text-center">Kantin <?= $nama_kantin ?></h3>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <?php foreach($daftar_menu as $menu): ?>
                            <div class="card" style="width: 12rem;">
                                <img src="img/<?= strtolower(str_replace(' ', '', $menu['nama_menu'])) ?>.jpg" class="card-img-top" alt="<?= $menu['nama_menu'] ?>">
                                <div class="card-body">
                                    <p class="card-text"><?= $menu['nama_menu'] ?> - Rp<?= number_format($menu['harga'], 0, ',', '.') ?></p>
                                    <p class="card-text">Stok = <?= $menu['stok'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php 
                $i++;
                if ($i % 2 == 0) echo '</div>';
            endforeach;

            if ($i % 2 != 0) echo '</div>';
            ?>
        </div>

        </section>
        
        <section id="buy">
            <div class="container my-5">
                <h2 class="text-center mb-4">How to buy</h2>
                
                <form method="POST" action="proses_bayar.php" oninput="updateTotal()" id="formPembelian">
                    <div class="mb-3">
                        <label for="nama">Nama Pembeli:</label>
                        <input type="text" class="form-control" id="nama" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="menu">Pilih Menu:</label>
                                <select class="form-control" id="menu" onchange="updateHarga()">
                                    <option value="" disabled selected>Pilih Menu</option>
                                    <option value="1" data-harga="5000">Teh Manis - Rp5.000</option>
                                    <option value="2" data-harga="8000">Sosis Bakar - Rp8.000</option>
                                    <option value="3" data-harga="12000">Nasi Goreng - Rp12.000</option>
                                    <option value="4" data-harga="10000">Mie Ayam - Rp10.000</option>
                                    <option value="5" data-harga="15000">Ayam Geprek - Rp15.000</option>
                                    <option value="6" data-harga="15000">Tempura Curry - Rp15.000</option>
                                    <option value="7" data-harga="8000">Indomie - Rp8.000</option>
                                    <option value="8" data-harga="5000">Cimol - Rp5.000</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jumlah">Jumlah:</label>
                                <input type="number" class="form-control" id="jumlah" value="1" min="1" onchange="updateTotal()">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Total Harga:</label>
                        <input type="text" class="form-control" id="total" readonly value="Rp0">
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" onclick="tambahKeKeranjang()">Tambah ke Keranjang</button>
                    </div>

                    <div id="keranjang" class="mb-4">
                        <h5>Pesanan Kamu:</h5>
                        <ul id="listKeranjang" class="list-group"></ul>
                    </div>

                    <button type="button" class="btn btn-success" onclick="prosesPembayaran()">Bayar Sekarang</button>
                    <div class="text-center mt-4">
                        <h5>Scan QR ini buat bayar:</h5>
                        <img src="img/qr_dummy.png" alt="QR Dummy" style="max-width: 200px;">
                    </div>

                </form>
            </div>
        </section>

        <script>
        let keranjang = [];
        let currentHarga = 0;

        function updateHarga() {
            const menuSelect = document.getElementById('menu');
            const selectedOption = menuSelect.options[menuSelect.selectedIndex];
            currentHarga = selectedOption ? parseInt(selectedOption.getAttribute('data-harga')) : 0;
            updateTotal();
        }

        function updateTotal() {
            const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
            const total = currentHarga * jumlah;
            document.getElementById('total').value = "Rp" + total.toLocaleString('id-ID');
        }

                function tambahKeKeranjang() {
                    const menuSelect = document.getElementById('menu');
                    const selectedOption = menuSelect.options[menuSelect.selectedIndex];
                    const menuId = selectedOption.value;
                    const menuNama = selectedOption.textContent.split(" - ")[0];
                    const jumlah = parseInt(document.getElementById('jumlah').value);
                    const harga = parseInt(selectedOption.getAttribute('data-harga'));
                    const subtotal = harga * jumlah;

                    //push item
                    keranjang.push({
                        id: menuId,
                        nama: menuNama,
                        jumlah: jumlah,
                        harga: harga,
                        subtotal: subtotal
                    });

                    //update list 
                    const listKeranjang = document.getElementById('listKeranjang');
                    const item = document.createElement('li');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center';
                    item.textContent = `${menuNama} x ${jumlah} = Rp${subtotal.toLocaleString('id-ID')}`;
                    listKeranjang.appendChild(item);
                }

                function prosesPembayaran() {
                    if (keranjang.length === 0) {
                        alert("Keranjang masih kosong!");
                        return;
                    }

                    const namaPembeli = document.getElementById('nama').value.trim();
                    if (!namaPembeli) {
                        alert("Silakan isi nama pembeli!");
                        return;
                    }

                    //confirm bayar
                    if (!confirm('Apakah Anda yakin ingin memproses pembayaran?')) {
                        return;
                    }

                    const data = {
                        nama: namaPembeli,
                        items: keranjang.map(item => ({
                            id_menu: parseInt(item.id),
                            jumlah: parseInt(item.jumlah),
                            harga: parseInt(item.harga)
                        }))
                    };

                    //loading untuk proses
                    const btnBayar = document.querySelector('#formPembelian button[onclick="prosesPembayaran()"]');
                    const originalText = btnBayar.innerHTML;
                    btnBayar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
                    btnBayar.disabled = true;

                    fetch('proses_bayar.php', {
                        method: 'POST',
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(`Pembayaran berhasil!\nTotal: Rp${data.total.toLocaleString('id-ID')}`);
                            
                            //reset form
                            keranjang = [];
                            document.getElementById('listKeranjang').innerHTML = '';
                            document.getElementById('formPembelian').reset();
                            document.getElementById('total').value = "Rp0";
                            currentHarga = 0;
                            
                            //redirect ke invoice
                            window.location.href = `invoice.php?id=${data.id_transaksi || ''}`;
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan saat memproses pembayaran');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message);
                    })
                    .finally(() => {
                        btnBayar.innerHTML = originalText;
                        btnBayar.disabled = false;
                    });
                }
                </script>


        <section id="contact">
        <div class="container my-5">
            <h2 class="text-center mb-4">Contact us</h2>
            <form id="formContact">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama_contact" name="nama" placeholder="Masukkan nama" required>
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email_contact" name="email" placeholder="name@example.com" required>
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">kritik dan saran</label>
                    <textarea class="form-control" id="pesan_contact" name="pesan" rows="3" required></textarea>
                </div>

                <button type="button" class="btn btn-primary" onclick="kirimContact()">kirim</button>
            </form>
        </div>
        <script>
            function kirimContact() {
            const form = document.getElementById('formContact');
            const formData = new FormData(form);

            fetch('contact_us.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) form.reset();
            })
            .catch(() => alert('Gagal mengirim pesan.'));
        }
        </script>
        </section>

    <footer class="bg-dark text-white py-4">
      <div class="container text-center">
        <p>&copy; 2025 Kantin SMK Telkom Jakarta. Dibuat Lutfi Abrar Rahman</p>
      </div>
    </footer>

    </div>
</body>
</html>
