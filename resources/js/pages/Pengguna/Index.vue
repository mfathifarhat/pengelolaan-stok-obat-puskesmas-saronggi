<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Pencil,
    Plus,
    Search,
    Shield,
    Trash2,
    UserCheck,
    Users,
} from '@lucide/vue';
import { ref, watch } from 'vue';
import DeleteConfirmDialog from '@/components/simstok/DeleteConfirmDialog.vue';
import Pagination from '@/components/simstok/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { PaginatedData, User } from '@/types';

const props = defineProps<{
    users: PaginatedData<User>;
    filters: {
        search?: string;
        role?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Kelola Pengguna', href: '/pengguna' },
        ],
    },
});

const search = ref(props.filters.search || '');
const role = ref(props.filters.role || '');

const deleteDialogOpen = ref(false);
const selectedUser = ref<User | null>(null);
const deleteLoading = ref(false);

const applyFilters = () => {
    router.get(
        '/pengguna',
        {
            search: search.value || undefined,
            role: role.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 350);
});

watch(role, () => {
    applyFilters();
});

const confirmDelete = (u: User) => {
    selectedUser.value = u;
    deleteDialogOpen.value = true;
};

const handleDelete = () => {
    if (!selectedUser.value) return;
    deleteLoading.value = true;
    router.delete(`/pengguna/${selectedUser.value.id}`, {
        onFinish: () => {
            deleteLoading.value = false;
            deleteDialogOpen.value = false;
            selectedUser.value = null;
        },
    });
};
</script>

<template>
    <Head title="Kelola Pengguna - SIMStok Puskesmas Saronggi" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                    Kelola Pengguna & Hak Akses
                </h1>
                <p class="text-xs text-muted-foreground sm:text-sm">
                    Manajemen akun pengguna sistem untuk Petugas Farmasi dan Admin / Atasan Puskesmas.
                </p>
            </div>
            <div>
                <Button as-child size="sm" class="bg-primary text-xs">
                    <Link href="/pengguna/create">
                        <Plus class="mr-1.5 size-4" />
                        Tambah Pengguna Baru
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="grid grid-cols-1 gap-3 rounded-xl border border-sidebar-border bg-card p-4 shadow-xs sm:grid-cols-12">
            <div class="relative sm:col-span-8">
                <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
                <Input
                    v-model="search"
                    placeholder="Cari nama pengguna atau email..."
                    class="h-9 pl-9 text-xs"
                />
            </div>
            <div class="sm:col-span-4">
                <select
                    v-model="role"
                    class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                >
                    <option value="">Semua Peran (Role)</option>
                    <option value="admin">Admin / Atasan</option>
                    <option value="petugas">Petugas Farmasi</option>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <div class="overflow-hidden rounded-xl border border-sidebar-border bg-card shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border/60 bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3">Nama Pengguna</th>
                            <th class="px-4 py-3">Alamat Email</th>
                            <th class="px-4 py-3">Peran / Role</th>
                            <th class="px-4 py-3">Status Verifikasi</th>
                            <th class="px-4 py-3">Tanggal Dibuat</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/40">
                        <tr
                            v-for="u in users.data"
                            :key="u.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 font-semibold text-foreground">
                                <div class="flex items-center gap-2">
                                    <div class="flex size-7 items-center justify-center rounded-full bg-primary/10 text-primary font-bold">
                                        {{ u.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div>{{ u.name }}</div>
                                        <div class="text-[11px] font-mono font-normal text-muted-foreground">@{{ u.username || '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground font-mono">
                                {{ u.email }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    v-if="u.role === 'admin'"
                                    class="inline-flex items-center gap-1 rounded-full bg-purple-500/10 px-2 py-0.5 text-[11px] font-bold text-purple-700 dark:text-purple-400"
                                >
                                    <Shield class="size-3" />
                                    Admin / Atasan
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 dark:text-emerald-400"
                                >
                                    <UserCheck class="size-3" />
                                    Petugas Farmasi
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                <span v-if="u.email_verified_at" class="text-emerald-600 dark:text-emerald-400 font-medium">
                                    ✓ Terverifikasi
                                </span>
                                <span v-else class="text-amber-600 dark:text-amber-400">
                                    Belum
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">
                                {{ new Date(u.created_at).toLocaleDateString('id-ID') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="sm"
                                        class="size-7 p-0 text-muted-foreground hover:text-foreground"
                                        title="Edit Pengguna"
                                    >
                                        <Link :href="`/pengguna/${u.id}/edit`">
                                            <Pencil class="size-3.5" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="size-7 p-0 text-muted-foreground hover:text-destructive"
                                        title="Hapus Pengguna"
                                        @click="confirmDelete(u)"
                                    >
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="p-8 text-center text-muted-foreground">
                                Tidak ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-border/40 px-4">
                <Pagination
                    :links="users.links"
                    :from="users.from"
                    :to="users.to"
                    :total="users.total"
                />
            </div>
        </div>
    </div>

    <DeleteConfirmDialog
        :open="deleteDialogOpen"
        :loading="deleteLoading"
        :title="`Hapus Pengguna ${selectedUser?.name || ''}?`"
        description="Akun ini tidak akan dapat login lagi ke sistem SIMStok Puskesmas Saronggi."
        @update:open="deleteDialogOpen = $event"
        @confirm="handleDelete"
    />
</template>
