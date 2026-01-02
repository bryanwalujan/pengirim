# Perbaikan Upload Surat Usulan Setelah Berita Acara Dihapus

## Masalah yang Diperbaiki

Sebelumnya, ketika staff menghapus data berita acara yang sudah selesai, mahasiswa tidak dapat melakukan upload ulang surat usulan (SK Proposal) karena:

1. **Status Jadwal Tidak Direset**: Ketika berita acara dihapus, status `jadwal_seminar_proposals` masih tetap `'selesai'`, sehingga sistem tidak mengizinkan perubahan
2. **File SK Masih Terdeteksi**: File SK Proposal yang lama masih ada di database dan mahasiswa tidak bisa menghapus atau mengupload yang baru
3. **Tombol Hapus Tidak Muncul**: UI hanya menampilkan tombol hapus jika status `'menunggu_jadwal'`, padahal setelah berita acara dihapus statusnya menjadi `'dijadwalkan'`

## Solusi yang Diterapkan

### 1. Auto-Reset Status Jadwal (BeritaAcaraSeminarProposal.php)

**File**: `app/Models/BeritaAcaraSeminarProposal.php`

Menambahkan logika di event `deleting()` untuk mereset status jadwal kembali ke `'dijadwalkan'` ketika berita acara dihapus:

```php
static::deleting(function ($model) {
    // Delete PDF file if exists
    if ($model->file_path && Storage::disk('public')->exists($model->file_path)) {
        Storage::disk('public')->delete($model->file_path);
    }

    // ✅ PERBAIKAN: Reset jadwal status when berita acara is deleted
    $jadwal = $model->jadwalSeminarProposal;

    if ($jadwal) {
        if ($jadwal->status === 'selesai') {
            $jadwal->update(['status' => 'dijadwalkan']);

            Log::info('✅ Auto-reset jadwal sempro status after berita acara deleted', [
                'jadwal_id' => $jadwal->id,
                'berita_acara_id' => $model->id,
                'old_status' => 'selesai',
                'new_status' => 'dijadwalkan',
            ]);
        }
    }
});
```

**Manfaat**:

-   Ketika staff menghapus berita acara, status jadwal otomatis kembali ke `'dijadwalkan'`
-   Memungkinkan mahasiswa untuk melakukan aksi selanjutnya (delete/upload SK baru)

### 2. Perluasan Logika Delete SK Proposal (JadwalSeminarProposalController.php)

**File**: `app/Http/Controllers/User/JadwalSeminarProposalController.php`

Mengubah method `deleteSkProposal()` untuk mengizinkan penghapusan SK dalam dua kondisi:

```php
public function deleteSkProposal(JadwalSeminarProposal $jadwal)
{
    // Validasi kepemilikan
    if ($jadwal->pendaftaranSeminarProposal->user_id !== Auth::id()) {
        abort(403, 'Anda tidak memiliki akses untuk menghapus file ini.');
    }

    // ✅ PERBAIKAN: Allow deletion if:
    // 1. Status is 'menunggu_jadwal' (original behavior)
    // 2. Status is 'dijadwalkan' BUT no berita acara exists (staff deleted it)
    $canDelete = $jadwal->status === 'menunggu_jadwal' ||
                 ($jadwal->status === 'dijadwalkan' && !$jadwal->hasBeritaAcara());

    if (!$canDelete) {
        return back()->with('error', 'SK Proposal tidak dapat dihapus karena sudah dijadwalkan atau sudah ada berita acara.');
    }

    // ... rest of the method
}
```

**Manfaat**:

-   Mahasiswa bisa hapus SK jika status `'menunggu_jadwal'` (behavior lama)
-   Mahasiswa juga bisa hapus SK jika status `'dijadwalkan'` TAPI tidak ada berita acara (berarti dihapus oleh staff)

### 3. Update UI untuk Menampilkan Tombol Hapus (index.blade.php)

**File**: `resources/views/user/jadwal-seminar-proposal/index.blade.php`

Mengubah kondisi penampilan tombol hapus SK:

```blade
@php
    // ✅ PERBAIKAN: Allow delete if status is 'menunggu_jadwal' OR 'dijadwalkan' without berita acara
    $canDelete = $jadwal->status === 'menunggu_jadwal' ||
                 ($jadwal->status === 'dijadwalkan' && !$jadwal->hasBeritaAcara());
@endphp

@if ($canDelete)
    <form action="{{ route('user.jadwal-seminar-proposal.delete-sk', $jadwal) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="...">
            <i class="bx bx-trash text-xl mr-2"></i>
            Hapus SK & Upload Ulang
        </button>
    </form>
@endif
```

**Manfaat**:

-   Tombol "Hapus SK & Upload Ulang" akan muncul ketika berita acara sudah dihapus oleh staff
-   Memberikan feedback visual yang jelas kepada mahasiswa

## Alur Kerja Setelah Perbaikan

1. **Staff menghapus Berita Acara yang sudah selesai**

    - Status jadwal otomatis berubah dari `'selesai'` → `'dijadwalkan'`
    - Database mencatat perubahan di log

2. **Mahasiswa membuka halaman Jadwal Seminar Proposal**

    - Sistem mendeteksi bahwa jadwal memiliki status `'dijadwalkan'`
    - Sistem mengecek bahwa tidak ada berita acara (`!$jadwal->hasBeritaAcara()`)
    - Tombol "Hapus SK & Upload Ulang" ditampilkan

3. **Mahasiswa menghapus SK lama**

    - File SK dihapus dari storage
    - Status jadwal berubah menjadi `'menunggu_sk'`
    - Database field `file_sk_proposal` menjadi `null`

4. **Mahasiswa upload SK baru**
    - File SK baru diupload
    - Status jadwal berubah menjadi `'menunggu_jadwal'`
    - Proses penjadwalan bisa dilakukan kembali oleh admin

## Testing yang Disarankan

### Skenario Test 1: Hapus Berita Acara yang Sudah Selesai

1. Buat jadwal sempro dengan status `'selesai'` dan berita acara yang sudah complete
2. Login sebagai staff dan hapus berita acara
3. Verifikasi bahwa status jadwal berubah menjadi `'dijadwalkan'`
4. Cek log untuk memastikan perubahan tercatat

### Skenario Test 2: Mahasiswa Hapus dan Upload Ulang SK

1. Setelah staff hapus berita acara (skenario 1)
2. Login sebagai mahasiswa
3. Buka halaman "Jadwal Seminar Proposal"
4. Verifikasi tombol "Hapus SK & Upload Ulang" muncul
5. Klik tombol hapus dan verifikasi SK terhapus
6. Upload SK baru dan verifikasi berhasil

### Skenario Test 3: Validasi Akses Control

1. Pastikan mahasiswa hanya bisa hapus SK milik sendiri
2. Pastikan tombol hapus tidak muncul jika masih ada berita acara
3. Pastikan tombol hapus tidak muncul jika status `'selesai'` dan masih ada berita acara

## File yang Diubah

1. ✅ `app/Models/BeritaAcaraSeminarProposal.php` - Auto-reset status jadwal
2. ✅ `app/Http/Controllers/User/JadwalSeminarProposalController.php` - Logika delete SK
3. ✅ `resources/views/user/jadwal-seminar-proposal/index.blade.php` - UI tombol hapus

## Catatan Penting

-   Perubahan ini **backward compatible** - tidak mengubah behavior yang sudah ada
-   Hanya menambahkan fitur baru untuk kasus edge case (berita acara dihapus oleh staff)
-   Log ditambahkan untuk audit trail yang lebih baik
-   Validasi akses tetap dipertahankan untuk keamanan
