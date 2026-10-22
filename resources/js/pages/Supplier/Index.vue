<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Building2,
    Pencil,
    Phone,
    Plus,
    Search,
    Trash2,
    UserCheck,
} from '@lucide/vue';
import { ref, watch } from 'vue';
import DeleteConfirmDialog from '@/components/simstok/DeleteConfirmDialog.vue';
import Pagination from '@/components/simstok/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { PaginatedData, Supplier } from '@/types';

const props = defineProps<{
    suppliers: PaginatedData<Supplier>;
    filters: {
        search?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Data Supplier', href: '/supplier' },
        ],
    },
});

const search = ref(props.filters.search || '');
const deleteDialogOpen = ref(false);
const selectedSupplier = ref<Supplier | null>(null);
const deleteLoading = ref(false);

const applySearch = () => {
    router.get(
        '/supplier',
        { search: search.value || undefined },
        { preserveState: true, replace: true }
    );
};

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applySearch, 350);
});

const confirmDelete = (sup: Supplier) => {
    selectedSupplier.value = sup;
    deleteDialogOpen.value = true;
};

const handleDelete = () => {
    if (!selectedSupplier.value) return;
    deleteLoading.value = true;
    router.delete(`/supplier/${selectedSupplier.value.id}`, {
        onFinish: () => {
            deleteLoading.value = false;
            deleteDialogOpen.value = false;
            selectedSupplier.value = null;
        },
    });
};
</script>

<template>
    <Head title="Data Supplier & PBF - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                    Data Supplier & Pedagang Besar Farmasi (PBF)
                </h1>
                <p class="text-xs text-muted-foreground sm:text-sm">
                    Daftar distributor resmi penyedia pasokan obat untuk UPTD Puskesmas Saronggi.
                </p>
            </div>
            <div>
                <Button as-child size="sm" class="bg-primary text-xs">
                    <Link href="/supplier/create">
                        <Plus class="mr-1.5 size-4" />
                        Tambah Supplier
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="rounded-xl border border-sidebar-border bg-card p-4 shadow-xs">
            <div class="relative max-w-md">
                <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
                <Input
                    v-model="search"
                    placeholder="Cari nama supplier, kontak person, telepon, kota..."
                    class="h-9 pl-9 text-xs"
                />
            </div>
        </div>

        <!-- Supplier Table -->
        <div class="overflow-hidden rounded-xl border border-sidebar-border bg-card shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border/60 bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3">Nama Perusahaan / PBF</th>
                            <th class="px-4 py-3">Kontak Person</th>
                            <th class="px-4 py-3">No. Telepon</th>
                            <th class="px-4 py-3">Alamat</th>
                            <th class="px-4 py-3 text-center">Batch Terdaftar</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/40">
                        <tr
                            v-for="sup in suppliers.data"
                            :key="sup.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 font-semibold text-foreground">
                                <div class="flex items-center gap-2">
                                    <Building2 class="size-4 text-muted-foreground" />
                                    <span>{{ sup.nama }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                <div class="flex items-center gap-1.5">
                                    <UserCheck class="size-3.5 text-muted-foreground" />
                                    <span>{{ sup.kontak_person || '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                <div class="flex items-center gap-1.5">
                                    <Phone class="size-3.5 text-muted-foreground" />
                                    <span>{{ sup.telepon || '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground max-w-xs truncate">
                                {{ sup.alamat || '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-[11px] font-semibold text-secondary-foreground">
                                    {{ sup.batch_obats_count ?? 0 }} batch
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="sm"
                                        class="size-7 p-0 text-muted-foreground hover:text-foreground"
                                        title="Edit Supplier"
                                    >
                                        <Link :href="`/supplier/${sup.id}/edit`">
                                            <Pencil class="size-3.5" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="size-7 p-0 text-muted-foreground hover:text-destructive"
                                        title="Hapus Supplier"
                                        @click="confirmDelete(sup)"
                                    >
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="suppliers.data.length === 0">
                            <td colspan="6" class="p-8 text-center text-muted-foreground">
                                Belum ada data supplier yang terdaftar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-border/40 px-4">
                <Pagination
                    :links="suppliers.links"
                    :from="suppliers.from"
                    :to="suppliers.to"
                    :total="suppliers.total"
                />
            </div>
        </div>
    </div>

    <DeleteConfirmDialog
        :open="deleteDialogOpen"
        :loading="deleteLoading"
        :title="`Hapus Supplier ${selectedSupplier?.nama || ''}?`"
        description="Supplier yang memiliki riwayat pasokan batch obat tidak dapat dihapus."
        @update:open="deleteDialogOpen = $event"
        @confirm="handleDelete"
    />
</template>
