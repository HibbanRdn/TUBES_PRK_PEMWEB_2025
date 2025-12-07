# 💻 Web-Based Point of Sales (POS) & Inventory Management

> **Tugas Besar Praktikum Pemrograman Web 2025**
>
> **Tema:** Digital Transformation for SMEs (No. 4)
> **Aplikasi:** DigiNiaga

Aplikasi ini adalah sistem kasir (Point of Sales) dan manajemen stok barang berbasis web yang dirancang untuk membantu UMKM dalam mencatat transaksi penjualan, mengelola inventaris, dan menghasilkan laporan keuangan secara digital, akurat, dan *real-time*.

---

## 👥 Anggota Kelompok 28

| No  | Nama Lengkap | NPM | Role |
| :--- | :--- | :--- | :--- |
| 1 | **M. Hibban Ramadhan** | **2315061094** | Fullstack / Project Lead |
| 2 | **Syahrul Ghufron Al Hamdan** | **2315061063** | Frontend |
| 3 | **M. Reza Rohman** | **2315061004** | Frontend |
| 4 | **Makka Muhammad Mustova** | **2315061100** | UI/Doc |

---

## 📖 Fitur & Fungsionalitas

### 1. Multi-Role Authentication
Sistem membedakan akses antara pemilik dan karyawan demi keamanan data.
* **Owner:** Akses penuh ke dashboard analitik, manajemen karyawan, pengaturan toko, dan laporan.
* **Admin Gudang:** (Coming Soon) Input stok masuk, manajemen kategori & data produk.
* **Kasir:** (Coming Soon) Input transaksi penjualan (POS) dengan kalkulasi otomatis.

### 2. Dashboard & Reporting (Owner)
* **Statistik Real-time:** Omzet harian, total transaksi, dan peringatan stok menipis.
* **Visualisasi Data:** Grafik tren penjualan dan kategori terlaris menggunakan **Chart.js**.
* **Cetak Laporan:** Ekspor laporan transaksi berdasarkan periode (7 hari/30 hari/Bulan ini) ke format **PDF** dengan kop surat otomatis (menggunakan **FPDF**).

### 3. Keamanan & Utilitas
* **Enkripsi Password:** Menggunakan `password_hash()` (Bcrypt).
* **Reset Password:** Fitur lupa password aman menggunakan token dan notifikasi Email (**PHPMailer**).
* **Setup Wizard:** Konfigurasi awal toko (Nama, Alamat, Telepon) saat pertama kali mendaftar.

---

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan teknologi Native sesuai ketentuan tugas besar:

* **Backend:** PHP Native (Procedural/Structured).
* **Frontend:** HTML5, **Tailwind CSS** (via CDN), JavaScript Native (AJAX & DOM).
* **Database:** MySQL.
* **Libraries (Third-Party):**
    * `FPDF` (PDF Generation).
    * `PHPMailer` (SMTP Email Service).
    * `Chart.js` (Data Visualization).

---

## 🌳 Struktur Folder

```bash
/kelompok_28
├── /assets                # File statis (Gambar/Logo)
├── /auth                  # Logika Autentikasi (Login, Register, Forgot Pass)
├── /config                # Konfigurasi Database & Email
│   ├── database.php
│   ├── send_email.php
│   └── smtp_secrets.php   # (Perlu dibuat manual dari example)
├── /library               # External Libraries (FPDF, PHPMailer)
├── /pages                 # Halaman Antarmuka (Views)
│   └── /owner             # Dashboard & Fitur Owner
├── /process               # Logika Pemrosesan Data (Backend Action)
└── index.php              # Landing Page / Redirector
```
---

## 🔀 Alur Fitur & Hak Akses (Role)

Untuk memenuhi syarat User Management, kita bagi hak aksesnya:  
**1. Owner:**
- Bisa akses semua halaman.  
- Fitur eksklusif: Melihat Laporan Penjualan (Grafik/Tabel total pendapatan) dan Manajemen User (Tambah/Hapus karyawan).

**2. Admin Gudang:** 
- Fokus pada halaman products.php.  
- Tugas: Tambah barang baru, edit harga, dan restock barang.

**3. Kasir:** 
- Fokus pada halaman pos.php.  
- Tugas: Input transaksi penjualan. Stok barang di database berkurang otomatis saat kasir menekan "Bayar".

--- 

## 🚀 Cara Instalasi & Menjalankan
1. Clone/Download repository ini.  
2. Masuk ke folder project: cd kelompok/kelompok_28.  
3. Database:  
    * Buat database baru di MySQL dengan nama db_pos_sme.  
    * Import file SQL (jika tersedia) atau sesuaikan tabel dengan skema yang dibutuhkan.
    * Sesuaikan konfigurasi di config/database.php.
4. Konfigurasi Email (Wajib untuk Fitur Reset Password):
    * Rename file config/smtp_secrets.example.php menjadi config/smtp_secrets.php.
    * Isi kredensial SMTP (Host, User, Password/App Password) di dalamnya.
5. Jalankan Server:  
    * Simpan folder di htdocs (XAMPP) atau www (Laragon).  
    * Buka browser dan akses: http://localhost/.../kelompok_28/.

> ⚠️ Catatan Pengembangan
>
> Pastikan koneksi internet aktif saat pengembangan karena Tailwind CSS dan Chart.js dimuat melalui CDN. Folder library/ berisi dependensi PHP yang tidak boleh dihapus.

---

### Perubahan yang dilakukan:
1.  **Update Tech Stack:** Menghapus "To be decided" pada CSS dan menegaskan penggunaan **Tailwind CSS**.
2.  **Penambahan Library:** Mencantumkan **FPDF**, **PHPMailer**, dan **Chart.js** yang ditemukan dalam kode.
3.  **Update Fitur:** Menambahkan detail tentang "Cetak Laporan PDF" dan "Reset Password via Email".
4.  **Instruksi Konfigurasi:** Menambahkan langkah penting untuk me-rename `smtp_secrets.example.php` agar fitur email berfungsi.
5.  **Struktur Folder:** Memperbarui pohon struktur agar sesuai dengan kondisi file saat ini (`/library`, `/pages/owner`).

> *Dibuat untuk memenuhi Tugas Besar Praktikum Pemrograman Web - Laboratorium Teknik Komputer Unila.*


