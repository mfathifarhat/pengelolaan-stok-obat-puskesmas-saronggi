<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    Calendar,
    Filter,
    PackagePlus,
    Search,
} from '@lucide/vue';
import { ref, watch } from 'vue';
import Pagination from '@/components/simstok/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { PaginatedData, StokMovement } from '@/types';

const props = defineProps<{
    movements: PaginatedData<StokMovement>;
    obats: { id: number; nama_obat: string; kode_obat: string }[];
    filters: {
        search?: string;
        tipe?: string;
        obat_id?: string;
        start_date?: string;
        end_date?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Mutasi Stok', href: '/stok' },
        ],
    },
});

const search = ref(props.filters.search || '');
const tipe = ref(props.filters.tipe || '');
const obatId = ref(props.filters.obat_id || '');
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');

const applyFilters = () => {
    router.get(
        '/stok',
        {
            search: search.value || undefined,
            tipe: tipe.value || undefined,
            obat_id: obatId.value || undefined,
            start_date: startDate.value || undefined,
            end_date: endDate.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 350);
});

watch([tipe, obatId, startDate, endDate], () => {
    applyFilters();
});
</script>

<template>
    <Head title="Mutasi Stok Obat - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header & Action Buttons -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                    Riwayat Mutasi Stok Obat
                </h1>
                <p class="text-xs text-muted-foreground sm:text-sm">
                    Log audit transaksi stok masuk (penerimaan batch) dan stok keluar (distribusi harian).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <Button as-child size="sm" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs">
                    <Link href="/stok/masuk">
                        <PackagePlus class="mr-1.5 size-4" />
                        Catat Stok Masuk
                    </Link>
                </Button>
                <Button as-child size="sm" variant="outline" class="border-rose-300 text-rose-700 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-400 text-xs">
                    <Link href="/stok/keluar">
                        <ArrowUpRight class="mr-1.5 size-4" />
                        Catat Stok Keluar
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="grid grid-cols-1 gap-3 rounded-xl border border-sidebar-border bg-card p-4 shadow-xs sm:grid-cols-12">
            <div class="relative sm:col-span-3">
                <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
                <Input
                    v-model="search"
                    placeholder="Cari keterangan, no batch..."
                    class="h-9 pl-9 text-xs"
                />
            </div>
            <div class="sm:col-span-3">
                <select
                    v-model="obatId"
                    class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                >
                    <option value="">Semua Obat</option>
                    <option v-for="o in obats" :key="o.id" :value="o.id">
                        {{ o.nama_obat }} ({{ o.kode_obat }})
                    </option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <select
                    v-model="tipe"
                    class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                >
                    <option value="">Semua Jenis Mutasi</option>
                    <option value="masuk">↓ Stok Masuk</option>
                    <option value="keluar">↑ Stok Keluar</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <Input
                    type="date"
                    v-model="startDate"
                    placeholder="Dari Tanggal"
                    class="h-9 text-xs"
                />
            </div>
            <div class="sm:col-span-2">
                <Input
                    type="date"
                    v-model="endDate"
                    placeholder="Sampai Tanggal"
                    class="h-9 text-xs"
                />
            </div>
        </div>

        <!-- Movements Table -->
        <div class="overflow-hidden rounded-xl border border-sidebar-border bg-card shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border/60 bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Nama Obat</th>
                            <th class="px-4 py-3">No. Batch</th>
                            <th class="px-4 py-3">Kadaluarsa (ED)</th>
                            <th class="px-4 py-3">Supplier / PBF</th>
                            <th class="px-4 py-3 text-center">Tipe</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                            <th class="px-4 py-3 text-right">Sebelum</th>
                            <th class="px-4 py-3 text-right">Sesudah</th>
                            <th class="px-4 py-3">Petugas</th>
                            <th class="px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/40">
                        <tr
                            v-for="m in movements.data"
                            :key="m.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                {{ new Date(m.tanggal).toLocaleDateString('id-ID') }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-foreground">
                                {{ m.batch_obat?.obat?.nama_obat ?? '-' }}
                                <span class="text-[11px] text-muted-foreground font-mono ml-1">
                                    ({{ m.batch_obat?.obat?.kode_obat }})
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono font-medium text-muted-foreground">
                                {{ m.batch_obat?.no_batch ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                {{ m.batch_obat?.tanggal_kadaluarsa ? new Date(m.batch_obat.tanggal_kadaluarsa).toLocaleDateString('id-ID') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ m.batch_obat?.supplier?.nama ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                    :class="
                                        m.tipe === 'masuk'
                                            ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                            : 'bg-rose-500/10 text-rose-700 dark:text-rose-400'
                                    "
                                >
                                    {{ m.tipe === 'masuk' ? '↓ Masuk' : '↑ Keluar' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-foreground">
                                {{ m.jumlah }}
                            </td>
                            <td class="px-4 py-3 text-right text-muted-foreground">
                                {{ m.stok_sebelum }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-foreground">
                                {{ m.stok_sesudah }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">
                                {{ m.user?.name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground max-w-xs truncate">
                                {{ m.keterangan || '-' }}
                            </td>
                        </tr>
                        <tr v-if="movements.data.length === 0">
                            <td colspan="11" class="p-8 text-center text-muted-foreground">
                                Belum ada catatan mutasi stok untuk filter ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-border/40 px-4">
                <Pagination
                    :links="movements.links"
                    :from="movements.from"
                    :to="movements.to"
                    :total="movements.total"
                />
            </div>
        </div>
    </div>
</template>
