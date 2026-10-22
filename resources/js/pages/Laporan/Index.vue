<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    Calendar,
    Download,
    FileSpreadsheet,
    FileText,
    Filter,
    Printer,
    Search,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import StatCard from '@/components/simstok/StatCard.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { StokMovement } from '@/types';

const props = defineProps<{
    movements: StokMovement[];
    obats: { id: number; nama_obat: string; kode_obat: string }[];
    summary: {
        total_masuk: number;
        total_keluar: number;
        nilai_masuk: number;
        total_transaksi: number;
    };
    filters: {
        start_date: string;
        end_date: string;
        tipe: string;
        obat_id: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Laporan Stok & Mutasi', href: '/laporan' },
        ],
    },
});

const startDate = ref(props.filters.start_date);
const endDate = ref(props.filters.end_date);
const tipe = ref(props.filters.tipe);
const obatId = ref(props.filters.obat_id);

const applyFilters = () => {
    router.get(
        '/laporan',
        {
            start_date: startDate.value || undefined,
            end_date: endDate.value || undefined,
            tipe: tipe.value || undefined,
            obat_id: obatId.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

const setQuickPeriod = (type: 'this_month' | 'last_month' | 'today') => {
    const today = new Date();
    if (type === 'today') {
        const d = today.toISOString().split('T')[0];
        startDate.value = d;
        endDate.value = d;
    } else if (type === 'this_month') {
        const start = new Date(today.getFullYear(), today.getMonth(), 1);
        startDate.value = start.toISOString().split('T')[0];
        endDate.value = today.toISOString().split('T')[0];
    } else if (type === 'last_month') {
        const start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const end = new Date(today.getFullYear(), today.getMonth(), 0);
        startDate.value = start.toISOString().split('T')[0];
        endDate.value = end.toISOString().split('T')[0];
    }
    applyFilters();
};

const queryParamsString = computed(() => {
    const params = new URLSearchParams();
    if (startDate.value) params.set('start_date', startDate.value);
    if (endDate.value) params.set('end_date', endDate.value);
    if (tipe.value) params.set('tipe', tipe.value);
    if (obatId.value) params.set('obat_id', obatId.value);
    return params.toString();
});

const openPrintView = () => {
    window.open(`/laporan/print?${queryParamsString.value}`, '_blank');
};

const exportCsv = () => {
    window.location.href = `/laporan/export-csv?${queryParamsString.value}`;
};

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val);
};
</script>

<template>
    <Head title="Laporan Mutasi Stok - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header & Export Actions -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                    Laporan Mutasi & Pengelolaan Stok
                </h1>
                <p class="text-xs text-muted-foreground sm:text-sm">
                    Modul eksklusif Admin / Manajer: Rekapitulasi pergerakan inventaris, cetak surat pertanggungjawaban, dan ekspor data.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <Button
                    size="sm"
                    variant="outline"
                    class="text-xs border-emerald-600 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-500 dark:text-emerald-400"
                    @click="exportCsv"
                >
                    <FileSpreadsheet class="mr-1.5 size-4 text-emerald-600" />
                    Export Excel / CSV
                </Button>
                <!-- <Button
                    size="sm"
                    class="bg-primary text-xs"
                    @click="openPrintView"
                >
                    <Printer class="mr-1.5 size-4" />
                    Cetak / Simpan PDF
                </Button> -->
            </div>
        </div>

        <!-- 4 Summary Metric Cards -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <StatCard
                title="Total Stok Masuk"
                :value="summary.total_masuk"
                subtitle="Satuan unit obat diterima"
                :icon="ArrowDownRight"
                colorVariant="emerald"
            />
            <StatCard
                title="Total Stok Keluar"
                :value="summary.total_keluar"
                subtitle="Satuan unit obat terpakai"
                :icon="ArrowUpRight"
                colorVariant="rose"
            />
            <StatCard
                title="Total Baris Mutasi"
                :value="summary.total_transaksi"
                subtitle="Transaksi tercatat"
                :icon="FileText"
                colorVariant="sky"
            />
            <StatCard
                title="Nilai Pasokan Masuk"
                :value="formatRupiah(summary.nilai_masuk)"
                subtitle="Total belanja obat diterima"
                :icon="Calendar"
                colorVariant="default"
            />
        </div>

        <!-- Filter Period & Controls -->
        <div class="rounded-xl border border-sidebar-border bg-card p-4 shadow-xs space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-border/40 pb-3">
                <span class="text-xs font-semibold text-foreground flex items-center gap-1.5">
                    <Filter class="size-3.5 text-primary" />
                    Filter Periode & Parameter Laporan
                </span>
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] text-muted-foreground">Pintasan Cepat:</span>
                    <button
                        type="button"
                        class="rounded-md border border-border px-2 py-0.5 text-[11px] hover:bg-muted font-medium"
                        @click="setQuickPeriod('today')"
                    >
                        Hari Ini
                    </button>
                    <button
                        type="button"
                        class="rounded-md border border-border px-2 py-0.5 text-[11px] hover:bg-muted font-medium"
                        @click="setQuickPeriod('this_month')"
                    >
                        Bulan Ini
                    </button>
                    <button
                        type="button"
                        class="rounded-md border border-border px-2 py-0.5 text-[11px] hover:bg-muted font-medium"
                        @click="setQuickPeriod('last_month')"
                    >
                        Bulan Lalu
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-12">
                <div class="sm:col-span-3 space-y-1">
                    <Label class="text-[11px] text-muted-foreground">Dari Tanggal</Label>
                    <Input
                        type="date"
                        v-model="startDate"
                        class="h-8 text-xs"
                        @change="applyFilters"
                    />
                </div>
                <div class="sm:col-span-3 space-y-1">
                    <Label class="text-[11px] text-muted-foreground">Sampai Tanggal</Label>
                    <Input
                        type="date"
                        v-model="endDate"
                        class="h-8 text-xs"
                        @change="applyFilters"
                    />
                </div>
                <div class="sm:col-span-3 space-y-1">
                    <Label class="text-[11px] text-muted-foreground">Jenis Mutasi</Label>
                    <select
                        v-model="tipe"
                        class="h-8 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        @change="applyFilters"
                    >
                        <option value="">Semua (Masuk & Keluar)</option>
                        <option value="masuk">Hanya Stok Masuk (Penerimaan)</option>
                        <option value="keluar">Hanya Stok Keluar (Pengeluaran)</option>
                    </select>
                </div>
                <div class="sm:col-span-3 space-y-1">
                    <Label class="text-[11px] text-muted-foreground">Filter Obat Spesifik</Label>
                    <select
                        v-model="obatId"
                        class="h-8 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        @change="applyFilters"
                    >
                        <option value="">Semua Obat</option>
                        <option v-for="o in obats" :key="o.id" :value="o.id">
                            {{ o.nama_obat }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-hidden rounded-xl border border-sidebar-border bg-card shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border/60 bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kode & Nama Obat</th>
                            <th class="px-4 py-3">No. Batch</th>
                            <th class="px-4 py-3">Kadaluarsa</th>
                            <th class="px-4 py-3">Supplier / PBF</th>
                            <th class="px-4 py-3 text-center">Tipe</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                            <th class="px-4 py-3 text-right">Sisa Batch</th>
                            <th class="px-4 py-3">Petugas</th>
                            <th class="px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/40">
                        <tr
                            v-for="(m, idx) in movements"
                            :key="m.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 text-muted-foreground">{{ idx + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted-foreground font-mono">
                                {{ new Date(m.tanggal).toLocaleDateString('id-ID') }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-foreground">
                                <div>{{ m.batch_obat?.obat?.nama_obat ?? '-' }}</div>
                                <div class="text-[10px] font-mono text-muted-foreground">
                                    {{ m.batch_obat?.obat?.kode_obat }}
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono font-medium text-muted-foreground">
                                {{ m.batch_obat?.no_batch ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted-foreground font-mono">
                                {{ m.batch_obat?.tanggal_kadaluarsa ? new Date(m.batch_obat.tanggal_kadaluarsa).toLocaleDateString('id-ID') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ m.batch_obat?.supplier?.nama ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-bold"
                                    :class="
                                        m.tipe === 'masuk'
                                            ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                            : 'bg-rose-500/10 text-rose-700 dark:text-rose-400'
                                    "
                                >
                                    {{ m.tipe === 'masuk' ? 'MASUK' : 'KELUAR' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-foreground">
                                {{ m.jumlah }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-muted-foreground">
                                {{ m.stok_sesudah }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">
                                {{ m.user?.name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground max-w-xs truncate">
                                {{ m.keterangan || '-' }}
                            </td>
                        </tr>
                        <tr v-if="movements.length === 0">
                            <td colspan="11" class="p-8 text-center text-muted-foreground">
                                Tidak ada data mutasi yang ditemukan untuk periode ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
