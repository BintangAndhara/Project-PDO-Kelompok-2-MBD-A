# Sistem Point of Sale Transaksi Kasir Lokale

Untuk Memenuhi Tugas Ujian Akhir Semester Manajemen Basis Data Kelas A<br>
Program Studi Informatika – Fakultas Teknik – Universitas Tanjungpura<br>

## Anggota Kelompok 2 :

Nelson Saputra (D1041241003)<br>
Bintang Andhara Putra (D1041241051)<br>
Gwenna Jasmine Farani (D1041241079)<br>

## Deskripsi Sistem

Sistem Point of Sale Transaksi Kasir Lokale adalah platform berbasis database yang menggantikan pencatatan manual. Sistem ini menyimpan data setiap transaksi (identitas kasir, menu, dan pesanan pelanggan), serta secara otomatis menghitung total belanjaan, pajak 10%, dan uang kembalian. Dengan demikian, rekapitulasi penjualan harian tersimpan aman dan meminimalkan risiko kesalahan perhitungan.<br>
Selain kasir, ada Admin yang mengelola berbagai jenis menu yang akan dijual. Adapun Manajer yang bertugas untuk memantau hasil transaksi yang sudah ada untuk memantau pendapatan.<br>

## Struktur Folder

```
project_php_kelompok2
├── Config
│ ├── database.php
│ └── users.json
├── Process
│ ├── delete.php
│ ├── insert.php
│ ├── proses_transaksi.php
│ └── update.php
├── Public
│ ├── edit.php
│ ├── hapus.php
│ ├── img
│ │ └── logo-lokale.png
│ ├── index.php
│ ├── kelola_menu.php
│ ├── login.php
│ ├── logout.php
│ ├── register.php
│ ├── riwayat_transaksi.php
│ ├── struk.php
│ ├── tambah.php
│ └── transaksi.php
└── README.md
```

## Instruksi Instalasi Menggunakan XAMPP

### Langkah 1 – Instal dan Jalankan XAMPP

1. Instal dan jalankan XAMPP
2. Pada XAMPP Control Panel, klik Start pada Apache dan MySQL

### Langkah 2 – Memasukkan Folder Project

1. Instal dan ekstrak file zip project ini
2. Copy folder project yang sudah diekstrak.
3. Paste folder hasil ekstrak file zip project ini ke dalam path berikut : C:\xampp\htdocs\

### Langkah 3 - Membuat Database

1. Buka browser dan akses phpMyAdmin : http://localhost/phpmyadmin
2. Klik New dan buat database baru

### Langkah 4 - Import Database

1. Pilih database yang telah dibuat
2. Pilih file database .sql yang ada pada folder project
3. Tunggu hingga proses selesai

### Langkah 5 - Menghubungkan Database

1. Buka folder project menggunakan VS Code atau text editor lainnya
2. Cari konfigurasi database : config.php
3. Sesuaikan database seperti :<br>$host = "localhost";<br>$user = "root";<br>$pass = "(sesuai password Anda)";<br>$db = "lokale";<br>
4. Simpan perubahan file

### Langkah 6 - Menjalankan Project

1. Buka browser dan akses alamat berikut : http://localhost/project_php_kelompok2/public/login.php

### Langkah 7 - Akses Login Default

Sistem ini sudah dilengkapi dengan beberapa data pengguna (berdasarkan `users.json` / database) untuk menguji fitur berdasarkan hak akses (*role*). Gunakan *username* berikut untuk masuk ke dalam sistem:

| Nama Pengguna | Username | Role Akses | Keterangan Tugas |
| --- | --- | --- | --- |
| **Nelson** | `Nelson123` | Kasir | Menjalankan proses transaksi dan pembayaran pelanggan. |
| **Gwenna** | `Gwen123` | Admin | Mengelola dan memperbarui daftar menu (Tambah/Edit/Hapus). |
| **Bintang** | `Bintang123` | Manajer | Memantau rekapitulasi dan riwayat transaksi penjualan. |
