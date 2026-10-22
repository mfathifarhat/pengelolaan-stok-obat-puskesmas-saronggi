<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

defineOptions({
    layout: {
        title: 'Pendaftaran Dinonaktifkan',
        description: 'Pendaftaran akun mandiri tidak tersedia untuk sistem internal ini.',
    },
});
</script>

<template>
    <Head title="Pendaftaran Dinonaktifkan" />

    <div class="space-y-4 text-center">
        <div class="rounded-lg border border-border/60 bg-muted/30 p-4 text-xs text-muted-foreground">
            <p class="font-medium text-foreground">Akses Terbatas</p>
            <p class="mt-1">
                Akun staf dan petugas hanya dapat didaftarkan langsung oleh Administrator Puskesmas Saronggi melalui modul Kelola Pengguna.
            </p>
        </div>

        <Button as-child class="w-full text-xs">
            <Link href="/login">Kembali ke Halaman Login</Link>
        </Button>
    </div>
</template>
