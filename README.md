# Sistem Database Online — PT Fathoni Factory Group

Aplikasi web **demo (prototipe fungsional)** untuk memindahkan data operasional yang selama ini dikelola
secara offline (file Excel/CSV di komputer lokal) ke database online. Perusahaan bergerak di bidang
pengumpulan dan perdagangan plastik daur ulang (PET, HDPE, LDPE, ABS).

> Proyek PKL. Seluruh data di dalamnya adalah data contoh, bukan data operasional sebenarnya.

## Teknologi

| Bagian | Pilihan |
| --- | --- |
| Bahasa & framework | PHP 8.3+, Laravel 13 |
| Database | PostgreSQL di Supabase (driver `pgsql`, `DB_SSLMODE=require`) |
| Tampilan | Blade + Bootstrap 5 (responsif desktop & ponsel), ikon Phosphor |
| Autentikasi | Laravel Breeze (Blade), peran **admin** dan **staf** lewat middleware |
| Excel & PDF | `maatwebsite/excel` 4, `barryvdh/laravel-dompdf` |
| Tes | Pest (PHPUnit) di atas PostgreSQL |

> Catatan: Laravel 13 membutuhkan **PHP 8.3 atau lebih baru**.

## Dokumen perancangan

Diagram PlantUML ada di folder [`docs/`](docs): use case, sitemap, tiga user flow, dan ERD.
Struktur tabel mengikuti `docs/06_erd.puml` dengan sedikit tambahan teknis:

- `pengguna.remember_token` agar fitur "Ingat saya" berfungsi.
- `setoran.updated_at` dan `penjualan.updated_at` karena transaksi dapat diubah.
- Sesi dan cache disimpan di file, sehingga database hanya berisi tabel ERD (+ tabel `migrations` milik Laravel).
- Row Level Security (RLS) diaktifkan pada semua tabel agar tidak bisa dibaca lewat API publik Supabase.
  Aplikasi Laravel tetap bisa mengakses karena terhubung sebagai pemilik tabel.

## Fitur & status pengerjaan

| Fase | Fitur | Status |
| --- | --- | --- |
| 1 | Login, hak akses admin/staf (admin mewarisi hak staf) | Selesai |
| 1 | Data master supplier & pembeli (staf & admin), jenis plastik & riwayat harga (admin) | Selesai |
| 1 | Impor data offline: unggah → cocokkan kolom → validasi → pratinjau → konfirmasi → `log_impor` | Selesai |
| 2 | Transaksi setoran & penjualan multi-jenis plastik, harga otomatis, pencarian & filter | Berikutnya |
| 3 | Dashboard, laporan + ekspor Excel/PDF, backup ZIP, pengguna, log aktivitas, nota timbang | Berikutnya |

### Hak akses

| Menu | Staf | Admin |
| --- | :-: | :-: |
| Dashboard, Supplier, Pembeli, Transaksi, Laporan, Profil | ✓ | ✓ |
| Jenis Plastik & Harga | | ✓ |
| Impor Data & Riwayat Impor | | ✓ |
| Pengaturan (pengguna, backup, log aktivitas) | | ✓ |

## Instalasi (lokal)

Prasyarat: PHP 8.3+ (ekstensi `pdo_pgsql`, `zip`, `gd`, `mbstring`, `xml`), Composer 2, Node.js 20+.

```bash
git clone https://github.com/Vonfring/offline-database-to-online-system.git
cd offline-database-to-online-system

composer install
npm install
npm run build          # membangun CSS/JS (Bootstrap, ikon, font)

cp .env.example .env
php artisan key:generate
```

Lalu isi koneksi database di `.env` (lihat bagian berikut), kemudian:

```bash
php artisan migrate --seed
php artisan serve      # buka http://localhost:8000
```

## Menghubungkan ke Supabase

