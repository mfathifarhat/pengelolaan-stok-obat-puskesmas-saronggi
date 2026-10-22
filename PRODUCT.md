# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Petugas Farmasi / Gudang Obat**: Bertugas menerima pasokan obat dari supplier (PBF), mendata nomor batch dan tanggal kadaluarsa, mencatat mutasi stok masuk dan keluar harian, serta memantau ketersediaan stok fisik di rak obat puskesmas.
- **Admin / Kepala Puskesmas / Manajer**: Bertanggung jawab memonitor ketersediaan stok obat secara keseluruhan, mengevaluasi laporan mutasi dan penggunaan obat berkala, mengelola akun pengguna, dan mengunduh laporan resmi untuk pertanggungjawaban dinas.

## Product Purpose

Menyediakan sistem informasi pengelolaan stok obat berbasis web yang akurat, real-time, dan mudah digunakan di Puskesmas Saronggi. Aplikasi ini mencegah kekosongan obat kritis, meminimalkan kerugian akibat obat kadaluarsa melalui sistem batch FEFO (First Expired, First Out), serta mempercepat pelaporan inventaris berkala.

## Positioning

Sistem inventaris obat terfokus puskesmas tingkat pertama yang mengintegrasikan pelacakan multi-batch dengan prinsip FEFO tanpa beban kompleksitas rekam medis pasien, didesain responsif untuk operasional meja administrasi maupun inspeksi fisik di rak gudang obat.

## Operating Context

- **Lingkungan Operasional**: Ruang farmasi, apotek, dan gudang obat Puskesmas Saronggi.
- **Perangkat**: Komputer desktop/laptop di meja administrasi farmasi dan perangkat portabel (tablet/smartphone) untuk pengecekan fisik langsung di rak gudang.
- **Ritual & Alur Kerja**: 
  - Penerimaan kiriman obat dari PBF (Supplier) dengan pengecekan nomor batch, tanggal kadaluarsa, dan kuantitas.
  - Pengeluaran obat harian (pemakaian rutin, resep harian umum) dengan pemilihan batch kadaluarsa terdekat.
  - Rekapitulasi berkala dan pelaporan resmi bertanda tangan pimpinan puskesmas.

## Capabilities and Constraints

- **Autentikasi & Otorisasi**:
  - Role-based access control (Admin & Petugas).
  - Admin memiliki akses penuh termasuk Kelola Pengguna dan Laporan.
  - Petugas memiliki akses operasional (Master Obat, Kategori Obat, Supplier, Stok Masuk/Keluar).
- **Master Data**:
  - Obat (kode obat unik, nama, produsen, kategori FK, satuan, batas stok minimum, keterangan).
  - Kategori Obat (lookup table dikelola via modal interaktif di halaman Obat).
  - Supplier / PBF (nama, alamat, no. telepon, kontak person).
- **Pengelolaan Stok Multi-Batch**:
  - Stok obat dihitung dari agregat batch aktif.
  - Setiap batch mencatat: nomor batch, tanggal masuk, tanggal kadaluarsa, supplier, harga beli, dan kuantitas stok sisa.
  - Stok keluar mengedepankan FEFO (menampilkan daftar batch aktif diurutkan berdasarkan tanggal kadaluarsa terdekat).
  - Peringatan stok kritis (< stok minimum) dan peringatan kadaluarsa (< 3 bulan).
- **Pelaporan & Ekspor**:
  - Filter laporan berdasarkan rentang tanggal, jenis mutasi, dan obat tertentu.
  - Ekspor dokumen siap cetak / PDF (dengan ruang tanda tangan pejabat/penanggung jawab).
  - Ekspor format Excel / Spreadsheet untuk analisis data inventaris lanjutan.
- **Batasan**:
  - Tidak menyimpan data identitas pasien (fokus pada inventaris & logistik obat puskesmas).
  - Pengeluaran obat dicatat secara agregat/umum (resep harian/pemakaian berkala).

## Brand Commitments

- **Nama Aplikasi**: SIMStok Puskesmas Saronggi (Sistem Informasi Manajemen Stok Obat).
- **Entitas**: Puskesmas Saronggi, Dinas Kesehatan Kabupaten Sumenep.
- **Bahasa**: Bahasa Indonesia untuk seluruh antarmuka, terminologi farmasi standar puskesmas, dan pesan sistem.

## Evidence on Hand

- Desain basis data dan model relasional di `implementation-plan.md`.
- Arsitektur aplikasi Laravel 13, Fortify, Inertia.js Vue 3, Tailwind CSS 4.

## Product Principles

1. **Akurasi Data Fisik vs Sistem**: Setiap pergerakan stok harus memiliki jejak audit jelas (waktu, pelaku, nomor batch, stok sebelum dan sesudah).
2. **Prioritas FEFO untuk Keamanan Obat**: Alur pengeluaran obat selalu memprioritaskan batch dengan tanggal kadaluarsa paling dekat.
3. **Efisiensi Input di Lapangan**: Antarmuka responsif dan ringkas, memudahkan penginputan batch dan stok saat berada di rak gudang.
4. **Kejelasan Visibilitas Stok Kritis**: Status obat menipis dan mendekati kadaluarsa harus terlihat seketika pada dashboard tanpa memerlukan penggalian laporan rumit.

## Accessibility & Inclusion

- Kontras warna yang jelas untuk status stok (aman, menipis, kritis, kadaluarsa).
- Touch target memadai pada elemen form dan tabel saat diakses melalui perangkat tablet di gudang.
