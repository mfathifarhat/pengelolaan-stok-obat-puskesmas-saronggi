<?php

namespace App\Http\Controllers;

use App\Models\BatchObat;
use App\Models\Obat;
use App\Models\StokMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StokController extends Controller
{
    /**
     * Display stock movement history with filters.
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $tipe = $request->query('tipe');
        $obatId = $request->query('obat_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $movements = StokMovement::with(['batchObat.obat', 'batchObat.supplier', 'user'])
            ->when($tipe, fn ($q) => $q->where('tipe', $tipe))
            ->when($obatId, function ($q) use ($obatId) {
                $q->whereHas('batchObat', fn ($b) => $b->where('obat_id', $obatId));
            })
            ->when($startDate, fn ($q) => $q->where('tanggal', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('tanggal', '<=', $endDate))
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('batchObat', function ($b) use ($search) {
                            $b->where('no_batch', 'like', "%{$search}%")
                                ->orWhereHas('obat', fn ($o) => $o->where('nama_obat', 'like', "%{$search}%"));
                        });
                });
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $obats = Obat::orderBy('nama_obat', 'asc')->get(['id', 'nama_obat', 'kode_obat']);

        return Inertia::render('Stok/Index', [
            'movements' => $movements,
            'obats' => $obats,
            'filters' => [
                'search' => $search,
                'tipe' => $tipe,
                'obat_id' => $obatId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Show form for incoming stock (Penerimaan Obat / Batch Baru).
     */
    public function masuk(): Response
    {
        $obats = Obat::with('kategori')->orderBy('nama_obat', 'asc')->get();
        $suppliers = Supplier::orderBy('nama', 'asc')->get();

        return Inertia::render('Stok/Masuk', [
            'obats' => $obats,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Store incoming stock and create/update batch.
     */
    public function storeMasuk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'obat_id' => 'required|exists:obats,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'no_batch' => 'required|string|max:100',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_masuk',
            'harga_beli' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'obat_id.required' => 'Obat wajib dipilih.',
            'no_batch.required' => 'Nomor batch wajib diisi.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_kadaluarsa.required' => 'Tanggal kadaluarsa wajib diisi.',
            'tanggal_kadaluarsa.after' => 'Tanggal kadaluarsa harus setelah tanggal masuk.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'jumlah.required' => 'Jumlah obat masuk wajib diisi dan minimal 1.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Find or create batch
            $batch = BatchObat::where('obat_id', $validated['obat_id'])
                ->where('no_batch', $validated['no_batch'])
                ->first();

            $stokSebelum = $batch ? $batch->stok : 0;
            $stokSesudah = $stokSebelum + $validated['jumlah'];

            if ($batch) {
                $batch->update([
                    'supplier_id' => $validated['supplier_id'] ?? $batch->supplier_id,
                    'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'],
                    'harga_beli' => $validated['harga_beli'],
                    'stok' => $stokSesudah,
                ]);
            } else {
                $batch = BatchObat::create([
                    'obat_id' => $validated['obat_id'],
                    'supplier_id' => $validated['supplier_id'],
                    'no_batch' => $validated['no_batch'],
                    'tanggal_masuk' => $validated['tanggal_masuk'],
                    'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'],
                    'harga_beli' => $validated['harga_beli'],
                    'stok' => $stokSesudah,
                ]);
            }

            // Record movement
            StokMovement::create([
                'batch_obat_id' => $batch->id,
                'user_id' => $request->user()->id,
                'tipe' => 'masuk',
                'jumlah' => $validated['jumlah'],
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'tanggal' => $validated['tanggal_masuk'],
                'keterangan' => $validated['keterangan'] ?? 'Penerimaan stok obat batch ' . $batch->no_batch,
            ]);
        });

        return redirect()->route('stok.index')->with('success', 'Stok masuk berhasil dicatat ke sistem.');
    }

    /**
     * Show form for outgoing stock (Pengeluaran Obat dengan rekomendasi FEFO).
     */
    public function keluar(): Response
    {
        $obats = Obat::whereHas('batches', fn ($b) => $b->where('stok', '>', 0))
            ->with(['activeBatches.supplier', 'kategori'])
            ->orderBy('nama_obat', 'asc')
            ->get();

        return Inertia::render('Stok/Keluar', [
            'obats' => $obats,
        ]);
    }

    /**
     * Store outgoing stock and deduct batch stock.
     */
    public function storeKeluar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'batch_obat_id' => 'required|exists:batch_obats,id',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:500',
        ], [
            'batch_obat_id.required' => 'Batch obat wajib dipilih.',
            'tanggal.required' => 'Tanggal pengeluaran wajib diisi.',
            'jumlah.required' => 'Jumlah obat keluar wajib diisi minimal 1.',
            'keterangan.required' => 'Keterangan pengeluaran obat wajib diisi (misal: Resep harian, pemakaian poli).',
        ]);

        $batch = BatchObat::findOrFail($validated['batch_obat_id']);

        if ($batch->stok < $validated['jumlah']) {
            return back()->withErrors([
                'jumlah' => "Jumlah yang diminta ({$validated['jumlah']}) melebihi sisa stok batch ({$batch->stok}).",
            ])->withInput();
        }

        DB::transaction(function () use ($batch, $validated, $request) {
            $stokSebelum = $batch->stok;
            $stokSesudah = $stokSebelum - $validated['jumlah'];

            $batch->update([
                'stok' => $stokSesudah,
            ]);

            StokMovement::create([
                'batch_obat_id' => $batch->id,
                'user_id' => $request->user()->id,
                'tipe' => 'keluar',
                'jumlah' => $validated['jumlah'],
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'],
            ]);
        });

        return redirect()->route('stok.index')->with('success', 'Stok keluar berhasil dicatat.');
    }
}