1. Buka project di [supabase.com/dashboard](https://supabase.com/dashboard), klik tombol **Connect**.
2. Pilih tab **Session pooler** (mendukung IPv4, cocok untuk Laravel). Salin host, port, user, dan nama database.
3. Isi `.env`:

   ```dotenv
   DB_CONNECTION=pgsql
   DB_HOST=aws-0-ap-northeast-1.pooler.supabase.com   # salin persis dari dashboard
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres.<id-project>
   DB_PASSWORD=<kata sandi database>
   DB_SSLMODE=require
   ```

   Kata sandi database bisa diatur ulang di **Project Settings → Database** jika lupa.
   Jika memakai **Transaction pooler** (port 6543), tambahkan `DB_EMULATE_PREPARES=true`.
4. Uji koneksi dan buat tabel:

   ```bash
   php artisan migrate:status
   php artisan migrate --seed
   ```

> Jangan pernah meng-commit file `.env`. File ini sudah tercantum di `.gitignore`;
> yang disimpan di repository hanya `.env.example` tanpa kata sandi.

## Data contoh (seeder)

```bash
php artisan migrate:fresh --seed   # PERINGATAN: menghapus seluruh isi database lalu mengisi ulang
```

Seeder mengisi 3 akun, 15 supplier, 8 pembeli, dan 4 jenis plastik dengan tiga kali perubahan harga
(1 Jun, 1 Agu, dan 15 Sep 2026).

### Akun demo

| Peran | Email | Kata sandi |
| --- | --- | --- |
| Admin | `admin@fathoni.test` | `admin12345` |
| Staf | `staf@fathoni.test` | `staf12345` |
| Staf | `bagas@fathoni.test` | `staf12345` |

## Impor data offline (admin)

Menu **Impor Data** mengikuti `docs/03_userflow_migrasi.puml`:

1. Pilih jenis data (supplier, pembeli, setoran, atau penjualan) lalu unggah file `.xlsx`, `.xls`, atau `.csv`
   (maks. 5 MB / 5.000 baris). Template Excel tersedia di halaman yang sama.
2. **Cocokkan kolom**: kolom dengan nama mirip dipasangkan otomatis dan masih bisa diubah.
3. **Validasi & pratinjau**: format (angka, tanggal, kode), data kosong pada kolom wajib, dan data ganda
   (di dalam file maupun dengan database). Baris valid dan tidak valid ditampilkan terpisah, lengkap dengan
   nomor baris Excel dan alasannya. File perbaikan bisa diunggah ulang tanpa memetakan kolom lagi.
4. **Konfirmasi impor**: hanya baris valid yang disimpan (`sumber_data = impor` untuk transaksi), dan prosesnya
   dicatat di `log_impor` (jumlah baris, berhasil, gagal).

Aturan khusus transaksi:

- Satu baris = satu jenis plastik. Baris dengan nomor transaksi yang sama digabung menjadi satu transaksi.
- Harga/kg boleh dikosongkan; sistem memakai harga beli (setoran) atau harga jual (penjualan) yang berlaku
  pada tanggal transaksi.
- Jika satu baris dalam sebuah transaksi tidak valid, seluruh transaksi itu tidak diimpor agar totalnya tidak terpotong.
- Kode supplier/pembeli dan jenis plastik harus sudah terdaftar, jadi impor data master terlebih dahulu.

## Menjalankan tes

Tes memakai database PostgreSQL terpisah bernama `fathoni_test` (lihat `phpunit.xml`), dengan host dan
kredensial dari `.env`. Buat database ini di server PostgreSQL lokal. **Jangan arahkan tes ke Supabase**,
karena tes mengosongkan database.

```bash
createdb fathoni_test
php artisan test
```

## Struktur kode penting

```
app/Http/Middleware/CekPeran.php        # middleware peran:admin / peran:staf
app/Models/                             # model Eloquent sesuai ERD
app/Services/LayananTransaksi.php       # harga berlaku, subtotal & total transaksi
app/Services/Impor/                     # pembaca file, normalisasi, definisi & validasi impor per jenis data
app/Exports/TemplateImpor.php           # template Excel impor
database/migrations, database/seeders   # skema & data contoh
resources/views/                        # tampilan Blade (Bootstrap 5)
docs/                                   # diagram PlantUML
```
