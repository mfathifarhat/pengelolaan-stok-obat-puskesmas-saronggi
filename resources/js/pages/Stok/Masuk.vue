<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, PackagePlus, Save } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Obat, Supplier } from '@/types';

const props = defineProps<{
    obats: Obat[];
    suppliers: Supplier[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Mutasi Stok', href: '/stok' },
            { title: 'Catat Stok Masuk', href: '/stok/masuk' },
        ],
    },
});

const todayStr = new Date().toISOString().split('T')[0];

const form = useForm({
    obat_id: '',
    supplier_id: '',
    no_batch: '',
    tanggal_masuk: todayStr,
    tanggal_kadaluarsa: '',
    harga_beli: 0,
    jumlah: 100,
    keterangan: '',
});

const selectedObat = computed(() => {
    return props.obats.find((o) => String(o.id) === String(form.obat_id));
});

const submit = () => {
    form.post('/stok/masuk');
};
</script>

<template>
    <Head title="Penerimaan Stok Obat Masuk - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 max-w-3xl">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="sm" class="size-8 p-0">
                <Link href="/stok">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">
                    Catat Penerimaan Stok Masuk (Batch Baru)
                </h1>
                <p class="text-xs text-muted-foreground">
                    Input pasokan obat dari distributor, nomor batch, dan tanggal kadaluarsa (ED).
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="rounded-xl border border-sidebar-border bg-card p-5 shadow-xs space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Pilih Obat -->
                <div class="space-y-1.5 sm:col-span-2">
                    <Label for="obat_id" class="text-xs">Pilih Obat *</Label>
                    <select
                        id="obat_id"
                        v-model="form.obat_id"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        required
                    >
                        <option value="" disabled>-- Pilih Obat dari Katalog --</option>
                        <option v-for="o in obats" :key="o.id" :value="o.id">
                            {{ o.nama_obat }} ({{ o.kode_obat }}) - {{ o.satuan }}
                        </option>
                    </select>
                    <p v-if="selectedObat" class="text-[11px] text-muted-foreground">
                        Kategori: <span class="font-medium text-foreground">{{ selectedObat.kategori?.nama }}</span> &bull;
                        Produsen: <span class="font-medium text-foreground">{{ selectedObat.produsen || '-' }}</span>
                    </p>
                    <p v-if="form.errors.obat_id" class="text-[11px] text-destructive">{{ form.errors.obat_id }}</p>
                </div>

                <!-- Supplier / PBF -->
                <div class="space-y-1.5">
                    <Label for="supplier_id" class="text-xs">Supplier / PBF Pengirim</Label>
                    <select
                        id="supplier_id"
                        v-model="form.supplier_id"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        <option value="">-- Tanpa Supplier / Sumber Lain --</option>
                        <option v-for="s in suppliers" :key="s.id" :value="s.id">
                            {{ s.nama }}
                        </option>
                    </select>
                    <p v-if="form.errors.supplier_id" class="text-[11px] text-destructive">{{ form.errors.supplier_id }}</p>
                </div>

                <!-- Nomor Batch -->
                <div class="space-y-1.5">
                    <Label for="no_batch" class="text-xs">Nomor Batch Pabrik *</Label>
                    <Input
                        id="no_batch"
                        v-model="form.no_batch"
                        placeholder="Contoh: PCT-2410-01"
                        class="text-xs font-mono"
                        required
                    />
                    <p v-if="form.errors.no_batch" class="text-[11px] text-destructive">{{ form.errors.no_batch }}</p>
                </div>

                <!-- Tanggal Diterima -->
                <div class="space-y-1.5">
                    <Label for="tanggal_masuk" class="text-xs">Tanggal Diterima *</Label>
                    <Input
                        id="tanggal_masuk"
                        type="date"
                        v-model="form.tanggal_masuk"
                        class="text-xs"
                        required
                    />
                    <p v-if="form.errors.tanggal_masuk" class="text-[11px] text-destructive">{{ form.errors.tanggal_masuk }}</p>
                </div>

                <!-- Tanggal Kadaluarsa (ED) -->
                <div class="space-y-1.5">
                    <Label for="tanggal_kadaluarsa" class="text-xs">Tanggal Kadaluarsa (Expired Date) *</Label>
                    <Input
                        id="tanggal_kadaluarsa"
                        type="date"
                        v-model="form.tanggal_kadaluarsa"
                        class="text-xs"
                        required
                    />
                    <p v-if="form.errors.tanggal_kadaluarsa" class="text-[11px] text-destructive">{{ form.errors.tanggal_kadaluarsa }}</p>
                </div>

                <!-- Jumlah Obat Masuk -->
                <div class="space-y-1.5">
                    <Label for="jumlah" class="text-xs">
                        Jumlah Diterima ({{ selectedObat?.satuan || 'Unit' }}) *
                    </Label>
                    <Input
                        id="jumlah"
                        type="number"
                        min="1"
                        v-model="form.jumlah"
                        class="text-xs font-semibold"
                        required
                    />
                    <p v-if="form.errors.jumlah" class="text-[11px] text-destructive">{{ form.errors.jumlah }}</p>
                </div>

                <!-- Harga Beli Satuan -->
                <div class="space-y-1.5">
                    <Label for="harga_beli" class="text-xs">Harga Beli per Satuan (Rp) *</Label>
                    <Input
                        id="harga_beli"
                        type="number"
                        min="0"
                        step="0.01"
                        v-model="form.harga_beli"
                        class="text-xs"
                        required
                    />
                    <p v-if="form.errors.harga_beli" class="text-[11px] text-destructive">{{ form.errors.harga_beli }}</p>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <Label for="keterangan" class="text-xs">Keterangan / No. Surat Pengantar</Label>
                <textarea
                    id="keterangan"
                    v-model="form.keterangan"
                    rows="2"
                    placeholder="Contoh: Penerimaan SP nomor 123/PBF-KF/X/2026"
                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                ></textarea>
                <p v-if="form.errors.keterangan" class="text-[11px] text-destructive">{{ form.errors.keterangan }}</p>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-border/60">
                <Button as-child variant="outline" size="sm" class="text-xs">
                    <Link href="/stok">Batal</Link>
                </Button>
                <Button type="submit" size="sm" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs" :disabled="form.processing">
                    <PackagePlus class="mr-1.5 size-4" />
                    Simpan Stok Masuk
                </Button>
            </div>
        </form>
    </div>
</template>
