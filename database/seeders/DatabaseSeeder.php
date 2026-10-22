<?php

namespace Database\Seeders;

use App\Models\BatchObat;
use App\Models\KategoriObat;
use App\Models\Obat;
use App\Models\StokMovement;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Puskesmas',
                'username' => 'admin',
                'email' => 'admin@puskesmas-saronggi.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $petugas = User::firstOrCreate(
            ['username' => 'petugas'],
            [
                'name' => 'Petugas Farmasi',
                'username' => 'petugas',
                'email' => 'petugas@puskesmas-saronggi.id',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'email_verified_at' => now(),
            ]
        );

        // 2. Kategori Obat
        $kategoriList = [
            ['nama' => 'Analgesik & Antipiretik', 'keterangan' => 'Pereda nyeri dan penurun demam'],
            ['nama' => 'Antibiotik', 'keterangan' => 'Pengobatan infeksi akibat bakteri'],
            ['nama' => 'Antihistamin & Antialergi', 'keterangan' => 'Pereda reaksi dan gejala alergi'],
            ['nama' => 'Antasida & Saluran Cerna', 'keterangan' => 'Obat gangguan lambung dan pencernaan'],
            ['nama' => 'Obat Batuk & Flu', 'keterangan' => 'Pereda batuk, pilek, dan sesak'],
            ['nama' => 'Vitamin & Suplemen', 'keterangan' => 'Suplemen nutrisi dan peningkat daya tahan tubuh'],
            ['nama' => 'Obat Luar & Topikal', 'keterangan' => 'Salep kulit, krim, dan antiseptik luar'],
            ['nama' => 'Antihipertensi', 'keterangan' => 'Pengontrol tekanan darah tinggi'],
        ];

        $kategoriModels = [];
        foreach ($kategoriList as $kat) {
            $kategoriModels[$kat['nama']] = KategoriObat::firstOrCreate(
                ['nama' => $kat['nama']],
                ['keterangan' => $kat['keterangan']]
            );
        }

        // 3. Suppliers
        $suppliersData = [
            [
                'nama' => 'PT Kimia Farma Trading & Distribution',
                'alamat' => 'Jl. Trunojoyo No. 45, Pamekasan, Madura',
                'telepon' => '0324-322110',
                'kontak_person' => 'Bambang Supriyanto',
            ],
            [
                'nama' => 'PT Enseval Putera Megatrading Tbk',
                'alamat' => 'Kawasan Industri Rungkut, Surabaya',
                'telepon' => '031-8439900',
                'kontak_person' => 'Dewi Lestari',
            ],
            [
                'nama' => 'PT Anugrah Argon Medica (AAM)',
                'alamat' => 'Jl. Berbek Industri I No. 12, Sidoarjo',
                'telepon' => '031-8671234',
                'kontak_person' => 'Hendra Setiawan',
            ],
        ];

        $supplierModels = [];
        foreach ($suppliersData as $sup) {
            $supplierModels[] = Supplier::firstOrCreate(
                ['nama' => $sup['nama']],
                $sup
            );
        }

        // 4. Data Obat Esensial Puskesmas
        $obatsData = [
            [
                'kode_obat' => 'OBT-001',
                'nama_obat' => 'Paracetamol 500 mg',
                'kategori' => 'Analgesik & Antipiretik',
                'satuan' => 'Tablet',
                'produsen' => 'Kimia Farma',
                'stok_minimum' => 100,
                'keterangan' => 'Pereda demam dan nyeri ringan hingga sedang',
            ],
            [
                'kode_obat' => 'OBT-002',
                'nama_obat' => 'Amoxicillin 500 mg',
                'kategori' => 'Antibiotik',
                'satuan' => 'Kapsul',
                'produsen' => 'Bernofarm',
                'stok_minimum' => 80,
                'keterangan' => 'Antibiotik spektrum luas untuk infeksi bakteri',
            ],
            [
                'kode_obat' => 'OBT-003',
                'nama_obat' => 'Cefadroxil 500 mg',
                'kategori' => 'Antibiotik',
                'satuan' => 'Kapsul',
                'produsen' => 'Dexa Medica',
                'stok_minimum' => 50,
                'keterangan' => 'Antibiotik sefalosporin generasi pertama',
            ],
            [
                'kode_obat' => 'OBT-004',
                'nama_obat' => 'Cetirizine 10 mg',
                'kategori' => 'Antihistamin & Antialergi',
                'satuan' => 'Tablet',
                'produsen' => 'Kalbe Farma',
                'stok_minimum' => 60,
                'keterangan' => 'Antihistamin untuk rinitis alergi dan urtikaria',
            ],
            [
                'kode_obat' => 'OBT-005',
                'nama_obat' => 'Antasida DOEN',
                'kategori' => 'Antasida & Saluran Cerna',
                'satuan' => 'Tablet Kunyah',
                'produsen' => 'Kimia Farma',
                'stok_minimum' => 100,
                'keterangan' => 'Mengurangi asam lambung berlebih dan tukak lambung',
            ],
            [
                'kode_obat' => 'OBT-006',
                'nama_obat' => 'Ambroxol 30 mg',
                'kategori' => 'Obat Batuk & Flu',
                'satuan' => 'Tablet',
                'produsen' => 'Kimia Farma',
                'stok_minimum' => 50,
                'keterangan' => 'Mukolitik pengencer dahak pada batuk produktif',
            ],
            [
                'kode_obat' => 'OBT-007',
                'nama_obat' => 'Vitamin C 50 mg',
                'kategori' => 'Vitamin & Suplemen',
                'satuan' => 'Tablet',
                'produsen' => 'Ikimu',
                'stok_minimum' => 150,
                'keterangan' => 'Suplemen vitamin C untuk pemulihan dan imunitas',
            ],
            [
                'kode_obat' => 'OBT-008',
                'nama_obat' => 'Vitamin B Kompleks',
                'kategori' => 'Vitamin & Suplemen',
                'satuan' => 'Tablet',
                'produsen' => 'Kimia Farma',
                'stok_minimum' => 120,
                'keterangan' => 'Menjaga metabolisme tubuh dan sistem saraf',
            ],
            [
                'kode_obat' => 'OBT-009',
                'nama_obat' => 'Amlodipine 10 mg',
                'kategori' => 'Antihipertensi',
                'satuan' => 'Tablet',
                'produsen' => 'Dexa Medica',
                'stok_minimum' => 60,
                'keterangan' => 'Pengontrol hipertensi harian pasien prolanis',
            ],
            [
                'kode_obat' => 'OBT-0010',
                'nama_obat' => 'Hidrokortison 1% Salep',
                'kategori' => 'Obat Luar & Topikal',
                'satuan' => 'Tube',
                'produsen' => 'Kimia Farma',
                'stok_minimum' => 20,
                'keterangan' => 'Salep kortikosteroid topikal untuk dermatitis/alergi kulit',
            ],
        ];

        $obatModels = [];
        foreach ($obatsData as $item) {
            $kategoriId = $kategoriModels[$item['kategori']]->id;
            $obatModels[$item['kode_obat']] = Obat::firstOrCreate(
                ['kode_obat' => $item['kode_obat']],
                [
                    'nama_obat' => $item['nama_obat'],
                    'kategori_obat_id' => $kategoriId,
                    'satuan' => $item['satuan'],
                    'produsen' => $item['produsen'],
                    'stok_minimum' => $item['stok_minimum'],
                    'keterangan' => $item['keterangan'],
                ]
            );
        }

        // 5. Batches & Initial Movements
        $today = Carbon::today();

        $batchesData = [
            // Paracetamol: Batch 1 (Aman), Batch 2 (Mendekati ED - warning)
            [
                'obat_kode' => 'OBT-001',
                'supplier_idx' => 0,
                'no_batch' => 'PCT-2401-A',
                'tanggal_masuk' => $today->copy()->subMonths(3),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(18),
                'harga_beli' => 250,
                'stok_awal' => 500,
                'stok_sekarang' => 350,
            ],
            [
                'obat_kode' => 'OBT-001',
                'supplier_idx' => 0,
                'no_batch' => 'PCT-2311-B',
                'tanggal_masuk' => $today->copy()->subMonths(8),
                'tanggal_kadaluarsa' => $today->copy()->addDays(45), // Peringatan < 3 bulan
                'harga_beli' => 230,
                'stok_awal' => 200,
                'stok_sekarang' => 45,
            ],
            // Amoxicillin: Batch 1
            [
                'obat_kode' => 'OBT-002',
                'supplier_idx' => 1,
                'no_batch' => 'AMX-2403-01',
                'tanggal_masuk' => $today->copy()->subMonths(2),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(14),
                'harga_beli' => 600,
                'stok_awal' => 300,
                'stok_sekarang' => 180,
            ],
            // Cefadroxil: Stok rendah (dibawah minimum 50)
            [
                'obat_kode' => 'OBT-003',
                'supplier_idx' => 2,
                'no_batch' => 'CFD-2402-12',
                'tanggal_masuk' => $today->copy()->subMonths(4),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(10),
                'harga_beli' => 1200,
                'stok_awal' => 100,
                'stok_sekarang' => 25, // Kritis < 50
            ],
            // Cetirizine
            [
                'obat_kode' => 'OBT-004',
                'supplier_idx' => 1,
                'no_batch' => 'CTZ-2405-09',
                'tanggal_masuk' => $today->copy()->subMonths(1),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(20),
                'harga_beli' => 350,
                'stok_awal' => 200,
                'stok_sekarang' => 140,
            ],
            // Antasida DOEN
            [
                'obat_kode' => 'OBT-005',
                'supplier_idx' => 0,
                'no_batch' => 'ATD-2404-03',
                'tanggal_masuk' => $today->copy()->subMonths(2),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(16),
                'harga_beli' => 200,
                'stok_awal' => 400,
                'stok_sekarang' => 220,
            ],
            // Ambroxol: Mendekati kadaluarsa
            [
                'obat_kode' => 'OBT-006',
                'supplier_idx' => 0,
                'no_batch' => 'AMB-2310-77',
                'tanggal_masuk' => $today->copy()->subMonths(9),
                'tanggal_kadaluarsa' => $today->copy()->addDays(60), // Peringatan < 3 bulan
                'harga_beli' => 220,
                'stok_awal' => 150,
                'stok_sekarang' => 60,
            ],
            // Vitamin C: Banyak stok
            [
                'obat_kode' => 'OBT-007',
                'supplier_idx' => 1,
                'no_batch' => 'VTC-2406-01',
                'tanggal_masuk' => $today->copy()->subWeeks(3),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(24),
                'harga_beli' => 150,
                'stok_awal' => 1000,
                'stok_sekarang' => 850,
            ],
            // Vitamin B Kompleks
            [
                'obat_kode' => 'OBT-008',
                'supplier_idx' => 0,
                'no_batch' => 'VTB-2405-55',
                'tanggal_masuk' => $today->copy()->subMonth(),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(15),
                'harga_beli' => 180,
                'stok_awal' => 500,
                'stok_sekarang' => 380,
            ],
            // Amlodipine 10 mg
            [
                'obat_kode' => 'OBT-009',
                'supplier_idx' => 2,
                'no_batch' => 'AML-2403-88',
                'tanggal_masuk' => $today->copy()->subMonths(3),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(12),
                'harga_beli' => 450,
                'stok_awal' => 300,
                'stok_sekarang' => 190,
            ],
            // Hidrokortison Salep
            [
                'obat_kode' => 'OBT-0010',
                'supplier_idx' => 0,
                'no_batch' => 'HDK-2404-11',
                'tanggal_masuk' => $today->copy()->subMonths(2),
                'tanggal_kadaluarsa' => $today->copy()->addMonths(18),
                'harga_beli' => 4500,
                'stok_awal' => 50,
                'stok_sekarang' => 32,
            ],
        ];

        foreach ($batchesData as $bData) {
            $obat = $obatModels[$bData['obat_kode']];
            $supplier = $supplierModels[$bData['supplier_idx']];

            $batch = BatchObat::firstOrCreate(
                [
                    'obat_id' => $obat->id,
                    'no_batch' => $bData['no_batch'],
                ],
                [
                    'supplier_id' => $supplier->id,
                    'tanggal_masuk' => $bData['tanggal_masuk'],
                    'tanggal_kadaluarsa' => $bData['tanggal_kadaluarsa'],
                    'harga_beli' => $bData['harga_beli'],
                    'stok' => $bData['stok_sekarang'],
                ]
            );

            // Record stok masuk awal
            StokMovement::firstOrCreate(
                [
                    'batch_obat_id' => $batch->id,
                    'tipe' => 'masuk',
                    'tanggal' => $bData['tanggal_masuk']->toDateString(),
                ],
                [
                    'user_id' => $petugas->id,
                    'jumlah' => $bData['stok_awal'],
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $bData['stok_awal'],
                    'keterangan' => 'Penerimaan pasokan perdana dari ' . $supplier->nama,
                ]
            );

            // Record pemakaian/stok keluar jika ada perbedaan
            $keluar = $bData['stok_awal'] - $bData['stok_sekarang'];
            if ($keluar > 0) {
                StokMovement::firstOrCreate(
                    [
                        'batch_obat_id' => $batch->id,
                        'tipe' => 'keluar',
                        'tanggal' => $bData['tanggal_masuk']->copy()->addDays(15)->toDateString(),
                    ],
                    [
                        'user_id' => $petugas->id,
                        'jumlah' => $keluar,
                        'stok_sebelum' => $bData['stok_awal'],
                        'stok_sesudah' => $bData['stok_sekarang'],
                        'keterangan' => 'Pelayanan resep harian & distribusi rutin puskesmas',
                    ]
                );
            }
        }
    }
}
