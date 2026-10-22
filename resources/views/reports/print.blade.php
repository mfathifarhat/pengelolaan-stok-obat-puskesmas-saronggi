<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Stok Obat - UPTD Puskesmas Saronggi</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            margin: 0;
            padding: 20px;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: normal;
            letter-spacing: 0.5px;
        }
        .header h2 {
            margin: 4px 0;
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header p {
            margin: 0;
            font-size: 9pt;
            color: #333;
        }
        .title-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-section h4 {
            margin: 0;
            font-size: 13pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title-section p {
            margin: 4px 0 0;
            font-size: 10pt;
            color: #444;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9.5pt;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-masuk {
            font-weight: bold;
            color: #047857;
        }
        .badge-keluar {
            font-weight: bold;
            color: #b91c1c;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 250px;
            text-align: center;
            font-size: 10pt;
        }
        .sig-space {
            height: 70px;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background: #0284c7;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 10pt;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 10mm; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <h3>PEMERINTAH KABUPATEN SUMENEP</h3>
        <h3>DINAS KESEHATAN PENGENDALIAN PENDUDUK DAN KB</h3>
        <h2>UPTD PUSKESMAS SARONGGI</h2>
        <p>Jl. Raya Saronggi No. 12, Saronggi, Kabupaten Sumenep, Jawa Timur 69467</p>
    </div>

    <div class="title-section">
        <h4>Laporan Rekapitulasi Mutasi Stok Obat</h4>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong></p>
        @if($tipe)
            <p>Jenis Mutasi: <strong>{{ strtoupper($tipe) }}</strong></p>
        @endif
    </div>

    <div class="summary-box">
        <div><strong>Total Stok Masuk:</strong> {{ number_format($totalMasuk) }} unit</div>
        <div><strong>Total Stok Keluar:</strong> {{ number_format($totalKeluar) }} unit</div>
        <div><strong>Jumlah Baris Transaksi:</strong> {{ count($movements) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 85px;">Tanggal</th>
                <th>Kode & Nama Obat</th>
                <th>No. Batch</th>
                <th style="width: 85px;">Kadaluarsa</th>
                <th>Supplier / PBF</th>
                <th style="width: 60px;">Tipe</th>
                <th style="width: 50px;">Jml</th>
                <th style="width: 60px;">Sisa</th>
                <th>Petugas</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $idx => $m)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $m->tanggal ? $m->tanggal->format('d/m/Y') : '-' }}</td>
                    <td>
                        <strong>{{ $m->batchObat->obat->kode_obat ?? '-' }}</strong><br>
                        {{ $m->batchObat->obat->nama_obat ?? '-' }}
                    </td>
                    <td class="text-center">{{ $m->batchObat->no_batch ?? '-' }}</td>
                    <td class="text-center">{{ $m->batchObat->tanggal_kadaluarsa ? $m->batchObat->tanggal_kadaluarsa->format('d/m/Y') : '-' }}</td>
                    <td>{{ $m->batchObat->supplier->nama ?? '-' }}</td>
                    <td class="text-center">
                        <span class="{{ $m->tipe === 'masuk' ? 'badge-masuk' : 'badge-keluar' }}">
                            {{ strtoupper($m->tipe) }}
                        </span>
                    </td>
                    <td class="text-right"><strong>{{ number_format($m->jumlah) }}</strong></td>
                    <td class="text-right">{{ number_format($m->stok_sesudah) }}</td>
                    <td>{{ $m->user->name ?? '-' }}</td>
                    <td>{{ $m->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada data mutasi stok pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <p>Mengetahui,<br><strong>Kepala UPTD Puskesmas Saronggi</strong></p>
            <div class="sig-space"></div>
            <p><u>( .................................................... )</u><br>NIP. ..........................................</p>
        </div>
        <div class="sig-box">
            <p>Saronggi, {{ $todayDate }}<br><strong>Pengelola Obat / Petugas Farmasi</strong></p>
            <div class="sig-space"></div>
            <p><u>{{ auth()->user()->name ?? '( .................................................... )' }}</u><br>Petugas Farmasi Puskesmas</p>
        </div>
    </div>

</body>
</html>
