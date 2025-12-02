# 📝 Log Progress & Update Teknis (Hibban - Project Lead)

**Tanggal Update:** 2 Desember 2025  
**Fokus:** Inisiasi Project, Struktur Backend, & Database

---

## 1. 🚀 Status Terkini (Backend & System)

Saya telah melakukan inisiasi awal proyek untuk memastikan kita tidak mulai dari nol. Berikut adalah update krusial yang perlu diketahui tim:

### **A. Branch Baru & Workflow**
* **Status:** ✅ **DONE**
* **Keterangan:**
  Saya tidak langsung melakukan *push* ke branch `main` untuk menjaga kerapian kode utama. Saya telah membuat branch pengembangan baru (contoh: `dev-core` atau `feat/auth-setup`).
* **Action untuk Tim:**
  Silakan **checkout** ke branch tersebut untuk melihat struktur folder terbaru sebelum mulai mengerjakan bagian masing-masing.

### **B. Struktur Folder (MVC Native)**
* **Status:** ✅ **DONE**
* **Keterangan:**
  Saya sudah menyusun kerangka folder agar terorganisir dan tidak membingungkan. File-file inti sudah saya letakkan sesuai fungsinya:
  
  ``` bash
  /kelompok/kelompok_28/
  ├── config/       # Koneksi database ada di sini
  ├── assets/       # Tempat menyimpan CSS, JS, dan Gambar
  ├── includes/     # Header, Footer, Sidebar (potongan layout)
  ├── auth/         # Folder khusus logika Login/Logout
  ├── pages/        # Halaman utama aplikasi (Dashboard, POS, dll)
  ├── process/      # Logika pemrosesan data (CRUD)
  └── index.php     # Routing sederhana (sudah diset redirect ke login)
  ```

---

## 🛠️ 2. Update Database (MySQL)
Saya telah merancang skema database db_pos_sme dan menyiapkan file SQL-nya.
Tabel yang sudah disiapkan:
- users (id, username, password, role, created_at). 
- Role: owner, admin_gudang, kasir.  
- categories (id, name, description).  
- products (id, category_id, name, stock, price, image).  
- transactions (id, user_id, invoice_code, total_price, date).  
- transaction_details (id, transaction_id, product_id, qty, subtotal).
> ***Catatan: File database.sql akan saya letakkan di folder config/ atau sql/. Silakan import ke phpMyAdmin masing-masing.***

---

## 📋 3. Checklist Pengerjaan Saya (Next Steps)
Berikut adalah roadmap pengerjaan saya untuk sisi Backend dalam 1-2 hari ke depan:
* [x] Setup Repository & Branching: Membuat struktur dasar repo agar lolos CI check.  
* [x] Database Schema: Finalisasi relasi antar tabel (ERD).  
* [x] Koneksi Database: Membuat script config/database.php agar terkoneksi ke MySQL.  
* [x] Fitur Autentikasi: Mengerjakan backend untuk Login (cek role user) & Logout.  
* [x] Security Dasar: Menambahkan enkripsi password (MD5/Bcrypt) dan proteksi Session.

---

## 📢 4. Instruksi untuk Frontend (Syahrul & Reza)
Karena struktur folder dan backend dasar sudah saya siapkan di branch baru, kalian bisa mulai masuk untuk mengerjakan tampilan (UI):
Pull Branch Terbaru: Jangan lupa lakukan git pull dari branch yang saya buat.
1. Kerjakan di file pages/:  
2. Buat file dashboard.php di dalam folder pages/.   
3. Buat file login.php di dalam folder auth/.  
4. Abaikan Logika PHP dulu: Fokus saja membuat tampilan HTML/CSS-nya agar rapi. Nanti saya yang akan menyelipkan kode PHP untuk penarikan datanya.  
> ***Log ini dibuat untuk transparansi progres pengerjaan sisi Backend & Server. > ~ Hibban***
