<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ArrowUpDown,
    FileText,
    LayoutGrid,
    Pill,
    Truck,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem, User } from '@/types';

const page = usePage();
const user = computed(() => page.props.auth?.user as User | undefined);
const isAdmin = computed(() => user.value?.role === 'admin');

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Data Obat',
            href: '/obat',
            icon: Pill,
        },
        {
            title: 'Data Supplier',
            href: '/supplier',
            icon: Truck,
        },
        {
            title: 'Mutasi Stok',
            href: '/stok',
            icon: ArrowUpDown,
        },
    ];

    if (isAdmin.value) {
        items.push(
            {
                title: 'Laporan',
                href: '/laporan',
                icon: FileText,
            },
            {
                title: 'Kelola Pengguna',
                href: '/pengguna',
                icon: Users,
            }
        );
    }

    return items;
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter v-if="footerNavItems.length > 0" :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
