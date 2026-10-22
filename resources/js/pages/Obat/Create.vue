<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { KategoriObat } from '@/types';

defineProps<{
    kategoriList: KategoriObat[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Data Obat', href: '/obat' },
            { title: 'Tambah Obat', href: '/obat/create' },
        ],
    },
});

const form = useForm({
    kode_obat: '',
    nama_obat: '',
    kategori_obat_id: '',
    satuan: 'Tablet',
    produsen: '',
    stok_minimum: 50,
    keterangan: '',
});

const satuanOptions = [
    'Tablet',
    'Kapsul',
    'Tablet Kunyah',
    'Sirup / Botol',
    'Salep / Tube',
    'Tetes / Botol',
    'Injeksi / Ampul',
    'Injeksi / Vial',
    'Sachet',
    'Suppositoria',
];

const submit = () => {
    form.post('/obat');
};
</script>

<template>
    <Head title="Tambah Data Obat - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 max-w-3xl">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="sm" class="size-8 p-0">
                <Link href="/obat">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">
                    Tambah Obat Baru
                </h1>
                <p class="text-xs text-muted-foreground">
                    Daftarkan master identitas obat baru ke dalam katalog Puskesmas Saronggi.
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="rounded-xl border border-sidebar-border bg-card p-5 shadow-xs space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Kode Obat -->
                <div class="space-y-1.5">
                    <Label for="kode_obat" class="text-xs">Kode Obat *</Label>
                    <Input
                        id="kode_obat"
                        v-model="form.kode_obat"
                        placeholder="Contoh: OBT-011"
                        class="text-xs"
                        required
                    />
                    <p v-if="form.errors.kode_obat" class="text-[11px] text-destructive">{{ form.errors.kode_obat }}</p>
                </div>

                <!-- Nama Obat -->
                <div class="space-y-1.5">
                    <Label for="nama_obat" class="text-xs">Nama Obat & Dosis *</Label>
                    <Input
                        id="nama_obat"
                        v-model="form.nama_obat"
                        placeholder="Contoh: Paracetamol 500 mg"
                        class="text-xs"
                        required
                    />
                    <p v-if="form.errors.nama_obat" class="text-[11px] text-destructive">{{ form.errors.nama_obat }}</p>
                </div>

                <!-- Kategori Obat -->
                <div class="space-y-1.5">
                    <Label for="kategori_obat_id" class="text-xs">Kategori Golongan *</Label>
                    <select
                        id="kategori_obat_id"
                        v-model="form.kategori_obat_id"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        required
                    >
                        <option value="" disabled>Pilih Kategori Golongan</option>
                        <option v-for="kat in kategoriList" :key="kat.id" :value="kat.id">
                            {{ kat.nama }}
                        </option>
                    </select>
                    <p v-if="form.errors.kategori_obat_id" class="text-[11px] text-destructive">{{ form.errors.kategori_obat_id }}</p>
                </div>

                <!-- Satuan -->
                <div class="space-y-1.5">
                    <Label for="satuan" class="text-xs">Bentuk / Satuan *</Label>
                    <div class="flex gap-2">
                        <select
                            id="satuan"
                            v-model="form.satuan"
                            class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option v-for="s in satuanOptions" :key="s" :value="s">
                                {{ s }}
                            </option>
                        </select>
                    </div>
                    <p v-if="form.errors.satuan" class="text-[11px] text-destructive">{{ form.errors.satuan }}</p>
                </div>

                <!-- Produsen / Pabrik -->
                <div class="space-y-1.5">
                    <Label for="produsen" class="text-xs">Pabrik / Produsen</Label>
                    <Input
                        id="produsen"
                        v-model="form.produsen"
                        placeholder="Contoh: Kimia Farma, Kalbe Farma, Sanbe"
                        class="text-xs"
                    />
                    <p v-if="form.errors.produsen" class="text-[11px] text-destructive">{{ form.errors.produsen }}</p>
                </div>

                <!-- Batas Stok Minimum -->
                <div class="space-y-1.5">
                    <Label for="stok_minimum" class="text-xs">Batas Stok Minimum *</Label>
                    <Input
                        id="stok_minimum"
                        type="number"
                        min="0"
                        v-model="form.stok_minimum"
                        class="text-xs"
                        required
                    />
                    <p class="text-[11px] text-muted-foreground">Peringatan stok kritis aktif jika total stok &le; angka ini.</p>
                    <p v-if="form.errors.stok_minimum" class="text-[11px] text-destructive">{{ form.errors.stok_minimum }}</p>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <Label for="keterangan" class="text-xs">Keterangan / Indikasi Khusus</Label>
                <textarea
                    id="keterangan"
                    v-model="form.keterangan"
                    rows="3"
                    placeholder="Catatan tambahan obat (aturan pakai umum, peruntukan poli, dll)"
                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                ></textarea>
                <p v-if="form.errors.keterangan" class="text-[11px] text-destructive">{{ form.errors.keterangan }}</p>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-border/60">
                <Button as-child variant="outline" size="sm" class="text-xs">
                    <Link href="/obat">Batal</Link>
                </Button>
                <Button type="submit" size="sm" class="text-xs" :disabled="form.processing">
                    <Save class="mr-1.5 size-4" />
                    Simpan Data Obat
                </Button>
            </div>
        </form>
    </div>
</template>
