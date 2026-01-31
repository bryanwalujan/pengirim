# E-Service App - AI Agent Instructions

Laravel 12 e-service for academic documents (SK Pembimbing, Berita Acara Sempro/Ujian Hasil). Three roles: **mahasiswa** (students), **dosen** (lecturers with optional jabatan), **staff** (admin). Uses Spatie Permission, DomPDF, QR codes for digital signatures.

## Architecture

**Action-Service-Controller Pattern**: Actions for single operations (`app/Actions/{Feature}/`), Services for complex logic (`app/Services/`), Controllers delegate only. See [app/Actions/SkPembimbing/SignByKorprodiAction.php](app/Actions/SkPembimbing/SignByKorprodiAction.php).

**Document Workflows**: Models use status constants with audit trails:

```php
const STATUS_DRAFT = 'draft';
const STATUS_MENUNGGU_TTD_KORPRODI = 'menunggu_ttd_korprodi';  // Awaiting Korprodi signature
const STATUS_MENUNGGU_TTD_KAJUR = 'menunggu_ttd_kajur';        // Awaiting Kajur signature
const STATUS_SELESAI = 'selesai';
```

Each transition tracked via `{action}_by`, `{action}_at` fields. Reference: [app/Models/PengajuanSkPembimbing.php](app/Models/PengajuanSkPembimbing.php).

**PDF + Digital Signatures**: Two-stage generation—draft PDF on creation, regenerated with QR codes after each signature. QR embeds verification URL (`/verify/{type}/{code}`). See [app/Services/SkPembimbing/SkPembimbingPdfService.php](app/Services/SkPembimbing/SkPembimbingPdfService.php).

**Letter Numbering**: `GeneratesNomorSurat` trait generates `UN41.2/TI.01/{seq}/{year}`, resets on semester "Genap". Config: [config/surat.php](config/surat.php).

## Developer Commands

```bash
composer dev              # Runs server + queue + vite concurrently
vendor/bin/pest           # Run Pest tests (uses RefreshDatabase)
./vendor/bin/pint         # Format code (Laravel Pint)
```

**Dev Quick Login** (local only): `POST /dev/login/{role}` where role is mahasiswa|dosen|staff.

## Key Conventions

- **Role checks**: Use `$user->hasRole('mahasiswa')`, `$user->isKoordinatorProdi()`, `$user->isKetuaJurusan()` (never `role == 'name'`)
- **UKT Middleware**: `CheckUktPayment` blocks unpaid mahasiswa from services
- **Cache keys**: `active_tahun_ajaran`, `surat_submission_check_{userId}` — clear after workflow changes via `SuratNotificationHelper::clearSuratCache()`
- **File paths**: `storage/app/{feature}/{YYYY/MM}/filename.pdf`
- **Views**: `resources/views/admin/` for staff/dosen, `resources/views/user/` for mahasiswa

## Critical Patterns

1. **Transactions for nomor surat**: Generate inside `DB::transaction()` with model save to prevent duplicates
2. **PDF regeneration required**: After each signature action, call PDF service to embed new QR code
3. **Route organization**: Grouped by role middleware in [routes/web.php](routes/web.php) — mahasiswa under `role:mahasiswa,check.ukt`, admin under `role:staff|dosen`

## Key Files

| Purpose                | File                                                                                                         |
| ---------------------- | ------------------------------------------------------------------------------------------------------------ |
| Workflow model example | [app/Models/PengajuanSkPembimbing.php](app/Models/PengajuanSkPembimbing.php)                                 |
| Action pattern         | [app/Actions/SkPembimbing/](app/Actions/SkPembimbing/)                                                       |
| PDF service            | [app/Services/SkPembimbing/SkPembimbingPdfService.php](app/Services/SkPembimbing/SkPembimbingPdfService.php) |
| Letter numbering       | [app/Traits/GeneratesNomorSurat.php](app/Traits/GeneratesNomorSurat.php)                                     |
| Surat config           | [config/surat.php](config/surat.php)                                                                         |
| Cache helper           | [app/Helpers/SuratNotificationHelper.php](app/Helpers/SuratNotificationHelper.php)                           |
