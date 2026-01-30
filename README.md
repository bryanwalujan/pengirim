# E-Service App

Aplikasi layanan elektronik (e-service) untuk manajemen dokumen akademik berbasis web. Dibangun menggunakan Laravel 12 dengan fitur pengelolaan surat, penugasan komite, penetapan dosen pembimbing, dan penjadwalan seminar.

## 📋 Fitur Utama

### Manajemen Dokumen Akademik

- **Surat Keterangan (SK) Pembimbing** - Pengajuan dan penerbitan SK dosen pembimbing tugas akhir
- **Berita Acara Seminar Proposal** - Pengelolaan dokumen seminar proposal mahasiswa
- **Berita Acara Ujian Hasil** - Pengelolaan dokumen ujian hasil/sidang akhir
- **Dokumen Pendukung** - Upload dan verifikasi dokumen pendukung

### Sistem Tanda Tangan Digital

- Alur persetujuan bertingkat (Koordinator Prodi → Ketua Jurusan)
- QR Code untuk verifikasi keaslian dokumen
- Halaman verifikasi publik tanpa autentikasi

### Manajemen Pengguna & Hak Akses

- **Mahasiswa** - Mengajukan permohonan surat dan dokumen
- **Dosen** - Menyetujui dan menandatangani dokumen (Koordinator Prodi, Ketua Jurusan)
- **Staff** - Administrasi penuh dan pengelolaan sistem

### Fitur Tambahan

- Generasi nomor surat otomatis berdasarkan tahun ajaran
- Export data ke Excel
- Import data mahasiswa & pembayaran UKT
- Kalender akademik terintegrasi
- Notifikasi dan tracking status pengajuan

## 🛠️ Tech Stack

### Backend

- **PHP 8.2+**
- **Laravel 12** - Framework PHP
- **Spatie Permission** - Role & Permission Management
- **DomPDF** - Generasi dokumen PDF
- **Maatwebsite Excel** - Import/Export Excel
- **SimpleSoftwareIO QR Code** - Generasi QR Code

### Frontend

- **Tailwind CSS 3** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Vite 6** - Build tool & asset bundler
- **PDF.js & pdf-lib** - Manipulasi PDF client-side

### Database & Storage

- **MySQL/MariaDB** - Database relasional
- **Laravel Queue** - Background job processing
- **File Storage** - Penyimpanan dokumen privat

## 📦 Instalasi

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL/MariaDB

### Langkah Instalasi

1. **Clone repository**

    ```bash
    git clone https://github.com/patrickrompas20/project-skripsi
    cd eservice-app
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Setup environment**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Konfigurasi database**

    Edit file `.env` dan sesuaikan konfigurasi database:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=eservice_app
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5. **Migrasi & Seeding**

    ```bash
    php artisan migrate --seed
    ```

6. **Build assets**
    ```bash
    npm run build
    ```

## 🚀 Menjalankan Aplikasi

### Mode Development

```bash
# Jalankan semua service secara bersamaan (server, queue, vite)
composer dev

# Atau jalankan secara terpisah:
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Mode Production

```bash
npm run build
php artisan optimize
```

## 🧪 Testing

Aplikasi menggunakan **Pest PHP** untuk testing:

```bash
# Jalankan semua test
vendor/bin/pest

# Jalankan test spesifik
vendor/bin/pest --filter=NamaTest
```

## 📝 Code Quality

```bash
# Format kode dengan Laravel Pint (PSR-12)
./vendor/bin/pint
```

## 📁 Struktur Project

```
app/
├── Actions/           # Single-purpose business operations
│   ├── BeritaAcaraSempro/
│   ├── BeritaAcaraUjianHasil/
│   └── SkPembimbing/
├── Services/          # Complex domain logic
├── Models/            # Eloquent models
├── Http/
│   ├── Controllers/   # Route handlers
│   ├── Requests/      # Form validation
│   └── Middleware/    # HTTP middleware
├── Policies/          # Authorization policies
└── Traits/            # Reusable traits

resources/views/
├── admin/             # Views untuk staff/dosen
├── user/              # Views untuk mahasiswa
├── layouts/           # Layout templates
└── components/        # Blade components
```

## 🔐 Sistem Role & Permission

| Role        | Deskripsi                                               |
| ----------- | ------------------------------------------------------- |
| `mahasiswa` | Mahasiswa yang mengajukan permohonan                    |
| `dosen`     | Dosen dengan jabatan (Koordinator Prodi, Ketua Jurusan) |
| `staff`     | Administrator dengan akses penuh                        |

## 📄 Alur Dokumen

```
Draft → Menunggu TTD Korprodi → Menunggu TTD Kajur → Selesai
                    ↓                    ↓
                 Ditolak              Ditolak
```

## 🔧 Konfigurasi

- `config/surat.php` - Konfigurasi dokumen dan aturan pengajuan
- `config/dompdf.php` - Konfigurasi generasi PDF
- `config/permission.php` - Konfigurasi role & permission

## 📚 Dokumentasi Tambahan

- [Laravel Documentation](https://laravel.com/docs)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Tailwind CSS](https://tailwindcss.com/docs)

## 🤝 Kontribusi

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/fitur-baru`)
3. Commit perubahan (`git commit -m 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin feature/fitur-baru`)
5. Buat Pull Request

## 📜 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).
