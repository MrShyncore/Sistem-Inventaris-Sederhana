# 📦 Sistem Inventaris Barang (PHP + MySQL)

Aplikasi **Sistem Informasi Manajemen Data Sederhana** dengan studi kasus **Inventaris Barang**, dibangun menggunakan **PHP murni** (tanpa framework) dan **MySQL/MariaDB**, dengan antarmuka **CSS custom** yang responsif untuk desktop, tablet, dan mobile.

---

## 📑 Daftar Isi

- [Fitur](#-fitur)
- [Teknologi](#️-teknologi)
- [Struktur Folder](#-struktur-folder)
- [Cara Menjalankan](#-cara-menjalankan)
- [Alur Program](#-alur-program)
- [Pemenuhan Spesifikasi Tugas](#-pemenuhan-spesifikasi-tugas)
- [Responsive Design](#-responsive-design)
- [Catatan Keamanan](#️-catatan-keamanan)
- [Styling & Dual Bahasa](#-styling--dual-bahasa)
- [Screenshot](#-screenshot)
- [Verifikasi Cepat](#-verifikasi-cepat)
- [Lisensi & Identitas](#-lisensi--identitas)

---

## ✨ Fitur

- 🔐 **Otentikasi login** (session-based) dengan password di-hash menggunakan `password_hash()`
- 🌐 **Dual bahasa**: Bahasa Indonesia & English, tersimpan di `$_SESSION`
- 📋 **CRUD lengkap**: Create, Read, Update, Delete
- ✅ **Validasi form** dengan percabangan `if-else` dan `filter_var`
- 🛡️ **Sanitasi input & output** (`htmlspecialchars`, `filter_var`) dan **prepared statement** (anti SQL injection)
- 🔎 **Pencarian** berdasarkan kode/nama barang
- 📱 **Responsive design**: tabel otomatis berubah menjadi kartu (*card layout*) di perangkat mobile
- 🎨 **CSS custom** tanpa framework eksternal (*offline-ready*, tanpa CDN)

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|---|---|
| Bahasa Server | PHP 7.4+ / 8.x |
| Database | MySQL / MariaDB |
| Styling | CSS custom (tanpa framework) |
| Interaksi | JavaScript vanilla (minimal) |
| Aset Eksternal | Tidak ada dependensi |

---

## 📁 Struktur Folder

```text
inventaris-app/
├── .gitignore
├── README.md
├── database.sql          ← Skrip database + user admin default
├── assets/
│   ├── style.css         ← CSS custom & responsive
│   └── app.js            ← JS untuk tombol close alert
├── config/
│   ├── config.php        ← Bootstrap: session + lang + DB
│   ├── db.php            ← Koneksi MySQL
│   └── lang.php          ← Loader file bahasa
├── includes/
│   ├── auth.php          ← Auth guard (wajib login)
│   ├── header.php        ← Navbar + layout atas
│   └── footer.php        ← Layout bawah
├── lang/
│   ├── id.php            ← Kamus Bahasa Indonesia
│   └── en.php            ← Kamus Bahasa Inggris
├── docs/
│   ├── Laporan_Sistem_Inventaris.pdf
│   └── screenshots/
├── index.php             ← READ (daftar barang + pencarian)
├── create.php            ← CREATE + validasi
├── edit.php              ← UPDATE
├── delete.php            ← DELETE (POST-only)
├── login.php             ← Form login
├── logout.php            ← Hapus session & redirect
├── setup.php             ← Pembuatan admin (opsional)
└── switch_lang.php       ← Ganti bahasa ID/EN
```

---

## 🚀 Cara Menjalankan

### Prasyarat

- Web server (XAMPP / Laragon / MAMP / Apache)
- PHP 7.4 atau lebih baru
- MySQL / MariaDB

### Langkah-langkah

**1. Clone/unduh repo** ke folder web server Anda:

```bash
cd C:\xampp\htdocs
git clone https://github.com/USERNAME/inventaris-app.git
```

**2. Import database**

1. Buka `http://localhost/phpmyadmin`
2. Tab **Import** → pilih file `database.sql` → **Go**
3. Database `db_inventaris` akan otomatis dibuat beserta tabel dan data contoh

**3. Sesuaikan koneksi database** di `config/db.php`:

```php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';              // kosongkan untuk XAMPP/Laragon default
$DB_NAME = 'db_inventaris';
```

**4. Buka aplikasi:**

```text
http://localhost/inventaris-app/login.php
```

**5. Login** dengan kredensial default di bawah.

> **Catatan:** `setup.php` tidak wajib dibuka karena user admin sudah tersedia di `database.sql`. File ini hanya berguna jika tabel `users` kosong.

### Kredensial Default (DEMO)

| Field | Value |
|---|---|
| Username | `admin` |
| Password | `admin123` |

> ⚠️ **Peringatan Keamanan**
> Kredensial di atas hanya untuk keperluan demo akademik. Untuk penggunaan lanjut, **wajib mengganti password dan menghapus `setup.php`**.

### Cara Mengganti Password

1. Buat hash baru:

   ```bash
   php -r "echo password_hash('password_baru_anda', PASSWORD_DEFAULT);"
   ```

2. Salin hash 60 karakter yang dihasilkan, lalu update di database:

   ```sql
   UPDATE users SET password = 'HASH_BARU' WHERE username = 'admin';
   ```

---

## 🔄 Alur Program

### 1. Otentikasi (Login)

```text
User → form POST (username, password)
        ↓
Validasi kosong (if-else)
        ↓
SELECT user (prepared statement)
        ↓
password_verify(password, hash)
        ↓
session_regenerate_id(true)
        ↓
$_SESSION['user_id'] = id
$_SESSION['nama']    = nama
        ↓
redirect → index.php
```

### 2. Auth Guard

File `includes/auth.php` dipanggil di setiap halaman CRUD. Jika `$_SESSION['user_id']` tidak ada, user diarahkan ke `login.php` dengan pesan flash.

### 3. CRUD

| Operasi | File | Alur |
|---|---|---|
| Create | `create.php` | Form POST → sanitasi → validasi if-else → cek duplikasi kode → INSERT (prepared) |
| Read | `index.php` | SELECT (opsional pencarian `LIKE`) → tampil di tabel HTML |
| Update | `edit.php` | SELECT by id → form → validasi → UPDATE (prepared) |
| Delete | `delete.php` | Form POST → cek id → DELETE (prepared) → redirect |

### 4. Dual Bahasa

```text
User klik tombol ID/EN
        ↓
switch_lang.php?lang=id|en
        ↓
$_SESSION['lang'] = 'id' atau 'en'
        ↓
config/lang.php memuat lang/{lang}.php → $t
        ↓
Semua halaman membaca label dari $t['...']
```

Kategori barang juga diterjemahkan otomatis melalui helper `kategoriLabel()` (`elektronik` → "Elektronik" / "Electronics").

---

## ✅ Pemenuhan Spesifikasi Tugas

| Requirement | Implementasi |
|---|---|
| Form POST | `create.php`, `edit.php`, `login.php`, `delete.php` |
| Validasi if-else | Cek kosong, format regex kode, `filter_var` untuk angka, `in_array` kategori |
| Sanitasi `htmlspecialchars` | Semua output ditampilkan via `htmlspecialchars($...)` |
| Sanitasi `filter_var` | `FILTER_VALIDATE_INT` dan `FILTER_VALIDATE_FLOAT` untuk jumlah & harga |
| Koneksi MySQL | `config/db.php` via `mysqli` dengan `set_charset('utf8mb4')` |
| Create | `create.php` → INSERT prepared statement |
| Read | `index.php` → SELECT + tabel HTML |
| Update | `edit.php` → UPDATE prepared statement |
| Delete | `delete.php` → DELETE prepared statement |
| Login session | `login.php` + `includes/auth.php` |
| Dual bahasa di session | `switch_lang.php` + `$_SESSION['lang']` |
| Responsive design | Tiga breakpoint (lihat bagian berikut) |

---

## 📱 Responsive Design

Antarmuka menerapkan tiga breakpoint:

| Breakpoint | Lebar | Perilaku |
|---|---|---|
| Desktop | ≥ 992px | Tabel penuh 8 kolom, grid 2–3 kolom |
| Tablet | 768–991px | Tabel penuh, font sedikit lebih kecil |
| Mobile | ≤ 767px | Tabel → kartu, navbar vertikal, tombol 40px+ |

Pada perangkat mobile:

- Setiap baris tabel diubah menjadi *card* (dengan label di sisi kiri)
- Font input dinaikkan ke 16px untuk mencegah auto-zoom iOS
- Tombol diperbesar (min. 40px) agar mudah di-tap
- Tombol ID/EN tetap dapat diakses tanpa scroll horizontal

CSS responsive berada di `assets/style.css` pada blok `@media (max-width: 767px)`.

---

## 🛡️ Catatan Keamanan

- ✅ Password disimpan sebagai hash bcrypt (`$2y$10$...`), tidak pernah plain-text
- ✅ Verifikasi dengan `password_verify()`, bukan `==`
- ✅ Prepared statement di semua query yang menerima input user
- ✅ Sanitasi output dengan `htmlspecialchars()` mencegah XSS
- ✅ `session_regenerate_id(true)` saat login & logout mencegah *session fixation*
- ✅ Delete via POST (bukan GET) untuk mencegah CSRF sederhana
- ⚠️ Untuk produksi: tambahkan **CSRF token** dan **HTTPS**

---

## 🎨 Styling & Dual Bahasa

**Styling**

- CSS custom tanpa framework, tidak ada dependensi eksternal
- Warna didefinisikan via CSS Variables (`:root`) untuk memudahkan re-branding
- Ukuran file CSS ≈ 10 KB (jauh lebih kecil dari Bootstrap ≈ 200 KB)
- Aplikasi 100% *offline-ready* setelah import database

**Dual Bahasa**

- Semua label UI terpusat di `lang/id.php` dan `lang/en.php`
- Label kategori juga diterjemahkan via helper `kategoriLabel()`
- Preferensi bahasa persist di `$_SESSION['lang']` sehingga konsisten antar halaman

---

## 📸 Screenshot

Disimpan di `docs/screenshots/`:

| # | Nama File | Halaman |
|---|---|---|
| 1 | `1-login.png` | Form login |
| 2 | `2-daftar-barang.png` | Daftar barang (ID) |
| 3 | `3-tambah-barang.png` | Form tambah barang |
| 4 | `4-edit-barang.png` | Form edit barang |
| 5 | `5-validasi-error.png` | Alert validasi saat form kosong |
| 6 | `6-bahasa-inggris.png` | Daftar barang (EN) |
| 7 | `7-mobile-daftar.png` | Daftar barang (mobile view) |
| 8 | `8-mobile-daftar-en.png` | Daftar barang (mobile + EN) |

---

## 🧪 Verifikasi Cepat

Setelah import database & login, pastikan:

- [ ] Login `admin` / `admin123` berhasil
- [ ] Daftar barang menampilkan 5 data contoh
- [ ] Tambah barang baru → muncul di tabel
- [ ] Edit barang → data berubah
- [ ] Hapus barang → data hilang, muncul alert hijau
- [ ] Submit form kosong → muncul alert merah (validasi bekerja)
- [ ] Klik **EN** → semua label berubah bahasa
- [ ] Logout → diarahkan ke login, `index.php` tidak bisa diakses
- [ ] Buka di DevTools mobile → tabel berubah jadi kartu

### File Bantu Sementara

File-file berikut hanya alat bantu selama pengembangan dan **sebaiknya dihapus (atau dimasukkan ke `.gitignore`) sebelum deploy**:

```text
fix_admin.php
hash_gen.php
tes_login
ganti_password.php
```

---

## 📚 Lisensi & Identitas

Dibuat untuk memenuhi tugas Sistem Informasi Manajemen Data. Bebas digunakan untuk keperluan akademik.

| | |
|---|---|
| **Nama** | Handiansyah Pria Atmaja |
| **NIM** | 10124089 |
| **Mata Kuliah** | Penerapan Teknologi Internet |
| **Dosen Pengampu** | Donny Reza, M.Kom |
| **Tahun** | 2026 |
