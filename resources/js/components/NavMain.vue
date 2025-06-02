<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const userType = page.props?.auth?.user?.type === 'employer' ? 'Employer' : 'Job Seeker';
const userName = page.props?.auth?.user?.name;
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel class="mb-2 text-sm font-medium text-sidebar-foreground/80"> Hi {{ userName }} - {{ userType }} </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="item.href === page.url"
                    :tooltip="item.title"
                    class="text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                >
                    <Link :href="item.href" class="flex items-center gap-2">
                        <component :is="item.icon" class="h-5 w-5" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
