<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Models\Prize;
use App\Models\Form;
use App\Models\Winner;

class AdminManagerController extends Controller
{
    /**
     * Daftar role yang bisa dikelola
     */
    protected $managedRoles = ['admin', 'input_hadiah'];

    /**
     * Tampilkan daftar admin
     */
    public function index()
    {
        $admins = User::whereIn('role', $this->managedRoles)
            ->with('permissions')
            ->latest()
            ->paginate(10);

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Tampilkan form tambah admin
     */
    public function create()
    {
        $roles = [
            'admin' => 'Admin (Full Access)',
            'input_hadiah' => 'Custom (Access)',
        ];

        $permissions = Permission::all();

        return view('admin.admins.create', compact('roles', 'permissions'));
    }

    /**
     * Simpan admin baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,input_hadiah'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Jika bukan admin, sync permissions
        if ($validated['role'] !== 'admin') {
            $user->permissions()->sync($validated['permissions'] ?? []);
        }

        $roleLabel = $validated['role'] === 'admin' ? 'Admin' : 'Input Hadiah';

        return redirect()->route('admin.admins.index')
            ->with('success', "Akun {$roleLabel} berhasil ditambahkan.");
    }

    /**
     * Tampilkan form edit admin
     */
    public function edit(User $admin)
    {
        if (!in_array($admin->role, $this->managedRoles)) {
            abort(404);
        }

        $roles = [
            'admin' => 'Admin (Full Access)',
            'input_hadiah' => 'Custom (Access)',
        ];

        $permissions = Permission::all();
        $userPermissions = $admin->permissions->pluck('id')->toArray();

        return view('admin.admins.edit', compact('admin', 'roles', 'permissions', 'userPermissions'));
    }

    /**
     * Update admin
     */
    public function update(Request $request, User $admin)
    {
        if (!in_array($admin->role, $this->managedRoles)) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($admin->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,input_hadiah'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        // Sync permissions jika bukan admin
        if ($validated['role'] !== 'admin') {
            $admin->permissions()->sync($validated['permissions'] ?? []);
        } else {
            $admin->permissions()->detach();
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Data akun berhasil diperbarui.');
    }

    /**
     * Hapus admin
     */
    public function destroy(User $admin)
    {
        if (!in_array($admin->role, $this->managedRoles)) {
            abort(404);
        }

        // Tidak boleh hapus diri sendiri
        if (Auth::id() === $admin->id) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    public function pemenang()
    {
        // Ambil semua hadiah beserta pemenangnya
        $prizes = Prize::with(['winner.submission'])->get();

        return view('pemenang', compact('prizes'));
    }

    public function winners()
    {
        $winners = Winner::with(['prize', 'submission'])->get();
        return view('winners', compact('winners'));
    }

    public function undian()
    {
        $forms = Form::with(['prizes.winner'])->get();
        return view('undian', compact('forms'));
    }
}