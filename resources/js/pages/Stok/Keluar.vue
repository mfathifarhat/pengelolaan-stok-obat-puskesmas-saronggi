<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    AlertCircle,
    ArrowLeft,
    ArrowUpRight,
    CheckCircle2,
    Clock,
    Sparkles,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BatchObat, Obat } from '@/types';

const props = defineProps<{
    obats: (Obat & { active_batches: BatchObat[] })[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Mutasi Stok', href: '/stok' },
            { title: 'Catat Stok Keluar', href: '/stok/keluar' },
        ],
    },
});

const todayStr = new Date().toISOString().split('T')[0];
const selectedObatId = ref<string>('');

const form = useForm({
    batch_obat_id: '',
    tanggal: todayStr,
    jumlah: 10,
    keterangan: 'Pelayanan resep harian rawat jalan puskesmas',
});

const currentObat = computed(() => {
    return props.obats.find((o) => String(o.id) === String(selectedObatId.value));
});

const activeBatches = computed(() => {
    return currentObat.value?.active_batches || [];
});

const selectedBatch = computed(() => {
    return activeBatches.value.find((b) => String(b.id) === String(form.batch_obat_id));
});

// Auto-select FEFO batch (first batch with nearest expiry date)
watch(selectedObatId, () => {
    form.batch_obat_id = '';
    if (activeBatches.value.length > 0) {
        form.batch_obat_id = String(activeBatches.value[0].id);
    }
});

const selectBatch = (batchId: number) => {
    form.batch_obat_id = String(batchId);
};

const submit = () => {
    form.post('/stok/keluar');
};
</script>

<template>
    <Head title="Pengeluaran Stok Obat (FEFO) - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 max-w-3xl">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="sm" class="size-8 p-0">
                <Link href="/stok">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">
                    Catat Pengeluaran Stok Obat (Prinsip FEFO)
                </h1>
                <p class="text-xs text-muted-foreground">
                    Sistem memprioritaskan batch obat yang memiliki masa kadaluarsa (ED) paling dekat.
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="rounded-xl border border-sidebar-border bg-card p-5 shadow-xs space-y-4">
            <!-- Step 1: Pilih Obat -->
            <div class="space-y-1.5">
                <Label for="obat_select" class="text-xs">1. Pilih Obat yang Dikeluarkan *</Label>
                <select
                    id="obat_select"
                    v-model="selectedObatId"
                    class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    required
                >
                    <option value="" disabled>-- Pilih Obat dengan Stok Aktif --</option>
                    <option v-for="o in obats" :key="o.id" :value="o.id">
                        {{ o.nama_obat }} ({{ o.kode_obat }}) — Tersedia {{ o.total_stok }} {{ o.satuan }} ({{ o.active_batches?.length || 0 }} batch)
                    </option>
                </select>
            </div>

            <!-- Step 2: Rekomendasi Batch FEFO -->
            <div v-if="currentObat" class="space-y-2 pt-2 border-t border-border/40">
                <div class="flex items-center justify-between">
                    <Label class="text-xs font-semibold text-foreground">
                        2. Pilih Batch Obat (Diurutkan berdasarkan FEFO) *
                    </Label>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
                        Prioritas Kadaluarsa Terdekat
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                    <div
                        v-for="(batch, idx) in activeBatches"
                        :key="batch.id"
                        class="cursor-pointer rounded-lg border p-3 transition-all duration-150"
                        :class="
                            String(form.batch_obat_id) === String(batch.id)
                                ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                : 'border-border/60 hover:bg-muted/40'
                        "
                        @click="selectBatch(batch.id)"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono text-xs font-bold text-foreground">
                                        {{ batch.no_batch }}
                                    </span>
                                    <span
                                        v-if="idx === 0"
                                        class="rounded-full bg-emerald-500/15 px-1.5 py-0.2 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400"
                                    >
                                        Rekomendasi FEFO
                                    </span>
                                </div>
                                <p class="text-[11px] text-muted-foreground mt-0.5">
                                    Supplier: {{ batch.supplier?.nama || '-' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-foreground">
                                    Stok: {{ batch.stok }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-2.5 flex items-center justify-between border-t border-border/40 pt-1.5 text-[11px]">
                            <div class="flex items-center gap-1 text-muted-foreground">
                                <Clock class="size-3" />
                                <span>ED: {{ new Date(batch.tanggal_kadaluarsa).toLocaleDateString('id-ID') }}</span>
                            </div>
                            <div v-if="String(form.batch_obat_id) === String(batch.id)" class="flex items-center gap-1 text-primary font-medium">
                                <CheckCircle2 class="size-3.5" />
                                <span>Dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p v-if="form.errors.batch_obat_id" class="text-[11px] text-destructive">{{ form.errors.batch_obat_id }}</p>
            </div>

            <!-- Step 3: Form Transaksi -->
            <div v-if="selectedBatch" class="space-y-4 pt-2 border-t border-border/40">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Jumlah Keluar -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <Label for="jumlah" class="text-xs">Jumlah Dikeluarkan ({{ currentObat?.satuan || 'Unit' }}) *</Label>
                            <span class="text-[11px] text-muted-foreground">
                                Sisa Batch: <strong>{{ selectedBatch.stok }}</strong>
                            </span>
                        </div>
                        <Input
                            id="jumlah"
                            type="number"
                            min="1"
                            :max="selectedBatch.stok"
                            v-model="form.jumlah"
                            class="text-xs font-semibold"
                            required
                        />
                        <p v-if="form.errors.jumlah" class="text-[11px] text-destructive">{{ form.errors.jumlah }}</p>
                    </div>

                    <!-- Tanggal Pengeluaran -->
                    <div class="space-y-1.5">
                        <Label for="tanggal" class="text-xs">Tanggal Pengeluaran *</Label>
                        <Input
                            id="tanggal"
                            type="date"
                            v-model="form.tanggal"
                            class="text-xs"
                            required
                        />
                        <p v-if="form.errors.tanggal" class="text-[11px] text-destructive">{{ form.errors.tanggal }}</p>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="space-y-1.5">
                    <Label for="keterangan" class="text-xs">Keterangan Pemakaian *</Label>
                    <textarea
                        id="keterangan"
                        v-model="form.keterangan"
                        rows="2"
                        placeholder="Contoh: Pelayanan resep harian rawat jalan, pemakaian tindakan IGD, dll"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        required
                    ></textarea>
                    <p v-if="form.errors.keterangan" class="text-[11px] text-destructive">{{ form.errors.keterangan }}</p>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-border/60">
                <Button as-child variant="outline" size="sm" class="text-xs">
                    <Link href="/stok">Batal</Link>
                </Button>
                <Button
                    type="submit"
                    size="sm"
                    class="bg-rose-600 hover:bg-rose-700 text-white text-xs"
                    :disabled="form.processing || !selectedBatch || form.jumlah > (selectedBatch?.stok || 0)"
                >
                    <ArrowUpRight class="mr-1.5 size-4" />
                    Simpan Pengeluaran Stok
                </Button>
            </div>
        </form>
    </div>
</template>
