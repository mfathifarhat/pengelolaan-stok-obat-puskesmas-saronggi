<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    AlertCircle,
    Filter,
    FolderKanban,
    Pencil,
    Plus,
    Search,
    Trash2,
} from '@lucide/vue';
import { ref, watch } from 'vue';
import DeleteConfirmDialog from '@/components/simstok/DeleteConfirmDialog.vue';
import KategoriObatModal from '@/components/simstok/KategoriObatModal.vue';
import Pagination from '@/components/simstok/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { KategoriObat, Obat, PaginatedData } from '@/types';

const props = defineProps<{
    obats: PaginatedData<Obat>;
    kategoriList: KategoriObat[];
    filters: {
        search?: string;
        kategori_id?: string;
        status_stok?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Data Obat', href: '/obat' },
        ],
    },
});

const search = ref(props.filters.search || '');
const kategoriId = ref(props.filters.kategori_id || '');
const statusStok = ref(props.filters.status_stok || '');

const showKategoriModal = ref(false);
const deleteDialogOpen = ref(false);
const selectedObat = ref<Obat | null>(null);
const deleteLoading = ref(false);

const applyFilters = () => {
    router.get(
        '/obat',
        {
            search: search.value || undefined,
            kategori_id: kategoriId.value || undefined,
            status_stok: statusStok.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 350);
});

watch([kategoriId, statusStok], () => {
    applyFilters();
});

const confirmDelete = (obat: Obat) => {
    selectedObat.value = obat;
    deleteDialogOpen.value = true;
};

const handleDelete = () => {
    if (!selectedObat.value) return;
    deleteLoading.value = true;
    router.delete(`/obat/${selectedObat.value.id}`, {
        onFinish: () => {
            deleteLoading.value = false;
            deleteDialogOpen.value = false;
            selectedObat.value = null;
        },
    });
};

const reloadCategories = () => {
    router.reload({ only: ['kategoriList', 'obats'] });
};
</script>

<template>
    <Head title="Master Data Obat - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header & Action Buttons -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                    Master Data Obat
                </h1>
                <p class="text-xs text-muted-foreground sm:text-sm">
                    Katalog obat, penentuan batas minimum stok, dan agregasi batch aktif.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    class="text-xs"
                    @click="showKategoriModal = true"
                >
                    <FolderKanban class="mr-1.5 size-4 text-emerald-600" />
                    Kelola Kategori
                </Button>
                <Button as-child size="sm" class="bg-primary text-xs">
                    <Link href="/obat/create">
                        <Plus class="mr-1.5 size-4" />
                        Tambah Obat Baru
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="grid grid-cols-1 gap-3 rounded-xl border border-sidebar-border bg-card p-4 shadow-xs sm:grid-cols-12">
            <div class="relative sm:col-span-5">
                <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
                <Input
                    v-model="search"
                    placeholder="Cari nama, kode obat, produsen..."
                    class="h-9 pl-9 text-xs"
                />
            </div>
            <div class="sm:col-span-4">
                <select
                    v-model="kategoriId"
                    class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                >
                    <option value="">Semua Kategori Golongan</option>
                    <option v-for="kat in kategoriList" :key="kat.id" :value="kat.id">
                        {{ kat.nama }}
                    </option>
                </select>
            </div>
            <div class="sm:col-span-3">
                <select
                    v-model="statusStok"
                    class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                >
                    <option value="">Semua Status Stok</option>
                    <option value="kritis">🔴 Stok Kritis (&le; Minimum)</option>
                    <option value="aman">🟢 Stok Aman (> Minimum)</option>
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-hidden rounded-xl border border-sidebar-border bg-card shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border/60 bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Nama Obat</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Produsen</th>
                            <th class="px-4 py-3">Satuan</th>
                            <th class="px-4 py-3 text-right">Stok Fisik</th>
                            <th class="px-4 py-3 text-right">Batas Min</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/40">
                        <tr
                            v-for="obat in obats.data"
                            :key="obat.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 font-mono font-medium text-muted-foreground">
                                {{ obat.kode_obat }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-foreground">{{ obat.nama_obat }}</span>
                                <p v-if="obat.keterangan" class="text-[11px] text-muted-foreground truncate max-w-xs">
                                    {{ obat.keterangan }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md bg-secondary px-2 py-0.5 text-[11px] font-medium text-secondary-foreground">
                                    {{ obat.kategori?.nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ obat.produsen || '-' }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ obat.satuan }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">
                                <span
                                    v-if="obat.total_stok <= obat.stok_minimum"
                                    class="inline-flex items-center gap-1 rounded-full bg-rose-500/10 px-2 py-0.5 text-xs font-bold text-rose-700 dark:text-rose-400"
                                >
                                    <AlertCircle class="size-3" />
                                    {{ obat.total_stok }}
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400"
                                >
                                    {{ obat.total_stok }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-muted-foreground">
                                {{ obat.stok_minimum }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="sm"
                                        class="size-7 p-0 text-muted-foreground hover:text-foreground"
                                        title="Edit Obat"
                                    >
                                        <Link :href="`/obat/${obat.id}/edit`">
                                            <Pencil class="size-3.5" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="size-7 p-0 text-muted-foreground hover:text-destructive"
                                        title="Hapus Obat"
                                        @click="confirmDelete(obat)"
                                    >
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="obats.data.length === 0">
                            <td colspan="8" class="p-8 text-center text-muted-foreground">
                                Tidak ada data obat yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="border-t border-border/40 px-4">
                <Pagination
                    :links="obats.links"
                    :from="obats.from"
                    :to="obats.to"
                    :total="obats.total"
                />
            </div>
        </div>
    </div>

    <!-- Modal Kategori Obat -->
    <KategoriObatModal
        :open="showKategoriModal"
        :kategoriList="kategoriList"
        @update:open="showKategoriModal = $event"
        @updated="reloadCategories"
    />

    <!-- Dialog Konfirmasi Hapus -->
    <DeleteConfirmDialog
        :open="deleteDialogOpen"
        :loading="deleteLoading"
        :title="`Hapus Obat ${selectedObat?.nama_obat || ''}?`"
        description="Obat yang masih memiliki batch dengan sisa stok aktif tidak dapat dihapus."
        @update:open="deleteDialogOpen = $event"
        @confirm="handleDelete"
    />
</template>
