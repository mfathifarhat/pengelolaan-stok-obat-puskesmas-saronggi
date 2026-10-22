<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    title: string;
    value: string | number;
    subtitle?: string;
    icon?: Component;
    colorVariant?: 'default' | 'emerald' | 'amber' | 'rose' | 'sky';
}>();
</script>

<template>
    <div class="relative overflow-hidden rounded-xl border border-sidebar-border bg-card p-5 shadow-xs transition-all duration-200 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ title }}</p>
                <h3 class="mt-2 text-2xl font-bold tracking-tight text-foreground">{{ value }}</h3>
                <p v-if="subtitle" class="mt-1 text-xs text-muted-foreground">{{ subtitle }}</p>
            </div>
            <div
                v-if="icon"
                class="flex size-12 items-center justify-center rounded-xl"
                :class="{
                    'bg-primary/10 text-primary': !colorVariant || colorVariant === 'default',
                    'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': colorVariant === 'emerald',
                    'bg-amber-500/10 text-amber-600 dark:text-amber-400': colorVariant === 'amber',
                    'bg-rose-500/10 text-rose-600 dark:text-rose-400': colorVariant === 'rose',
                    'bg-sky-500/10 text-sky-600 dark:text-sky-400': colorVariant === 'sky',
                }"
            >
                <component :is="icon" class="size-6" />
            </div>
        </div>
    </div>
</template>
