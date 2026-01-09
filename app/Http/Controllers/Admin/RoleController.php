<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('manage roles');

        $roles = Role::with('permissions')->paginate(10);

        // Group permissions by their group attribute for better UI
        $permissions = Permission::all()
            ->groupBy('group')
            ->sortKeys();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage roles');

        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role berhasil ditambahkan');
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('manage roles');

        $isSystemRole = in_array($role->name, ['staff', 'dosen', 'mahasiswa']);

        // Validation rules
        $rules = [
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ];

        // Only allow name change for non-system roles
        if (!$isSystemRole) {
            $rules['name'] = 'required|unique:roles,name,' . $role->id . '|max:255';
        }

        $request->validate($rules);

        // Update name only if it's not a system role
        if (!$isSystemRole && $request->has('name')) {
            $role->update(['name' => $request->name]);
        }

        // Sync permissions for all roles
        $role->syncPermissions($request->permissions ?? []);

        $message = $isSystemRole
            ? 'Permission role sistem berhasil diperbarui'
            : 'Role berhasil diperbarui';

        return redirect()
            ->route('admin.roles.index')
            ->with('success', $message);
    }

    public function destroy(Role $role)
    {
        $this->authorize('manage roles');

        // Prevent deleting core system roles
        if (in_array($role->name, ['staff', 'dosen', 'mahasiswa'])) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Role sistem tidak dapat dihapus');
        }

        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh ' . $role->users()->count() . ' pengguna');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role berhasil dihapus');
    }
}