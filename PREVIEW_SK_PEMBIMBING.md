# Preview SK Pembimbing PDF

## Deskripsi

File ini berisi dokumentasi untuk fitur preview PDF SK Pembimbing Skripsi.

## URL Preview

Untuk melihat preview template PDF SK Pembimbing, akses URL berikut:

```
http://localhost/preview-sk-pembimbing-pdf
```

atau

```
http://eservice-app.test/preview-sk-pembimbing-pdf
```

## Fitur

-   Preview template PDF dengan data dummy
-   Menampilkan format surat lengkap dengan:
    -   Kop surat
    -   Nomor surat
    -   Data mahasiswa
    -   Data pembimbing 1 dan 2
    -   Tanda tangan Korprodi dan Kajur
    -   Footer verifikasi

## Data Dummy yang Digunakan

-   **Mahasiswa**: John Doe (NIM: 20210001)
-   **Pembimbing 1**: Dr. Jane Smith, M.Kom (NIP: 198501012010121001)
-   **Pembimbing 2**: Prof. Dr. Ahmad Rahman, M.T (NIP: 197801012005011001)
-   **Korprodi**: Dr. Maria Ulfa, M.Pd (NIP: 197501012000032001)
-   **Kajur**: Prof. Dr. Budi Santoso, M.Kom (NIP: 196801011995121001)
-   **Judul Skripsi**: Pengembangan Sistem Informasi E-Service Berbasis Web untuk Meningkatkan Efisiensi Layanan Akademik di Jurusan Teknologi Informasi dan Komunikasi
-   **Nomor Surat**: UN41.2/TI.01/0001/2026

## Catatan

-   Preview ini hanya untuk pengecekan template PDF
-   QR Code tidak ditampilkan pada preview (akan muncul watermark DRAFT)
-   Untuk mengakses, user harus sudah login (middleware: auth)
