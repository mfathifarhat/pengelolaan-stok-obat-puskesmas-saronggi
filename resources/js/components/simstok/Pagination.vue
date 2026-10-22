<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    from?: number | null;
    to?: number | null;
    total?: number;
}>();
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-col items-center justify-between gap-4 py-3 sm:flex-row">
        <div v-if="total !== undefined" class="text-xs text-muted-foreground">
            Menampilkan <span class="font-medium text-foreground">{{ from ?? 0 }}</span> sampai
            <span class="font-medium text-foreground">{{ to ?? 0 }}</span> dari
            <span class="font-medium text-foreground">{{ total }}</span> data
        </div>
        <div class="flex flex-wrap items-center gap-1">
            <template v-for="(link, index) in links" :key="index">
                <span
                    v-if="link.url === null"
                    class="rounded-md px-3 py-1.5 text-xs text-muted-foreground/60 select-none"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors"
                    :class="
                        link.active
                            ? 'bg-primary text-primary-foreground font-semibold shadow-xs'
                            : 'text-foreground hover:bg-muted border border-border/40'
                    "
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
