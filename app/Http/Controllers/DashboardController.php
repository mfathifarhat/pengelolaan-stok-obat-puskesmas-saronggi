<?php

namespace App\Http\Controllers;

use App\Models\BatchObat;
use App\Models\Obat;
use App\Models\StokMovement;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();
        $threeMonthsFromNow = $today->copy()->addDays(90);

        // 1. Total Jenis Obat
        $totalObat = Obat::count();

        // 2. Obat dengan Stok Kritis (total_stok <= stok_minimum)
        $allObat = Obat::with('batches')->get();
        $obatKritisList = $allObat->filter(function ($obat) {
            return $obat->total_stok <= $obat->stok_minimum;
        })->values();
        $totalObatKritis = $obatKritisList->count();

        // 3. Batch Mendekati Kadaluarsa (< 90 hari) dan masih memiliki stok
        $batchExpiringQuery = BatchObat::with(['obat', 'supplier'])
            ->where('stok', '>', 0)
            ->where('tanggal_kadaluarsa', '<=', $threeMonthsFromNow)
            ->orderBy('tanggal_kadaluarsa', 'asc');

        $totalBatchExpiring = (clone $batchExpiringQuery)->count();
        $expiringBatches = (clone $batchExpiringQuery)->take(5)->get();

        // 4. Mutasi Bulan Ini
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $stokMasukBulanIni = StokMovement::where('tipe', 'masuk')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $stokKeluarBulanIni = StokMovement::where('tipe', 'keluar')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        // 5. Transaksi Mutasi Terakhir
        $recentMovements = StokMovement::with(['batchObat.obat', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_obat' => $totalObat,
                'total_kritis' => $totalObatKritis,
                'total_expiring' => $totalBatchExpiring,
                'stok_masuk_bulan_ini' => (int) $stokMasukBulanIni,
                'stok_keluar_bulan_ini' => (int) $stokKeluarBulanIni,
            ],
            'kritisList' => $obatKritisList->take(5),
            'expiringBatches' => $expiringBatches,
            'recentMovements' => $recentMovements,
        ]);
    }
}
