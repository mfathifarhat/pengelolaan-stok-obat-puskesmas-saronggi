<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Supplier } from '@/types';

const props = defineProps<{
    supplier: Supplier;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Data Supplier', href: '/supplier' },
            { title: 'Edit Supplier', href: `/supplier` },
        ],
    },
});

const form = useForm({
    nama: props.supplier.nama,
    alamat: props.supplier.alamat || '',
    telepon: props.supplier.telepon || '',
    kontak_person: props.supplier.kontak_person || '',
});

const submit = () => {
    form.put(`/supplier/${props.supplier.id}`);
};
</script>

<template>
    <Head :title="`Edit ${supplier.nama} - SIMStok Puskesmas Saronggi`" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 max-w-2xl">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="sm" class="size-8 p-0">
                <Link href="/supplier">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">
                    Edit Data Supplier
                </h1>
                <p class="text-xs text-muted-foreground">
                    Perbarui rincian kontak dan profil distributor {{ supplier.nama }}.
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="rounded-xl border border-sidebar-border bg-card p-5 shadow-xs space-y-4">
            <!-- Nama Supplier -->
            <div class="space-y-1.5">
                <Label for="nama" class="text-xs">Nama Perusahaan / PBF *</Label>
                <Input
                    id="nama"
                    v-model="form.nama"
                    class="text-xs"
                    required
                />
                <p v-if="form.errors.nama" class="text-[11px] text-destructive">{{ form.errors.nama }}</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Kontak Person -->
                <div class="space-y-1.5">
                    <Label for="kontak_person" class="text-xs">Nama Narahubung / Sales</Label>
                    <Input
                        id="kontak_person"
                        v-model="form.kontak_person"
                        class="text-xs"
                    />
                    <p v-if="form.errors.kontak_person" class="text-[11px] text-destructive">{{ form.errors.kontak_person }}</p>
                </div>

                <!-- Telepon -->
                <div class="space-y-1.5">
                    <Label for="telepon" class="text-xs">No. Telepon / WhatsApp</Label>
                    <Input
                        id="telepon"
                        v-model="form.telepon"
                        class="text-xs"
                    />
                    <p v-if="form.errors.telepon" class="text-[11px] text-destructive">{{ form.errors.telepon }}</p>
                </div>
            </div>

            <!-- Alamat -->
            <div class="space-y-1.5">
                <Label for="alamat" class="text-xs">Alamat Kantor / Gudang Distributor</Label>
                <textarea
                    id="alamat"
                    v-model="form.alamat"
                    rows="3"
                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-xs shadow-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                ></textarea>
                <p v-if="form.errors.alamat" class="text-[11px] text-destructive">{{ form.errors.alamat }}</p>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-border/60">
                <Button as-child variant="outline" size="sm" class="text-xs">
                    <Link href="/supplier">Batal</Link>
                </Button>
                <Button type="submit" size="sm" class="text-xs" :disabled="form.processing">
                    <Save class="mr-1.5 size-4" />
                    Simpan Perubahan
                </Button>
            </div>
        </form>
    </div>
</template>
