<script setup lang="ts">
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

defineProps<{
    open: boolean;
    title?: string;
    description?: string;
    loading?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', val: boolean): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle class="text-base font-semibold text-rose-600 dark:text-rose-400">
                    {{ title || 'Konfirmasi Penghapusan' }}
                </DialogTitle>
                <DialogDescription class="text-sm text-muted-foreground">
                    {{ description || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.' }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex flex-row justify-end gap-2 pt-4">
                <DialogClose as-child>
                    <Button variant="outline" size="sm" :disabled="loading">Batal</Button>
                </DialogClose>
                <Button
                    variant="destructive"
                    size="sm"
                    :disabled="loading"
                    @click="emit('confirm')"
                >
                    <span v-if="loading">Menghapus...</span>
                    <span v-else>Hapus</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
