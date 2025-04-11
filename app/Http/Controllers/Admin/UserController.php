<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\MahasiswaExport;
use App\Imports\MahasiswaImport;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of all users (for admin)
     */
    // public function index()
    // {
    //     $users = User::with('roles')->get();
    //     $roles = Role::all();
    //     return view('admin.users.index', compact('users', 'roles'));
    // }

    /**
     * Display a listing of mahasiswa users
     */
    public function mahasiswa(Request $request)
    {
        $this->authorize('manage students');
        $search = $request->input('search');
        $query = User::role('mahasiswa');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }
        $users = $query->paginate(15);
        return view('admin.users.mahasiswa.index', compact('users', 'search'));
    }


    /**
     * Display a listing of dosen users
     */
    public function dosen()
    {
        $this->authorize('manage lecturers');
        $users = User::role('dosen')->paginate(10); // 10 items per page
        return view('admin.users.dosen.index', compact('users'));
    }

    /**
     * Display a listing of staff users
     */
    public function staff()
    {
        $this->authorize('manage staff');
        $users = User::role('staff')->paginate(10); // 10 items per page
        return view('admin.users.staff.index', compact('users'));
    }

    /**
     * Show the form for creating a new mahasiswa
     */
    public function createMahasiswa()
    {
        return view('admin.users.mahasiswa.create');
    }

    /**
     * Show the form for creating a new dosen
     */
    public function createDosen()
    {
        return view('admin.users.dosen.create');
    }

    /**
     * Show the form for creating a new staff
     */
    public function createStaff()
    {
        return view('admin.users.staff.create');
    }

    /**
     * Store a newly created mahasiswa in storage.
     */
    public function storeMahasiswa(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'nim' => 'required|unique:users'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nim' => $request->nim,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('mahasiswa');

        return redirect()->route('admin.users.mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan');
    }

    /**
     * Store a newly created dosen in storage.
     */
    public function storeDosen(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'nidn' => 'required|unique:users'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nidn' => $request->nidn,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('dosen');

        return redirect()->route('admin.users.dosen')->with('success', 'Dosen berhasil ditambahkan');
    }

    /**
     * Store a newly created staff in storage.
     */
    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('staff');

        return redirect()->route('admin.users.staff')->with('success', 'Staff berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified mahasiswa.
     */
    public function editMahasiswa(User $user)
    {
        return view('admin.users.mahasiswa.edit', compact('user'));
    }

    /**
     * Show the form for editing the specified dosen.
     */
    public function editDosen(User $user)
    {
        return view('admin.users.dosen.edit', compact('user'));
    }

    /**
     * Show the form for editing the specified staff.
     */
    public function editStaff(User $user)
    {
        return view('admin.users.staff.edit', compact('user'));
    }

    /**
     * Update the specified mahasiswa in storage.
     */
    public function updateMahasiswa(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nim' => 'required|unique:users,nim,' . $user->id
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nim' => $request->nim
        ]);

        return redirect()->route('admin.users.mahasiswa')->with('success', 'Data mahasiswa berhasil diperbarui');
    }

    /**
     * Update the specified dosen in storage.
     */
    public function updateDosen(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nidn' => 'required|unique:users,nidn,' . $user->id
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nidn' => $request->nidn
        ]);

        return redirect()->route('admin.users.dosen')->with('success', 'Data dosen berhasil diperbarui');
    }

    /**
     * Update the specified staff in storage.
     */
    public function updateStaff(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.users.staff')->with('success', 'Data staff berhasil diperbarui');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Determine where the request is coming from
        $referer = $request->header('referer');
        $defaultRoute = 'admin.users.index';

        // Check which list the request came from
        if (str_contains($referer, 'mahasiswa')) {
            $redirectRoute = 'admin.users.mahasiswa';
            $message = 'Mahasiswa berhasil dihapus';
        } elseif (str_contains($referer, 'dosen')) {
            $redirectRoute = 'admin.users.dosen';
            $message = 'Dosen berhasil dihapus';
        } elseif (str_contains($referer, 'staff')) {
            $redirectRoute = 'admin.users.staff';
            $message = 'Staff berhasil dihapus';
        } else {
            $redirectRoute = $defaultRoute;
            $message = 'Pengguna berhasil dihapus';
        }

        $user->delete();

        return redirect()->route($redirectRoute)
            ->with('success', $message);
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $validRoles = ['staff', 'dosen', 'mahasiswa']; // Sesuaikan
        $request->validate([
            'role' => 'required|in:' . implode(',', $validRoles)
        ]);

        $user->syncRoles($request->role);
        return back()->with('success', 'Role berhasil diperbarui');
    }

    /**
     * Export mahasiswa data to Excel
     */
    public function exportMahasiswa()
    {
        return Excel::download(new MahasiswaExport, 'data-mahasiswa-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Show import form for mahasiswa
     */
    public function showImportMahasiswa()
    {
        return view('admin.users.mahasiswa.import');
    }

    /**
     * Process mahasiswa data import
     */
    public function importMahasiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            // Pastikan role mahasiswa ada
            Role::firstOrCreate(['name' => 'mahasiswa']);

            Excel::import(new MahasiswaImport, $request->file('file'));

            return redirect()
                ->route('admin.users.mahasiswa')
                ->with('success', 'Data mahasiswa berhasil diimport');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download template for mahasiswa import
     */
    public function downloadTemplateMahasiswa()
    {
        $headers = [
            'NIM' => 'Contoh: 12345678',
            'Nama' => 'Contoh: John Doe',
            'Email' => 'Opsional',
            'Password' => 'Opsional (min 8 karakter)'
        ];

        $export = new class ($headers) implements FromArray {
            private $headers;

            public function __construct($headers)
            {
                $this->headers = $headers;
            }

            public function array(): array
            {
                return [
                    array_keys($this->headers),
                    array_values($this->headers)
                ];
            }
        };

        return Excel::download($export, 'template-import-mahasiswa.xlsx');
    }
}