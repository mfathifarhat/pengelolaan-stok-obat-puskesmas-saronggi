import type { User } from './auth';

export interface KategoriObat {
    id: number;
    nama: string;
    keterangan?: string | null;
    obats_count?: number;
    created_at?: string;
    updated_at?: string;
}

export interface Supplier {
    id: number;
    nama: string;
    alamat?: string | null;
    telepon?: string | null;
    kontak_person?: string | null;
    batch_obats_count?: number;
    created_at?: string;
    updated_at?: string;
}

export interface BatchObat {
    id: number;
    obat_id: number;
    supplier_id?: number | null;
    no_batch: string;
    tanggal_kadaluarsa: string;
    tanggal_masuk: string;
    harga_beli: number;
    stok: number;
    status_kadaluarsa: 'safe' | 'warning' | 'expired';
    obat?: Obat;
    supplier?: Supplier;
    created_at?: string;
    updated_at?: string;
}

export interface Obat {
    id: number;
    kode_obat: string;
    nama_obat: string;
    kategori_obat_id: number;
    satuan: string;
    produsen?: string | null;
    stok_minimum: number;
    keterangan?: string | null;
    total_stok: number;
    kategori?: KategoriObat;
    batches?: BatchObat[];
    active_batches?: BatchObat[];
    created_at?: string;
    updated_at?: string;
}

export interface StokMovement {
    id: number;
    batch_obat_id: number;
    user_id: number;
    tipe: 'masuk' | 'keluar';
    jumlah: number;
    stok_sebelum: number;
    stok_sesudah: number;
    tanggal: string;
    keterangan?: string | null;
    batch_obat?: BatchObat;
    user?: User;
    created_at?: string;
    updated_at?: string;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}
