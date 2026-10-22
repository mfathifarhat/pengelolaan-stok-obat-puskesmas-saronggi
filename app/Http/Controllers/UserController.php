<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $users = User::when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Pengguna/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Pengguna/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|min:3|max:50|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(['admin', 'petugas'])],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'username.required' => 'Username login wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berupa huruf, angka, tanda strip, atau garis bawah.',
            'username.unique' => 'Username ini sudah digunakan.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Pengguna/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'petugas'])],
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'username.required' => 'Username login wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berupa huruf, angka, tanda strip, atau garis bawah.',
            'username.unique' => 'Username ini sudah digunakan.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
