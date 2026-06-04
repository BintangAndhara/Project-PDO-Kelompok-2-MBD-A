CREATE DATABASE Lokale;
USE Lokale;

CREATE TABLE Member (
    Id_Member CHAR(5) NOT NULL PRIMARY KEY,
    Tgl_Daftar DATE NOT NULL,
    Nama_Member VARCHAR(100) NOT NULL,
    Nomor_HP VARCHAR(15) NOT NULL,
    Jumlah_Poin INT DEFAULT 0
);


CREATE TABLE Kasir (
    Id_Kasir CHAR(5) NOT NULL PRIMARY KEY,
    Nama_Kasir VARCHAR(50) NOT NULL,
    No_Telepon VARCHAR(15) NOT NULL
);


CREATE TABLE Menu (
    Id_Menu CHAR(5) NOT NULL PRIMARY KEY,
    Kategori VARCHAR(30) NOT NULL,
    Nama_Menu VARCHAR(100) NOT NULL,
    Harga DECIMAL(12,2) NOT NULL,
    Status_Aktif TINYINT(1) DEFAULT 1
);


CREATE TABLE Transaksi (
    Id_Transaksi CHAR(5) NOT NULL PRIMARY KEY,
    Waktu_Transaksi DATETIME NOT NULL,
    Id_Kasir CHAR(5) NOT NULL,   
    Tipe_Pesanan ENUM('Dine In', 'Takeaway') NOT NULL, 
    Id_Member CHAR(5),           
    Total_Harga DECIMAL(12,2) NOT NULL,
    Nilai_Pajak DECIMAL(12,2) NOT NULL,
    Total_Akhir DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (Id_Member) REFERENCES Member(Id_Member),
    FOREIGN KEY (Id_Kasir) REFERENCES Kasir(Id_Kasir)
);


CREATE TABLE Detail_Transaksi (
    Id_Detail CHAR(5) NOT NULL PRIMARY KEY, 
    Id_Transaksi CHAR(5) NOT NULL,          
    Id_Menu CHAR(5) NOT NULL,               
    Jumlah_Beli INT NOT NULL,
    Total_Harga_Item DECIMAL(12,2),
    FOREIGN KEY (Id_Transaksi) REFERENCES Transaksi(Id_Transaksi),
    FOREIGN KEY (Id_Menu) REFERENCES Menu(Id_Menu)
);


INSERT INTO Member (Id_Member, Tgl_Daftar, Nama_Member, Nomor_HP, Jumlah_Poin) VALUES
('M001', '2026-01-10', 'Zalikhah Khairunnisa', '081234500001', 120),
('M002', '2026-01-12', 'Syarif Fahridho', '081234500002', 95),
('M003', '2026-01-15', 'Nasywa Nayla', '081234500003', 110),
('M004', '2026-01-18', 'Nabila Indaswari', '081234500004', 90),
('M005', '2026-02-01', 'Muhammad Nasyid Asraf', '081234500005', 130),
('M006', '2026-02-05', 'Muhammad Taufik Kamil', '081234500006', 100),
('M007', '2026-02-10', 'Edwin Hermili', '081234500007', 85),
('M008', '2026-02-12', 'Zukovski Tangguh Dirgantara', '081234500008', 105),
('M009', '2026-02-14', 'Tangguh Pratama', '081234500009', 115),
('M010', '2026-02-16', 'Nelson Saputra', '081234500010', 90),
('M011', '2026-02-18', 'Fauzan Mulyana', '081234500011', 125),
('M012', '2026-02-20', 'Amru Daffa Khoirullah', '081234500012', 95),
('M013', '2026-02-22', 'Violaine Gunawan', '081234500013', 135),
('M014', '2026-02-24', 'Muhammad Aziiz Trinaldi', '081234500014', 110),
('M015', '2026-02-26', 'Juven Lawrinsen', '081234500015', 100);

INSERT INTO Kasir (Id_Kasir, Nama_Kasir, No_Telepon) VALUES
('K001', 'Samuel Bagus Prayogi', '081234510001'),
('K002', 'Dhyanesa Amirah K', '081234510002'),
('K003', 'Adjie Prasetya', '081234510003'),
('K004', 'Raihan Fadhillah', '081234510004'),
('K005', 'Kristian Candra Dinata', '081234510005'),
('K006', 'Rafli Gustiansyah', '081234510006'),
('K007', 'Fauzan Mulyana', '081234510007'),
('K008', 'Dwi Nayla Cintia', '081234510008');

