# Sistem Point of Sale (PoS) Lokale Select 

Untuk Memenuhi Tugas Ujian Akhir Semester Manajemen Basis Data Kelas A

Program Studi Informatika – Fakultas Teknik – Universitas Tanjungpura

## 👥 Anggota Kelompok 2:

* **Nelson Saputra** (D1041241003)
* **Bintang Andhara Putra** (D1041241051)
* **Gwenna Jasmine Farani** (D1041241079)

---

## Deskripsi Sistem

Sistem Point of Sale (PoS) Lokale Select adalah platform aplikasi kasir berbasis web yang mengotomatisasi pencatatan penjualan dan manajemen pangkalan data. Sistem ini dibangun dengan arsitektur **PHP Data Objects (PDO)** untuk menjamin keamanan dari *SQL Injection* dan menggunakan desain pangkalan data relasional (MySQL).

Sistem ini mendukung 3 *Role* akses (*Multi-user*):

1. **Kasir**: Menangani proses transaksi, mendeteksi keranjang belanja cerdas (*Smart Cart* untuk menghindari duplikasi struk), menghitung pajak otomatis, poin member, dan mencetak struk.
2. **Admin**: Mengelola pangkalan data menu (Tambah, Edit, Hapus) dengan fitur **Soft Delete**, memastikan menu yang dihapus tidak akan merusak integritas riwayat transaksi lama.
3. **Manajer**: Memantau rekapitulasi penjualan keseluruhan, pendapatan akhir, dan mencetak ulang struk lama.

---

## 📂 Struktur Folder

```text
project_php_kelompok2/
├── config/
│   ├── database.php
│   └── users.json
├── process/
│   ├── delete.php
│   ├── insert.php
│   ├── proses_transaksi.php
│   └── update.php
├── public/
│   ├── img/
│   │   └── logo-lokale.png
│   ├── edit.php
│   ├── hapus.php
│   ├── index.php
│   ├── kelola_menu.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── riwayat_transaksi.php
│   ├── struk.php
│   ├── tambah.php
│   └── transaksi.php
└── README.md

```

---

##  Instruksi Instalasi (XAMPP)

### Langkah 1 – Instal dan Jalankan XAMPP

1. Buka aplikasi **XAMPP Control Panel**.
2. Klik tombol **Start** pada modul **Apache** dan **MySQL**.

### Langkah 2 – Menyiapkan Folder Project

1. Ekstrak file zip project ini.
2. *Copy* folder hasil ekstrak (misal: `project_php_kelompok2`).
3. *Paste* folder tersebut ke dalam direktori XAMPP Anda: `C:\xampp\htdocs\`.

### Langkah 3 - Membuat Database MySQL

1. Buka browser dan akses phpMyAdmin: `http://localhost/phpmyadmin`
2. Klik menu **New**, lalu buat database baru dengan nama **`lokale`**.

### Langkah 4 - Import Database

1. Pilih database `lokale` yang baru saja dibuat.
2. Klik *tab* **Import**, klik **Choose File**, lalu pilih file database `.sql` yang ada di dalam folder project.
3. Klik tombol **Go** / **Import** di bagian bawah dan tunggu hingga proses selesai.

### Langkah 5 - Menghubungkan Database (Konfigurasi)

1. Buka folder project menggunakan VS Code atau teks editor lainnya.
2. Buka file konfigurasi di `config/database.php`.
3. Sesuaikan detail koneksi dengan pengaturan MySQL lokal Anda:
```php
$host = "localhost";      // Ubah port jika perlu (misal: localhost:3307)
$dbname = "lokale";
$username = "root";       // Default XAMPP adalah 'root'
$password = "";           // Kosongkan jika XAMPP default tidak ber-password

```


4. Simpan perubahan file (`Ctrl + S`).

### Langkah 6 - Menjalankan Project

1. Buka *browser* pilihan Anda.
2. Akses alamat URL berikut: `http://localhost/project_php_kelompok2/public/login.php`

---

## 🔑 Akses Login Default

Sistem membaca basis data kredensial *dummy* dari file `users.json` untuk *login*. Anda dapat langsung melakukan *login* dengan salah satu akun di bawah ini tanpa perlu registrasi ulang:

| Nama Pengguna | Username | Password | Role Akses | Keterangan Tugas |
| --- | --- | --- | --- | --- |
| **Nelson** | `Nelson123` | *123* | Kasir | Menjalankan proses transaksi dan pembayaran pelanggan. |
| **Gwenna** | `Gwen123` | *123* | Admin | Mengelola dan memperbarui daftar menu (Tambah/Edit/Hapus). |
| **Bintang** | `Bintang123` | *123* | Manajer | Memantau rekapitulasi dan riwayat transaksi penjualan. |

> *Catatan: Anda juga bisa membuat akun baru secara dinamis melalui halaman pendaftaran (`register.php`).*