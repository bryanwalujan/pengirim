{{-- filepath: resources/views/admin/roles/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Manajemen Role')

@push('styles')
    <style>
        .system-role-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            vertical-align: middle;
        }

        .permission-group {
            transition: all 0.3s ease;
        }

        .permission-group:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .approval-permission {
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
        }

        .group-header {
            background-color: #f8f9fa;
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        /* Custom styling for protected role dropdown */
        .dropdown-item-text.protected-info {
            font-size: 0.75rem;
            padding: 0.35rem 1.5rem;
            color: #a1acb8;
            cursor: default;
            background-color: #f8f9fa;
        }

        .dropdown-item-text.protected-info:hover {
            background-color: #f8f9fa;
        }

        /* Hover effect for view button */
        .view-role-btn:hover {
            background-color: rgba(105, 108, 255, 0.08);
            color: #696cff;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Role</li>
            </ol>
        </nav>

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Daftar Role & Permission</span>
        </h4>

        {{-- Info Alert --}}
        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-1">
                <i class="bx bx-info-circle me-2"></i>Informasi Permission Management
            </h6>
            <ul class="mb-0 ps-3">
                <li>
                    <strong>Role Sistem</strong> (Staff, Dosen, Mahasiswa): Nama tidak dapat diubah, tapi permissions dapat
                    disesuaikan.
                </li>
                <li>
                    Permission <strong>approval surat</strong> untuk dosen diberikan otomatis berdasarkan jabatan
                    (<strong>Koordinator Program Studi</strong> atau <strong>Pimpinan Jurusan PTIK</strong>).
                </li>
                <li>
                    Anda dapat membuat role custom baru sesuai kebutuhan organisasi.
                </li>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    <div class="col-auto">
                        <h5 class="mb-0">Total: {{ $roles->total() }} Role</h5>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createRoleModal">
                            <i class="bx bx-plus me-1"></i>
                            <span>Tambah Role</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama Role</th>
                            <th>Permissions</th>
                            <th width="10%">Users</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-shield me-2"></i>
                                        <span>{{ ucfirst($role->name) }}</span>
                                        @if (in_array($role->name, ['staff', 'dosen', 'mahasiswa']))
                                            <span class="badge bg-label-warning system-role-badge ms-2">
                                                <i class="bx bx-lock-alt"></i> System
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($role->permissions->count() > 0)
                                        @php
                                            $displayLimit = 5;
                                            $groupedPerms = $role->permissions->groupBy('group');
                                        @endphp

                                        @foreach ($groupedPerms->take(3) as $group => $perms)
                                            <div class="mb-1">
                                                <small class="text-muted">{{ $group }}:</small>
                                                @foreach ($perms->take(2) as $perm)
                                                    <span class="badge bg-label-primary me-1">{{ $perm->name }}</span>
                                                @endforeach
                                                @if ($perms->count() > 2)
                                                    <span class="badge bg-label-secondary">+{{ $perms->count() - 2 }}</span>
                                                @endif
                                            </div>
                                        @endforeach

                                        @if ($groupedPerms->count() > 3)
                                            <small class="text-muted">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                                {{ $groupedPerms->count() - 3 }} group lainnya
                                            </small>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">Tidak ada permission</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-info">
                                        {{ $role->users()->count() }} user
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            {{-- Edit button for ALL roles --}}
                                            <button class="dropdown-item edit-role-btn" data-id="{{ $role->id }}"
                                                data-name="{{ $role->name }}"
                                                data-is-system="{{ in_array($role->name, ['staff', 'dosen', 'mahasiswa']) ? 'true' : 'false' }}"
                                                data-permissions="{{ json_encode($role->permissions->pluck('name')->toArray()) }}">
                                                <i class="bx bx-edit-alt me-2"></i>
                                                <span>Edit
                                                    {{ in_array($role->name, ['staff', 'dosen', 'mahasiswa']) ? 'Permissions' : '' }}</span>
                                            </button>

                                            {{-- Only show delete for non-system roles --}}
                                            @if (!in_array($role->name, ['staff', 'dosen', 'mahasiswa']))
                                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger delete-btn">
                                                        <i class="bx bx-trash me-2"></i>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="dropdown-divider my-1"></div>

                                                {{-- Info for system roles --}}
                                                <div class="dropdown-item-text protected-info d-flex align-items-center">
                                                    <i class="bx bx-info-circle me-2"></i>
                                                    <span class="small">Nama role tidak dapat diubah</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="bx bx-folder-open bx-lg text-muted mb-2 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada role yang ditambahkan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($roles->hasPages())
                <div class="card-footer border-top py-3">
                    {{ $roles->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-plus-circle me-2"></i>Tambah Role Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="roleName" class="form-label">Nama Role</label>
                            <input type="text" class="form-control" id="roleName" name="name"
                                placeholder="Contoh: editor, moderator" required>
                            <small class="text-muted">Gunakan huruf kecil tanpa spasi</small>
                        </div>

                        <h6 class="mb-3">
                            <i class="bx bx-lock-open me-2"></i>Pilih Permissions
                        </h6>
                        <div class="row">
                            @foreach ($permissions as $group => $perms)
                                <div class="col-md-6 mb-3">
                                    <div
                                        class="permission-group border p-3 rounded {{ in_array($group, ['Surat (Approval)']) ? 'approval-permission' : '' }}">
                                        <div class="group-header">
                                            <i class="bx bx-folder me-1"></i>{{ $group }}
                                            @if ($group === 'Surat (Approval)')
                                                <span class="badge bg-warning ms-2"
                                                    title="Otomatis untuk Koordinator & Pimpinan">
                                                    <i class="bx bx-info-circle"></i>
                                                </span>
                                            @endif
                                        </div>
                                        @foreach ($perms as $permission)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    id="perm-{{ $permission->id }}" value="{{ $permission->name }}">
                                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editRoleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-edit me-2"></i>Edit <span id="editModalTitle">Role</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Alert for system roles --}}
                        <div class="alert alert-warning d-none" id="systemRoleAlert">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Role Sistem:</strong> Nama role tidak dapat diubah. Anda hanya dapat mengelola
                            permissions.
                        </div>

                        <div class="mb-4" id="roleNameContainer">
                            <label for="editRoleName" class="form-label">Nama Role</label>
                            <input type="text" class="form-control" id="editRoleName" name="name" required>
                            <div id="systemRoleBadge" class="mt-2 d-none">
                                <span class="badge bg-label-warning">
                                    <i class="bx bx-lock-alt me-1"></i>Role Sistem
                                </span>
                            </div>
                        </div>

                        <h6 class="mb-3">
                            <i class="bx bx-lock-open me-2"></i>Kelola Permissions
                        </h6>
                        <div class="row">
                            @foreach ($permissions as $group => $perms)
                                <div class="col-md-6 mb-3">
                                    <div
                                        class="permission-group border p-3 rounded {{ in_array($group, ['Surat (Approval)']) ? 'approval-permission' : '' }}">
                                        <div class="group-header">
                                            <i class="bx bx-folder me-1"></i>{{ $group }}
                                            @if ($group === 'Surat (Approval)')
                                                <span class="badge bg-warning ms-2"
                                                    title="Otomatis untuk Koordinator & Pimpinan">
                                                    <i class="bx bx-info-circle"></i>
                                                </span>
                                            @endif
                                        </div>
                                        @foreach ($perms as $permission)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    name="permissions[]" id="edit-perm-{{ $permission->id }}"
                                                    value="{{ $permission->name }}">
                                                <label class="form-check-label" for="edit-perm-{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- View Role Modal (for system roles) -->
    <div class="modal fade" id="viewRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-show me-2"></i>Detail Role <span id="viewRoleName" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bx bx-lock-alt me-2"></i>
                        <strong>Role Sistem:</strong> Role ini dilindungi dan tidak dapat diubah melalui interface ini.
                    </div>

                    <h6 class="mb-3">Permissions yang Dimiliki:</h6>
                    <div id="viewPermissions" class="row"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Role yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Edit role modal
            const editRoleButtons = document.querySelectorAll('.edit-role-btn');
            const editRoleModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
            const editRoleForm = document.getElementById('editRoleForm');
            const editRoleName = document.getElementById('editRoleName');
            const systemRoleAlert = document.getElementById('systemRoleAlert');
            const systemRoleBadge = document.getElementById('systemRoleBadge');
            const editModalTitle = document.getElementById('editModalTitle');

            editRoleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const roleId = this.getAttribute('data-id');
                    const roleName = this.getAttribute('data-name');
                    const isSystem = this.getAttribute('data-is-system') === 'true';
                    const permissions = JSON.parse(this.getAttribute('data-permissions'));

                    console.log('Edit button clicked:', {
                        roleId,
                        roleName,
                        isSystem,
                        permissions
                    }); // Debug log

                    // Set form action
                    editRoleForm.action = `/admin/roles/${roleId}`;
                    editRoleName.value = roleName;

                    // Handle system role display
                    if (isSystem) {
                        editModalTitle.textContent = 'Permissions Role ' + roleName.toUpperCase();
                        systemRoleAlert.classList.remove('d-none');
                        systemRoleBadge.classList.remove('d-none');
                        editRoleName.setAttribute('readonly', 'readonly');
                        editRoleName.classList.add('bg-light');
                    } else {
                        editModalTitle.textContent = 'Role';
                        systemRoleAlert.classList.add('d-none');
                        systemRoleBadge.classList.add('d-none');
                        editRoleName.removeAttribute('readonly');
                        editRoleName.classList.remove('bg-light');
                    }

                    // Uncheck all first
                    document.querySelectorAll('.permission-checkbox').forEach(cb => {
                        cb.checked = false;
                    });

                    // Check permissions this role has
                    permissions.forEach(permName => {
                        const checkbox = document.querySelector(
                            `.permission-checkbox[value="${permName}"]`
                        );
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });

                    editRoleModal.show();
                });
            });

            // View role modal (for system roles) - MOVED INSIDE DOMContentLoaded
            const viewRoleButtons = document.querySelectorAll('.view-role-btn');
            const viewRoleModal = new bootstrap.Modal(document.getElementById('viewRoleModal'));

            viewRoleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const roleName = this.getAttribute('data-name');
                    const permissions = JSON.parse(this.getAttribute('data-permissions'));

                    console.log('View button clicked:', {
                        roleName,
                        permissions
                    }); // Debug log

                    document.getElementById('viewRoleName').textContent = roleName.toUpperCase();

                    const viewPermissionsDiv = document.getElementById('viewPermissions');
                    viewPermissionsDiv.innerHTML = '';

                    // Group permissions
                    const grouped = {};
                    @foreach ($permissions as $group => $perms)
                        grouped['{{ $group }}'] = [];
                        @foreach ($perms as $perm)
                            if (permissions.includes('{{ $perm->name }}')) {
                                grouped['{{ $group }}'].push('{{ $perm->name }}');
                            }
                        @endforeach
                    @endforeach

                    // Display grouped permissions
                    Object.keys(grouped).forEach(group => {
                        if (grouped[group].length > 0) {
                            const col = document.createElement('div');
                            col.className = 'col-md-6 mb-3';
                            col.innerHTML = `
                                <div class="permission-group border p-3 rounded">
                                    <div class="group-header mb-2">
                                        <i class="bx bx-folder me-1"></i>${group}
                                    </div>
                                    ${grouped[group].map(p => `
                                            <div class="mb-1">
                                                <i class="bx bx-check text-success me-1"></i>
                                                <span>${p}</span>
                                            </div>
                                        `).join('')}
                                </div>
                            `;
                            viewPermissionsDiv.appendChild(col);
                        }
                    });

                    viewRoleModal.show();
                });
            });
        });
    </script>
@endpush
