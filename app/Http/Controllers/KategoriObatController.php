<?php

namespace App\Http\Controllers;

use App\Models\KategoriObat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KategoriObatController extends Controller
{
    /**
     * Get all categories as JSON (for dropdowns and modal lists)
     */
    public function index(): JsonResponse
    {
        $categories = KategoriObat::withCount('obats')
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_obats,nama',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori ini sudah terdaftar.',
        ]);

        KategoriObat::create($validated);

        return back()->with('success', 'Kategori obat berhasil ditambahkan.');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, KategoriObat $kategoriObat): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_obats,nama,' . $kategoriObat->id,
            'keterangan' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori ini sudah terdaftar.',
        ]);

        $kategoriObat->update($validated);

        return back()->with('success', 'Kategori obat berhasil diperbarui.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(KategoriObat $kategoriObat): RedirectResponse
    {
        if ($kategoriObat->obats()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data obat.');
        }

        $kategoriObat->delete();

        return back()->with('success', 'Kategori obat berhasil dihapus.');
    }
}
