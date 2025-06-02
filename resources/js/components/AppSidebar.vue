<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, User } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import UserProfileList from './Messaging/List.vue';
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];
const footerNavItems: NavItem[] = [
    {
        title: 'Edit Profile',
        href: route('profile.edit'),
        icon: User,
    },
];
</script>

<template>
    <Sidebar
        collapsible="icon"
        state="collapsed"
        variant="inset"
        class="bg-sidebar-background z-[100] border-r border-sidebar-border text-sidebar-foreground"
    >
        <SidebarHeader class="border-b border-sidebar-border">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                        <Link :href="route('dashboard.index')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent class="border-b border-sidebar-border">
            <NavMain :items="mainNavItems" />
        </SidebarContent>
        <SidebarFooter class="border-t border-sidebar-border">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <UserProfileList />

    <slot />
</template>
