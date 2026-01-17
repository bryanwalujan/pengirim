# E-Service App - AI Agent Instructions

## Project Overview

Laravel 12 e-service application for academic document management (student letters, committee assignments, thesis advisor appointments, seminar scheduling). Uses Spatie Permission for role-based access (mahasiswa/student, dosen/lecturer, staff/admin), PDF generation with DomPDF, QR codes, and digital signatures.

## Architecture & Key Patterns

### Action-Service-Controller Pattern

Business logic organized in three layers:

- **Actions** (`app/Actions/{Feature}/`): Single-purpose operations (e.g., `AssignPembimbingAction`, `SignByKajurAction`)
- **Services** (`app/Services/`): Complex domain logic (e.g., `SkPembimbingPdfService`, `SignatureService`)
- **Controllers**: Route handling only, delegate to Actions/Services

Example: [app/Actions/SkPembimbing/AssignPembimbingAction.php](app/Actions/SkPembimbing/AssignPembimbingAction.php)

### Role-Based Authorization

Uses Spatie Permission package with three primary roles:

- **mahasiswa**: Students who submit requests
- **dosen**: Lecturers (can have jabatan: `koordinator_prodi`, `ketua_jurusan`)
- **staff**: Admin users with full access

Authorization via:

- Policies (`app/Policies/`) mapped in `AuthServiceProvider`
- Blade directives: `@can('permission')` in sidebar/views
- Request classes: `authorize()` method checks roles
- Trait helpers: `hasRole()`, `isKoordinatorProdi()`, `isKetuaJurusan()`

### Document Workflow System

Letter/document models follow status-based workflows with audit trails:

**Status Constants Pattern** (see `PengajuanSkPembimbing`):

```php
const STATUS_DRAFT = 'draft';
const STATUS_MENUNGGU_TTD_KORPRODI = 'menunggu_ttd_korprodi';
const STATUS_MENUNGGU_TTD_KAJUR = 'menunggu_ttd_kajur';
const STATUS_SELESAI = 'selesai';
const STATUS_DITOLAK = 'ditolak';
```

**Audit Trail Fields**: Track who/when for each state transition

- `verified_by`, `verified_at`
- `ps_assigned_by`, `ps_assigned_at`
- `ttd_korprodi_by`, `ttd_korprodi_at`
- `ttd_kajur_by`, `ttd_kajur_at`

**Helper Methods**: Models include status checks (`canBeEditedByMahasiswa()`, `isSelesai()`)

### Letter Numbering System (`GeneratesNomorSurat` Trait)

Unified numbering across surat types using academic year context:

- Format: `UN41.2/TI.01/{sequence}/{year}`
- Resets counter on semester change (when semester = "Genap")
- Cache-backed to prevent duplicates (`active_tahun_ajaran` cache key)
- Supports custom numbers via admin override

Config: [config/surat.php](config/surat.php) defines models, final statuses, required fields

### PDF Generation & Digital Signatures

Two-stage PDF generation (see `SkPembimbingPdfService`):

1. **Initial PDF**: Draft without signatures when PS assigned
2. **Final PDF**: Regenerated with QR codes after each signature

Signature workflow embeds QR codes (base64) into PDFs:

- QR contains verification URL: `/verify/sk-pembimbing/{code}`
- Stored as `qr_code_korprodi`, `qr_code_kajur` in model
- Public verification route (no auth) for document authenticity

### Event-Driven Cache Management

Cache strategy for performance:

- `TahunAjaranChanged` event → clears `active_tahun_ajaran` cache
- `ClearSuratSubmissionCache` listener → clears user-specific caches
- Helper: `SuratNotificationHelper::clearSuratCache()` for sidebar badges
- Models use `Cache::remember()` for expensive queries (e.g., submission checks)

Key cache keys: `surat_submission_check_{userId}`, `pending_surat_check_{userId}`, `active_tahun_ajaran`

## Development Workflows

### Running the Application

```bash
# Development mode (concurrent server + queue + vite)
composer dev

# Or manually:
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Testing

Uses Pest PHP (v3):

```bash
vendor/bin/pest                    # Run all tests
vendor/bin/pest --filter FeatureName
```

Test structure: `tests/Feature/` and `tests/Unit/`
Example: [tests/Feature/ProfileTest.php](tests/Feature/ProfileTest.php)

### Code Quality

```bash
./vendor/bin/pint    # Laravel Pint (PSR-12 formatting)
```

### Quick Login (Development)

Local-only dev routes for fast role switching:

- GET `/dev/users/{role}` - List users by role
- POST `/dev/login/{role}` - Quick login (mahasiswa|dosen|staff)

Protected by `local.only` middleware

## Project-Specific Conventions

### Database Migrations

- Named with full timestamps: `YYYY_MM_DD_HHMMSS_create_table.php`
- Use composite indexes for common query patterns (e.g., `['status', 'created_at']`)
- Soft deletes on all user-submitted data
- Foreign keys: `constrained()->cascadeOnDelete()` or `nullOnDelete()` based on data retention

### Blade Views Organization

```
resources/views/
  admin/           # Staff/dosen views (CRUD, approvals)
  user/            # Mahasiswa views (submissions)
  layouts/
    admin/sidebar.blade.php  # Permission-based navigation
  components/      # Reusable UI components
```

### Request Validation

Custom Request classes with role-based rules:

```php
// Different validation based on user role
if ($this->user()->hasRole('staff')) {
    return ['field' => 'required'];
}
```

Example: [app/Http/Requests/SkPembimbing/AssignPembimbingRequest.php](app/Http/Requests/SkPembimbing/AssignPembimbingRequest.php)

### File Storage

- Private files: `storage/app/{feature}/{YYYY/MM}/filename.pdf`
- Public access via: `Storage::disk('local')->exists()` checks + response streams
- Verification documents: separate download routes for authenticated access

## Integration Points

### External Dependencies

- **DomPDF** (`barryvdh/laravel-dompdf`): PDF generation from Blade templates
- **Maatwebsite Excel**: Import/export mahasiswa/UKT data
- **SimpleSoftwareIO QR Code**: Digital signature verification
- **Spatie Permission**: Role & permission management
- **Laravel Telescope**: Debugging/monitoring (access via `/telescope`, gated by role)

### Frontend Stack

- **Vite** (v6): Asset bundling
- **Tailwind CSS** (v3) + forms plugin
- **Alpine.js**: Minimal interactivity
- **PDF.js** + **pdf-lib**: Client-side PDF manipulation

### Cross-Component Communication

- Models fire events on state changes (configured in `EventServiceProvider`)
- Listeners update caches, notifications, related records
- Helpers bridge components: `SuratNotificationHelper` for sidebar counts

## Critical Files to Reference

- [app/Traits/GeneratesNomorSurat.php](app/Traits/GeneratesNomorSurat.php) - Letter numbering logic
- [app/Models/PengajuanSkPembimbing.php](app/Models/PengajuanSkPembimbing.php) - Example of complete workflow model
- [config/surat.php](config/surat.php) - Document submission rules
- [routes/web.php](routes/web.php) - Route organization by role/feature
- [app/Providers/AuthServiceProvider.php](app/Providers/AuthServiceProvider.php) - Policy registration

## Common Pitfalls

1. **Cache invalidation**: Always clear related caches after workflow state changes
2. **Role checks**: Use `hasRole()` not `role == 'name'` (supports multi-role users)
3. **Nomor surat generation**: Must be in transaction with model save to prevent duplicates
4. **PDF regeneration**: Required after each signature to embed new QR codes
5. **UKT check middleware**: Applied to mahasiswa routes, blocks unpaid students
