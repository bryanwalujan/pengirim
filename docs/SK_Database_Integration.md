# Dokumentasi Integrasi Data SK (Database Access)

Panduan ini ditujukan bagi developer eksternal yang akan melakukan fetching data terkait Nomor SK mahasiswa (SK Proposal, SK Pembimbing, dan SK Ujian Hasil) secara langsung melalui koneksi database (*Read-Only Access*).



## Relasi dan Penjelasan Tabel

1. **`users`**: Tabel utama yang menyimpan data mahasiswa (Nama & NIM).
2. **`pendaftaran_seminar_proposals`**: Menyimpan pengajuan pendaftaran seminar proposal mahasiswa. Berelasi *Many-to-One* kembali ke `users`.
3. **`jadwal_seminar_proposals`**: Menyimpan jadwal & Nomor SK dari Seminar Proposal. Memiliki relasi *One-to-One* ke tabel `pendaftaran_seminar_proposals`. Nomor SK-nya ditaruh pada kolom **`nomor_sk_proposal`**.
4. **`pendaftaran_ujian_hasils`**: Menyimpan pengajuan pendaftaran ujian hasil. Memiliki relasi *Many-to-One* ke `users`. **Nomor SK Pembimbing Skripsi** disimpan di tabel ini pada kolom **`nomor_sk_pembimbing`**.
5. **`jadwal_ujian_hasils`**: Menyimpan jadwal dan Nomor SK dari Ujian Hasil (Ujian Skripsi). Memiliki relasi *One-to-One* ke `pendaftaran_ujian_hasils`. Nomor SK-nya ditaruh pada kolom **`nomor_sk`**.

---

## Contoh Query Rekomendasi 

Berikut adalah query praktis (`Read-Only`/SELECT) yang bisa dimodifikasi atau digunakan langsung sesuai bahasa pemrograman lain (Golang, NodeJS, Python, dsb).

### 1. Fetching Nomor SK Proposal
Query ini mengambil judul skripsi dan nomor SK Proposal berdasarkan NIM.

```sql
SELECT 
    u.name, 
    u.nim, 
    psp.judul_skripsi,
    jsp.nomor_sk_proposal
FROM users u
JOIN pendaftaran_seminar_proposals psp ON u.id = psp.user_id
JOIN jadwal_seminar_proposals jsp ON psp.id = jsp.pendaftaran_seminar_proposal_id
WHERE u.nim = 'NIM_MAHASISWA' 
ORDER BY psp.created_at DESC 
LIMIT 1;
```

### 2. Fetching Nomor SK Pembimbing
Nomor SK Pembimbing dimasukkan pada saat mahasiswa mengajukan Pendaftaran Ujian Hasil, sehingga posisinya ada di tabel `pendaftaran_ujian_hasils`.

```sql
SELECT 
    u.name, 
    u.nim, 
    puh.judul_skripsi,
    puh.nomor_sk_pembimbing
FROM users u
JOIN pendaftaran_ujian_hasils puh ON u.id = puh.user_id
WHERE u.nim = 'NIM_MAHASISWA'
ORDER BY puh.created_at DESC
LIMIT 1;
```

### 3. Fetching Nomor SK Ujian Hasil (SK Ujian Skripsi)
Nomor SK Ujian Hasil dimasukkan ketika Admin/Sistem sudah membuat atau mengunggah data Jadwal Ujian Hasil.

```sql
SELECT 
    u.name, 
    u.nim,
    puh.judul_skripsi, 
    juh.nomor_sk AS nomor_sk_ujian_hasil
FROM users u
JOIN pendaftaran_ujian_hasils puh ON u.id = puh.user_id
JOIN jadwal_ujian_hasils juh ON puh.id = juh.pendaftaran_ujian_hasil_id
WHERE u.nim = 'NIM_MAHASISWA'
ORDER BY puh.created_at DESC
LIMIT 1;
```

### 4. Fetching Semua Nomor SK Sekaligus Lengkap
*Catatan: Query dibawah ini menggunakan `LEFT JOIN` agar apabila salah satu tahap (misalnya SK Ujian Hasil) belum diterbitkan, data mahasiswa dari tahap sebelumnya tetap dapat ditarik.*

```sql
SELECT 
    u.name, 
    u.nim, 
    jsp.nomor_sk_proposal,
    puh.nomor_sk_pembimbing,
    juh.nomor_sk AS nomor_sk_ujian_hasil
FROM users u
LEFT JOIN pendaftaran_seminar_proposals psp ON u.id = psp.user_id
LEFT JOIN jadwal_seminar_proposals jsp ON psp.id = jsp.pendaftaran_seminar_proposal_id

LEFT JOIN pendaftaran_ujian_hasils puh ON u.id = puh.user_id
LEFT JOIN jadwal_ujian_hasils juh ON puh.id = juh.pendaftaran_ujian_hasil_id

WHERE u.nim = 'NIM_MAHASISWA' 
AND (jsp.nomor_sk_proposal IS NOT NULL OR puh.nomor_sk_pembimbing IS NOT NULL)

-- Filter untuk memastikan kita mengambil data (record) pengajuan terbaru
ORDER BY puh.created_at DESC, psp.created_at DESC
LIMIT 1;
```

---

## Ketentuan Akses Database
* **Metode Interaksi:** Strictly `Read-Only` (SELECT only). Tidak diperbolehkan menggunakan metode `INSERT`, `UPDATE`, atau `DELETE`.
* **Keamanan:** Dimohon untuk menggunakan akun kredensial Database terpisah (misalnya User Role khusus _viewer_) jika tersedia, guna meminimalisir accidental transaction di server _production_.
