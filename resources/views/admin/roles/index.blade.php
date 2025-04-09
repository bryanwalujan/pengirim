@extends('layouts.admin.app')

@section('title', 'Manajemen Role')

@push('styles')
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
        <!-- /Breadcrumb -->

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Daftar Role & Permission</span>
        </h4>

        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-end g-2">
                    <!-- Search Column -->
                    <div class="col-4 col-md-4 col-lg-3">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text" id="basic-addon-search31">
                                <i class="bx bx-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Cari role..."
                                wire:model.debounce.300ms="search" aria-label="Search..."
                                aria-describedby="basic-addon-search31" />
                        </div>
                    </div>
                    <!-- Button Column -->
                    <div class="col-auto text-end">
                        <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#createRoleModal" style="min-width: 42px; justify-content: center;">
                            <i class="bx bx-plus d-flex d-sm-inline-flex"></i>
                            <span class="d-none d-sm-inline ms-2">Tambah Role</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Role</th>
                            <th>Permissions</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-shield me-2"></i>
                                        <span>{{ $role->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @foreach ($role->permissions as $permission)
                                        <span class="badge bg-label-primary me-1 mb-1">{{ $permission->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item text-info edit-role-btn"
                                                data-id="{{ $role->id }}" data-name="{{ $role->name }}"
                                                data-permissions="{{ json_encode($role->permissions->pluck('id')->toArray()) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </button>

                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger delete-btn">
                                                    <i class="bx bx-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada role yang ditambahkan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($roles->hasPages())
                <div class="card-footer border-top py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                            {{-- Previous Page Link --}}
                            <li class="page-item prev {{ $roles->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $roles->previousPageUrl() }}">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>

                            {{-- Pagination Elements --}}
                            @foreach ($roles->getUrlRange(1, $roles->lastPage()) as $page => $url)
                                <li class="page-item {{ $roles->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            {{-- Next Page Link --}}
                            <li class="page-item next {{ !$roles->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $roles->nextPageUrl() }}">
                                    <i class="bx bx-chevrons-right icon-sm"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
        <!--/ Card -->
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Role Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="roleName" class="form-label">Nama Role</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                        </div>

                        <h6 class="mb-3">Permissions:</h6>
                        <div class="row">
                            @foreach ($permissions as $group => $perms)
                                <div class="col-md-6 mb-3">
                                    <div class="permission-group border p-3 rounded">
                                        <h6 class="mb-2">{{ ucfirst($group) }}</h6>
                                        @foreach ($perms as $permission)
                                            <div class="form-check">
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
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editRoleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editRoleName" class="form-label">Nama Role</label>
                            <input type="text" class="form-control" id="editRoleName" name="name" required>
                        </div>

                        <h6 class="mb-3">Permissions:</h6>
                        <div class="row">
                            @foreach ($permissions as $group => $perms)
                                <div class="col-md-6 mb-3">
                                    <div class="permission-group border p-3 rounded">
                                        <h6 class="mb-2">{{ ucfirst($group) }}</h6>
                                        @foreach ($perms as $permission)
                                            <div class="form-check">
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
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap semua tombol delete
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

            // Handle edit role button
            const editRoleButtons = document.querySelectorAll('.edit-role-btn');
            const editRoleModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
            const editRoleForm = document.getElementById('editRoleForm');
            const editRoleName = document.getElementById('editRoleName');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            editRoleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const roleId = this.getAttribute('data-id');
                    const roleName = this.getAttribute('data-name');
                    const permissions = JSON.parse(this.getAttribute('data-permissions'));

                    // Set form action
                    editRoleForm.action = `/admin/roles/${roleId}`;

                    // Set role name
                    editRoleName.value = roleName;

                    // Uncheck all checkboxes first
                    permissionCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Check the permissions this role has
                    permissions.forEach(permissionId => {
                        const checkbox = document.querySelector(
                            `#edit-perm-${permissionId}`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });

                    // Show modal
                    editRoleModal.show();
                });
            });
        });
    </script>
@endpush
