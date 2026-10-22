<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowDownRight,
    ArrowUpRight,
    Calendar,
    Clock,
    PackagePlus,
    Pill,
    ShieldAlert,
} from '@lucide/vue';
import StatCard from '@/components/simstok/StatCard.vue';
import { Button } from '@/components/ui/button';
import type { BatchObat, Obat, StokMovement } from '@/types';

defineProps<{
    stats: {
        total_obat: number;
        total_kritis: number;
        total_expiring: number;
        stok_masuk_bulan_ini: number;
        stok_keluar_bulan_ini: number;
    };
    kritisList: Obat[];
    expiringBatches: BatchObat[];
    recentMovements: StokMovement[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header & Quick Actions -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                    Sistem Pengelolaan Stok Obat
                </h1>
                <p class="text-xs text-muted-foreground sm:text-sm">
                    UPTD Puskesmas Saronggi &bull; Monitoring ketersediaan obat, masa kadaluarsa, dan mutasi harian.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <Button as-child size="sm" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs">
                    <Link href="/stok/masuk">
                        <PackagePlus class="mr-1.5 size-4" />
                        Stok Masuk
                    </Link>
                </Button>
                <Button as-child size="sm" variant="outline" class="border-rose-300 text-rose-700 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-400 text-xs">
                    <Link href="/stok/keluar">
                        <ArrowUpRight class="mr-1.5 size-4" />
                        Stok Keluar
                    </Link>
                </Button>
                <Button as-child size="sm" variant="secondary" class="text-xs">
                    <Link href="/obat/create">
                        <Pill class="mr-1.5 size-4" />
                        Tambah Obat
                    </Link>
                </Button>
            </div>
        </div>

        <!-- 5 Stats Cards Grid -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard
                title="Stok Kritis"
                :value="stats.total_kritis"
                subtitle="Di bawah stok minimum"
                :icon="AlertTriangle"
                colorVariant="rose"
            />
            <StatCard
                title="Mendekati ED"
                :value="stats.total_expiring"
                subtitle="Kadaluarsa < 90 hari"
                :icon="Clock"
                colorVariant="amber"
            />
            <StatCard
                title="Masuk Bulan Ini"
                :value="stats.stok_masuk_bulan_ini"
                subtitle="Unit obat diterima"
                :icon="ArrowDownRight"
                colorVariant="emerald"
            />
            <StatCard
                title="Keluar Bulan Ini"
                :value="stats.stok_keluar_bulan_ini"
                subtitle="Unit terdistribusi"
                :icon="ArrowUpRight"
                colorVariant="sky"
            />
        </div>

        <!-- Two Columns: Kritis & Expiring -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Peringatan Obat Stok Kritis -->
            <div class="rounded-xl border border-sidebar-border bg-card shadow-xs">
                <div class="flex items-center justify-between border-b border-sidebar-border px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        <div class="rounded-md bg-rose-500/10 p-1.5 text-rose-600 dark:text-rose-400">
                            <ShieldAlert class="size-4" />
                        </div>
                        <h2 class="text-sm font-semibold text-foreground">Obat Menipis / Stok Kritis</h2>
                    </div>
                    <Link href="/obat?status_stok=kritis" class="text-xs font-medium text-primary hover:underline">
                        Lihat Semua
                    </Link>
                </div>
                <div class="divide-y divide-border/40 p-1">
                    <div
                        v-for="item in kritisList"
                        :key="item.id"
                        class="flex items-center justify-between p-3 transition-colors hover:bg-muted/40"
                    >
                        <div>
                            <p class="text-xs font-medium text-foreground">{{ item.nama_obat }}</p>
                            <p class="text-[11px] text-muted-foreground">
                                Kode: {{ item.kode_obat }} &bull; Satuan: {{ item.satuan }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center rounded-full bg-rose-500/10 px-2 py-0.5 text-xs font-semibold text-rose-700 dark:text-rose-400">
                                Sisa: {{ item.total_stok }} (Min: {{ item.stok_minimum }})
                            </span>
                            <div class="mt-1">
                                <Link
                                    :href="`/stok/masuk?obat_id=${item.id}`"
                                    class="text-[11px] font-medium text-emerald-600 hover:underline dark:text-emerald-400"
                                >
                                    + Restok Sekarang
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-if="kritisList.length === 0" class="p-6 text-center text-xs text-muted-foreground">
                        ✨ Semua stok obat saat ini berada di atas batas minimum.
                    </div>
                </div>
            </div>

            <!-- Peringatan Batch Mendekati Kadaluarsa -->
            <div class="rounded-xl border border-sidebar-border bg-card shadow-xs">
                <div class="flex items-center justify-between border-b border-sidebar-border px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        <div class="rounded-md bg-amber-500/10 p-1.5 text-amber-600 dark:text-amber-400">
                            <Calendar class="size-4" />
                        </div>
                        <h2 class="text-sm font-semibold text-foreground">Batch Mendekati Kadaluarsa (&le; 90 Hari)</h2>
                    </div>
                    <Link href="/stok" class="text-xs font-medium text-primary hover:underline">
                        Riwayat Mutasi
                    </Link>
                </div>
                <div class="divide-y divide-border/40 p-1">
                    <div
                        v-for="batch in expiringBatches"
                        :key="batch.id"
                        class="flex items-center justify-between p-3 transition-colors hover:bg-muted/40"
                    >
                        <div>
                            <p class="text-xs font-medium text-foreground">{{ batch.obat?.nama_obat }}</p>
                            <p class="text-[11px] text-muted-foreground">
                                Batch: <span class="font-mono font-medium">{{ batch.no_batch }}</span> &bull;
                                ED: {{ new Date(batch.tanggal_kadaluarsa).toLocaleDateString('id-ID') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center rounded-full bg-amber-500/10 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-400">
                                Sisa {{ batch.stok }} {{ batch.obat?.satuan }}
                            </span>
                            <div class="mt-1">
                                <Link
                                    :href="`/stok/keluar?batch_id=${batch.id}`"
                                    class="text-[11px] font-medium text-rose-600 hover:underline dark:text-rose-400"
                                >
                                    Prioritaskan Distribusi
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-if="expiringBatches.length === 0" class="p-6 text-center text-xs text-muted-foreground">
                        ✨ Tidak ada batch aktif yang mendekati batas kadaluarsa.
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Stock Movements Table -->
        <div class="rounded-xl border border-sidebar-border bg-card shadow-xs">
            <div class="flex items-center justify-between border-b border-sidebar-border px-5 py-3.5">
                <h2 class="text-sm font-semibold text-foreground">Aktivitas Mutasi Stok Terkini</h2>
                <Link href="/stok" class="text-xs font-medium text-primary hover:underline">
                    Semua Mutasi &rarr;
                </Link>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border/60 bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-4 py-2.5">Tanggal</th>
                            <th class="px-4 py-2.5">Obat</th>
                            <th class="px-4 py-2.5">No. Batch</th>
                            <th class="px-4 py-2.5">Tipe</th>
                            <th class="px-4 py-2.5 text-right">Jumlah</th>
                            <th class="px-4 py-2.5">Petugas</th>
                            <th class="px-4 py-2.5">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/40">
                        <tr
                            v-for="m in recentMovements"
                            :key="m.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-4 py-2.5 whitespace-nowrap text-muted-foreground">
                                {{ new Date(m.tanggal).toLocaleDateString('id-ID') }}
                            </td>
                            <td class="px-4 py-2.5 font-medium text-foreground">
                                {{ m.batch_obat?.obat?.nama_obat ?? '-' }}
                            </td>
                            <td class="px-4 py-2.5 font-mono text-muted-foreground">
                                {{ m.batch_obat?.no_batch ?? '-' }}
                            </td>
                            <td class="px-4 py-2.5">
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
                            <td class="px-4 py-2.5 text-right font-semibold text-foreground">
                                {{ m.jumlah }}
                            </td>
                            <td class="px-4 py-2.5 text-muted-foreground">
                                {{ m.user?.name ?? '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-muted-foreground truncate max-w-xs">
                                {{ m.keterangan || '-' }}
                            </td>
                        </tr>
                        <tr v-if="recentMovements.length === 0">
                            <td colspan="7" class="p-6 text-center text-muted-foreground">
                                Belum ada riwayat mutasi stok.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
