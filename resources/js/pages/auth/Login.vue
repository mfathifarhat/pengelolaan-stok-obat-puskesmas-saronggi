<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Masuk ke SIMStok Puskesmas Saronggi',
        description: 'Masukkan username dan password akun Anda untuk melanjutkan',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Masuk - SIMStok Puskesmas Saronggi" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-emerald-600"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <!-- Username Field -->
            <div class="grid gap-1.5">
                <Label for="username" class="text-xs">Username</Label>
                <Input
                    id="username"
                    type="text"
                    name="username"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="username"
                    placeholder="Contoh: admin / petugas"
                    class="h-9 text-xs"
                />
                <InputError :message="errors.username" />
            </div>

            <!-- Password Field -->
            <div class="grid gap-1.5">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-xs">Password</Label>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Masukkan password akun"
                    class="h-9 text-xs"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-2 text-xs text-muted-foreground cursor-pointer">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Ingat saya di perangkat ini</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 w-full bg-primary text-xs font-semibold"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" class="mr-2" />
                Masuk ke SIMStok
            </Button>
        </div>

        <div class="mt-4 rounded-lg border border-border/50 bg-muted/30 p-3 text-center text-xs text-muted-foreground">
            <p class="font-medium text-foreground">Akses Terbatas</p>
            <p class="mt-0.5 text-[11px]">Akun hanya dapat dibuat dan dikelola oleh Administrator Puskesmas.</p>
        </div>
    </Form>
</template>
