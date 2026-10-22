<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { KategoriObat } from '@/types';

defineProps<{
    open: boolean;
    kategoriList: KategoriObat[];
}>();

const emit = defineEmits<{
    (e: 'update:open', val: boolean): void;
    (e: 'updated'): void;
}>();

const editingId = ref<number | null>(null);

const form = useForm({
    nama: '',
    keterangan: '',
});

const startCreate = () => {
    editingId.value = null;
    form.reset();
};

const startEdit = (kat: KategoriObat) => {
    editingId.value = kat.id;
    form.nama = kat.nama;
    form.keterangan = kat.keterangan || '';
};

const submit = () => {
    if (editingId.value) {
        form.put(`/kategori-obat/${editingId.value}`, {
            onSuccess: () => {
                editingId.value = null;
                form.reset();
                emit('updated');
            },
        });
    } else {
        form.post('/kategori-obat', {
            onSuccess: () => {
                form.reset();
                emit('updated');
            },
        });
    }
};

const deleteCategory = (kat: KategoriObat) => {
    if (!confirm(`Hapus kategori "${kat.nama}"?`)) return;

    router.delete(`/kategori-obat/${kat.id}`, {
        onSuccess: () => {
            emit('updated');
        },
    });
};
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-lg">
            <DialogHeader>
                <DialogTitle class="text-lg font-bold">Kelola Kategori Obat</DialogTitle>
                <DialogDescription class="text-xs text-muted-foreground">
                    Tambah, ubah, atau hapus kategori golongan obat di Puskesmas Saronggi.
                </DialogDescription>
            </DialogHeader>

            <!-- Form Tambah / Edit -->
            <form @submit.prevent="submit" class="mt-3 rounded-lg border border-border/60 bg-muted/30 p-3.5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-foreground">
                        {{ editingId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                    </span>
                    <button
                        v-if="editingId"
                        type="button"
                        class="text-xs text-muted-foreground hover:underline"
                        @click="startCreate"
                    >
                        Batal Edit
                    </button>
                </div>

                <div class="space-y-1">
                    <Label for="nama-kat" class="text-xs">Nama Kategori *</Label>
                    <Input
                        id="nama-kat"
                        v-model="form.nama"
                        placeholder="Contoh: Analgesik, Antibiotik"
                        class="h-8 text-xs"
                        required
                    />
                    <p v-if="form.errors.nama" class="text-[11px] text-destructive">{{ form.errors.nama }}</p>
                </div>

                <div class="space-y-1">
                    <Label for="ket-kat" class="text-xs">Keterangan (Opsional)</Label>
                    <Input
                        id="ket-kat"
                        v-model="form.keterangan"
                        placeholder="Deskripsi singkat golongan obat"
                        class="h-8 text-xs"
                    />
                    <p v-if="form.errors.keterangan" class="text-[11px] text-destructive">{{ form.errors.keterangan }}</p>
                </div>

                <div class="flex justify-end pt-1">
                    <Button type="submit" size="sm" class="h-7 text-xs px-3" :disabled="form.processing">
                        <Plus v-if="!editingId" class="mr-1 size-3.5" />
                        <span>{{ editingId ? 'Simpan Perubahan' : 'Tambah Kategori' }}</span>
                    </Button>
                </div>
            </form>

            <!-- Daftar Kategori yang ada -->
            <div class="mt-4 space-y-2">
                <h4 class="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                    Daftar Kategori Saat Ini ({{ kategoriList.length }})
                </h4>

                <div class="divide-y divide-border/40 rounded-md border border-border/60 text-xs">
                    <div
                        v-for="kat in kategoriList"
                        :key="kat.id"
                        class="flex items-center justify-between p-2.5 hover:bg-muted/40"
                    >
                        <div>
                            <p class="font-medium text-foreground">{{ kat.nama }}</p>
                            <p v-if="kat.keterangan" class="text-[11px] text-muted-foreground">{{ kat.keterangan }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="size-7 p-0 text-muted-foreground hover:text-foreground"
                                @click="startEdit(kat)"
                                title="Edit"
                            >
                                <Pencil class="size-3.5" />
                            </Button>
                            <Button
                                variant="ghost"
                                size="sm"
                                class="size-7 p-0 text-muted-foreground hover:text-destructive"
                                @click="deleteCategory(kat)"
                                title="Hapus"
                            >
                                <Trash2 class="size-3.5" />
                            </Button>
                        </div>
                    </div>
                    <div v-if="kategoriList.length === 0" class="p-4 text-center text-xs text-muted-foreground">
                        Belum ada data kategori obat.
                    </div>
                </div>
            </div>

            <DialogFooter class="pt-3">
                <DialogClose as-child>
                    <Button variant="outline" size="sm" class="text-xs">Tutup</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
