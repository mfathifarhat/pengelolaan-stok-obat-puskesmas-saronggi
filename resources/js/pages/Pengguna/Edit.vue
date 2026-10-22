<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { User } from '@/types';

const props = defineProps<{
    user: User;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Kelola Pengguna', href: '/pengguna' },
            { title: 'Edit Pengguna', href: `/pengguna` },
        ],
    },
});

const form = useForm({
    name: props.user.name,
    username: props.user.username || '',
    email: props.user.email,
    role: props.user.role || 'petugas',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(`/pengguna/${props.user.id}`);
};
</script>

<template>
    <Head :title="`Edit ${user.name} - SIMStok Puskesmas Saronggi`" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 max-w-2xl">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="sm" class="size-8 p-0">
                <Link href="/pengguna">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">
                    Edit Akun Pengguna
                </h1>
                <p class="text-xs text-muted-foreground">
                    Perbarui profil, hak akses peran, atau reset password akun {{ user.name }}.
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="rounded-xl border border-sidebar-border bg-card p-5 shadow-xs space-y-4">
            <!-- Nama Lengkap -->
            <div class="space-y-1.5">
                <Label for="name" class="text-xs">Nama Lengkap & Gelar *</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    class="text-xs"
                    required
                />
                <p v-if="form.errors.name" class="text-[11px] text-destructive">{{ form.errors.name }}</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Username -->
                <div class="space-y-1.5">
                    <Label for="username" class="text-xs">Username (Untuk Login) *</Label>
                    <Input
                        id="username"
                        v-model="form.username"
                        class="text-xs font-mono"
                        required
                    />
                    <p class="text-[10px] text-muted-foreground">Digunakan petugas untuk login ke sistem.</p>
                    <p v-if="form.errors.username" class="text-[11px] text-destructive">{{ form.errors.username }}</p>
                </div>

                <!-- Email -->
                <div class="space-y-1.5">
                    <Label for="email" class="text-xs">Alamat Email *</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        class="text-xs font-mono"
                        required
                    />
                    <p v-if="form.errors.email" class="text-[11px] text-destructive">{{ form.errors.email }}</p>
                </div>
            </div>

            <!-- Role / Hak Akses -->
            <div class="space-y-1.5">
                <Label for="role" class="text-xs">Peran & Hak Akses *</Label>
                <select
                    id="role"
                    v-model="form.role"
                    class="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    required
                >
                    <option value="petugas">Petugas Farmasi (Operasional Obat, Supplier, & Mutasi Stok)</option>
                    <option value="admin">Admin / Atasan (Akses Penuh: Kelola Pengguna, Laporan, & Seluruh Modul)</option>
                </select>
                <p v-if="form.errors.role" class="text-[11px] text-destructive">{{ form.errors.role }}</p>
            </div>

            <div class="space-y-3 pt-3 border-t border-border/40">
                <p class="text-xs font-semibold text-foreground">Ganti Password (Kosongkan jika tidak ingin mengubah)</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Password Baru -->
                    <div class="space-y-1.5">
                        <Label for="password" class="text-xs">Password Baru</Label>
                        <Input
                            id="password"
                            type="password"
                            v-model="form.password"
                            placeholder="Minimal 8 karakter"
                            class="text-xs"
                        />
                        <p v-if="form.errors.password" class="text-[11px] text-destructive">{{ form.errors.password }}</p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="space-y-1.5">
                        <Label for="password_confirmation" class="text-xs">Ulangi Password Baru</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            placeholder="Konfirmasi password"
                            class="text-xs"
                        />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-border/60">
                <Button as-child variant="outline" size="sm" class="text-xs">
                    <Link href="/pengguna">Batal</Link>
                </Button>
                <Button type="submit" size="sm" class="text-xs" :disabled="form.processing">
                    <Save class="mr-1.5 size-4" />
                    Simpan Perubahan
                </Button>
            </div>
        </form>
    </div>
</template>
