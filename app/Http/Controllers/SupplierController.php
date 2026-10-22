<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        $suppliers = Supplier::withCount('batchObats')
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('kontak_person', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            })
            ->orderBy('nama', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Supplier/Index', [
            'suppliers' => $suppliers,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Supplier/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150|unique:suppliers,nama',
            'alamat' => 'nullable|string|max:500',
            'telepon' => 'nullable|string|max:50',
            'kontak_person' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama supplier / PBF wajib diisi.',
            'nama.unique' => 'Nama supplier ini sudah terdaftar.',
        ]);

        Supplier::create($validated);

        return redirect()->route('supplier.index')->with('success', 'Data supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier): Response
    {
        return Inertia::render('Supplier/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150|unique:suppliers,nama,' . $supplier->id,
            'alamat' => 'nullable|string|max:500',
            'telepon' => 'nullable|string|max:50',
            'kontak_person' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama supplier / PBF wajib diisi.',
            'nama.unique' => 'Nama supplier ini sudah terdaftar.',
        ]);

        $supplier->update($validated);

        return redirect()->route('supplier.index')->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->batchObats()->exists()) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki riwayat batch obat.');
        }

        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
