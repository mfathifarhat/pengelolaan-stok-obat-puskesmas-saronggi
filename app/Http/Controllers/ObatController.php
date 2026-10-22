<?php

namespace App\Http\Controllers;

use App\Models\KategoriObat;
use App\Models\Obat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ObatController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $kategoriId = $request->query('kategori_id');
        $statusStok = $request->query('status_stok'); // 'kritis', 'aman'

        $query = Obat::with(['kategori', 'batches'])
            ->withSum('batches', 'stok')
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_obat', 'like', "%{$search}%")
                        ->orWhere('kode_obat', 'like', "%{$search}%")
                        ->orWhere('produsen', 'like', "%{$search}%");
                });
            })
            ->when($kategoriId, function ($q, $kategoriId) {
                $q->where('kategori_obat_id', $kategoriId);
            });

        if ($statusStok === 'kritis') {
            $query->whereRaw('(SELECT COALESCE(SUM(stok), 0) FROM batch_obats WHERE batch_obats.obat_id = obats.id) <= obats.stok_minimum');
        } elseif ($statusStok === 'aman') {
            $query->whereRaw('(SELECT COALESCE(SUM(stok), 0) FROM batch_obats WHERE batch_obats.obat_id = obats.id) > obats.stok_minimum');
        }

        $obats = $query->orderBy('nama_obat', 'asc')
            ->paginate(10)
            ->withQueryString();

        $kategoriList = KategoriObat::orderBy('nama', 'asc')->get();

        return Inertia::render('Obat/Index', [
            'obats' => $obats,
            'kategoriList' => $kategoriList,
            'filters' => [
                'search' => $search,
                'kategori_id' => $kategoriId,
                'status_stok' => $statusStok,
            ],
        ]);
    }

    public function create(): Response
    {
        $kategoriList = KategoriObat::orderBy('nama', 'asc')->get();

        return Inertia::render('Obat/Create', [
            'kategoriList' => $kategoriList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_obat' => 'required|string|max:50|unique:obats,kode_obat',
            'nama_obat' => 'required|string|max:150',
            'kategori_obat_id' => 'required|exists:kategori_obats,id',
            'satuan' => 'required|string|max:50',
            'produsen' => 'nullable|string|max:100',
            'stok_minimum' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'kode_obat.required' => 'Kode obat wajib diisi.',
            'kode_obat.unique' => 'Kode obat ini sudah digunakan.',
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'kategori_obat_id.required' => 'Kategori obat wajib dipilih.',
            'satuan.required' => 'Satuan obat wajib diisi.',
            'stok_minimum.required' => 'Batas stok minimum wajib ditentukan.',
        ]);

        Obat::create($validated);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function edit(Obat $obat): Response
    {
        $kategoriList = KategoriObat::orderBy('nama', 'asc')->get();

        return Inertia::render('Obat/Edit', [
            'obat' => $obat->load('kategori'),
            'kategoriList' => $kategoriList,
        ]);
    }

    public function update(Request $request, Obat $obat): RedirectResponse
    {
        $validated = $request->validate([
            'kode_obat' => 'required|string|max:50|unique:obats,kode_obat,' . $obat->id,
            'nama_obat' => 'required|string|max:150',
            'kategori_obat_id' => 'required|exists:kategori_obats,id',
            'satuan' => 'required|string|max:50',
            'produsen' => 'nullable|string|max:100',
            'stok_minimum' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'kode_obat.required' => 'Kode obat wajib diisi.',
            'kode_obat.unique' => 'Kode obat ini sudah digunakan.',
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'kategori_obat_id.required' => 'Kategori obat wajib dipilih.',
            'satuan.required' => 'Satuan obat wajib diisi.',
            'stok_minimum.required' => 'Batas stok minimum wajib ditentukan.',
        ]);

        $obat->update($validated);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat): RedirectResponse
    {
        if ($obat->batches()->where('stok', '>', 0)->exists()) {
            return back()->with('error', 'Obat tidak dapat dihapus karena masih memiliki batch dengan sisa stok aktif.');
        }

        $obat->delete();

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil dihapus.');
    }

    /**
     * Get active batches sorted by FEFO (First Expired First Out)
     */
    public function getActiveBatches(Obat $obat): JsonResponse
    {
        $batches = $obat->batches()
            ->where('stok', '>', 0)
            ->with('supplier')
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();

        return response()->json($batches);
    }
}
