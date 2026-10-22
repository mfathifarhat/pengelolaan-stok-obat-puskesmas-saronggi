<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StokMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Display report dashboard with filters and summary.
     */
    public function index(Request $request): Response
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $tipe = $request->query('tipe');
        $obatId = $request->query('obat_id');

        $query = StokMovement::with(['batchObat.obat.kategori', 'batchObat.supplier', 'user'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->when($tipe, fn ($q) => $q->where('tipe', $tipe))
            ->when($obatId, function ($q) use ($obatId) {
                $q->whereHas('batchObat', fn ($b) => $b->where('obat_id', $obatId));
            });

        $movements = (clone $query)->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

        // Calculate summaries
        $totalMasuk = $movements->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $movements->where('tipe', 'keluar')->sum('jumlah');

        $nilaiMasuk = $movements->where('tipe', 'masuk')->sum(function ($m) {
            return $m->jumlah * ($m->batchObat->harga_beli ?? 0);
        });

        $obats = Obat::orderBy('nama_obat', 'asc')->get(['id', 'nama_obat', 'kode_obat']);

        return Inertia::render('Laporan/Index', [
            'movements' => $movements,
            'obats' => $obats,
            'summary' => [
                'total_masuk' => (int) $totalMasuk,
                'total_keluar' => (int) $totalKeluar,
                'nilai_masuk' => (float) $nilaiMasuk,
                'total_transaksi' => $movements->count(),
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tipe' => $tipe ?? '',
                'obat_id' => $obatId ?? '',
            ],
        ]);
    }

    /**
     * Official printable / PDF view with Puskesmas letterhead and signature blocks.
     */
    public function printView(Request $request): HttpResponse
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $tipe = $request->query('tipe');
        $obatId = $request->query('obat_id');

        $query = StokMovement::with(['batchObat.obat.kategori', 'batchObat.supplier', 'user'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->when($tipe, fn ($q) => $q->where('tipe', $tipe))
            ->when($obatId, function ($q) use ($obatId) {
                $q->whereHas('batchObat', fn ($b) => $b->where('obat_id', $obatId));
            });

        $movements = $query->orderBy('tanggal', 'asc')->get();

        $totalMasuk = $movements->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $movements->where('tipe', 'keluar')->sum('jumlah');

        return response()->view('reports.print', [
            'movements' => $movements,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tipe' => $tipe,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'todayDate' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }

    /**
     * Export report data as Excel-compatible CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $tipe = $request->query('tipe');
        $obatId = $request->query('obat_id');

        $query = StokMovement::with(['batchObat.obat', 'batchObat.supplier', 'user'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->when($tipe, fn ($q) => $q->where('tipe', $tipe))
            ->when($obatId, function ($q) use ($obatId) {
                $q->whereHas('batchObat', fn ($b) => $b->where('obat_id', $obatId));
            });

        $movements = $query->orderBy('tanggal', 'asc')->get();

        $filename = 'Laporan_Mutasi_Stok_Puskesmas_Saronggi_' . $startDate . '_sd_' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($movements) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Row
            fputcsv($handle, [
                'No',
                'Tanggal',
                'Kode Obat',
                'Nama Obat',
                'No Batch',
                'Tanggal Kadaluarsa',
                'Supplier',
                'Jenis Mutasi',
                'Jumlah',
                'Stok Sebelum',
                'Stok Sesudah',
                'Petugas',
                'Keterangan',
            ]);

            foreach ($movements as $idx => $m) {
                fputcsv($handle, [
                    $idx + 1,
                    $m->tanggal ? $m->tanggal->format('d/m/Y') : '-',
                    $m->batchObat->obat->kode_obat ?? '-',
                    $m->batchObat->obat->nama_obat ?? '-',
                    $m->batchObat->no_batch ?? '-',
                    $m->batchObat->tanggal_kadaluarsa ? $m->batchObat->tanggal_kadaluarsa->format('d/m/Y') : '-',
                    $m->batchObat->supplier->nama ?? '-',
                    strtoupper($m->tipe),
                    $m->jumlah,
                    $m->stok_sebelum,
                    $m->stok_sesudah,
                    $m->user->name ?? '-',
                    $m->keterangan ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