INSERT INTO Menu (Id_Menu, Kategori, Nama_Menu, Harga) VALUES
('MN001', 'Coffee', 'Kopi Susu Lokale', 19000),
('MN002', 'Coffee', 'Kopi Susu Raya', 24000),
('MN003', 'Coffee', 'Americano', 20000),
('MN004', 'Coffee', 'Aren Latte', 25000),
('MN005', 'Coffee', 'Hazelnut Latte', 28000),
('MN006', 'Coffee', 'Butterscotch Latte', 28000),
('MN007', 'Coffee', 'Oat Latte', 31000),
('MN008', 'Main Dish', 'Teriyaki Beef', 38000),
('MN009', 'Main Dish', 'Mie Kuah Lokale', 22000),
('MN010', 'Main Dish', 'Smoked Beef Carbonara', 38000),
('MN011', 'Main Dish', 'Nasi Telor Pontianak', 22000),
('MN012', 'Main Dish', 'Nasi Goreng Nusantara', 27000),
('MN013', 'Main Dish', 'Soto Betawi', 45000),
('MN014', 'Main Dish', 'Salted Egg Chicken', 35000),
('MN015', 'Dessert', 'Milkbun', 32000),
('MN016', 'Dessert', 'Nuttela Delight', 19000),
('MN017', 'Dessert', 'Banana Raisin Cake', 27000),
('MN018', 'Dessert', 'Buttermilk Croissant', 33000),
('MN019', 'Dessert', 'Cinnamon Roll', 33000),
('MN020', 'Dessert', 'Matcha Mousse', 19000),
('MN021', 'Dessert', 'Lotus Biscoff', 19000);

INSERT INTO Transaksi (Id_Transaksi, Waktu_Transaksi, Id_Kasir, Tipe_Pesanan, Id_Member, Total_Harga, Nilai_Pajak, Total_Akhir) VALUES
('TR001', '2026-03-25 10:15:00', 'K001', 'Dine In',  'M002', 50000, 5000, 55000),   
('TR002', '2026-03-25 12:30:00', 'K002', 'Takeaway', 'M005', 90000, 9000, 99000),   
('TR003', '2026-03-25 14:00:00', 'K003', 'Dine In',  NULL,   38000, 3800, 41800),   
('TR004', '2026-03-26 16:45:00', 'K004', 'Takeaway', 'M010', 114000, 11400, 125400),
('TR005', '2026-03-26 18:20:00', 'K001', 'Dine In',  'M001', 40000, 4000, 44000),   
('TR006', '2026-03-27 09:10:00', 'K005', 'Takeaway', 'M012', 76000, 7600, 83600),   
('TR007', '2026-03-27 11:05:00', 'K006', 'Dine In',  NULL,   24000, 2400, 26400),  
('TR008', '2026-03-27 13:20:00', 'K007', 'Takeaway', 'M003', 70000, 7000, 77000),   
('TR009', '2026-03-27 14:30:00', 'K008', 'Dine In',  NULL,   108000, 10800, 118800),
('TR010', '2026-03-27 15:10:00', 'K002', 'Takeaway', 'M015', 160000, 16000, 176000);

INSERT INTO Detail_Transaksi (Id_Detail, Id_Transaksi, Id_Menu, Jumlah_Beli, Total_Harga_Item) VALUES
('DT001', 'TR001', 'MN004', 2, 50000),   
('DT002', 'TR002', 'MN013', 2, 90000),   
('DT003', 'TR003', 'MN008', 1, 38000),  
('DT004', 'TR004', 'MN010', 3, 114000), 
('DT005', 'TR005', 'MN003', 2, 40000),  
('DT006', 'TR006', 'MN008', 2, 76000),   
('DT007', 'TR007', 'MN002', 1, 24000),   
('DT008', 'TR008', 'MN014', 2, 70000),  
('DT009', 'TR009', 'MN012', 4, 108000),  
('DT010', 'TR010', 'MN015', 5, 160000);