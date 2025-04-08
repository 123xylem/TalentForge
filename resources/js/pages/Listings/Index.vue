<script setup lang="ts">
import StatusContainer from '@/components/StatusContainer.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const page = usePage<SharedData>();
const { flash, listings } = page.props;

console.log(listings, 'listings', page.props);
</script>

<template>
    <Head title="Dashboard" />
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <StatusContainer :errors="flash.errors" :success="flash.success" />
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <h1>Listings</h1>
            <p>This is the listings page</p>
            <div v-if="listings" class="listings-container flex flex-col gap-4">
                <p>There are {{ listings.length }} listings</p>
                <div v-for="listing in listings" :key="listing.id">
                    <div>
                        <h2>{{ listing.title }}</h2>
                        <p>{{ listing.description }}</p>
                        <p>{{ listing.salary }}</p>
                        <p>{{ listing.location }}</p>
                        <p>{{ listing.created_at }}</p>
                    </div>
                </div>
            </div>
            <div v-else class="flex flex-col items-center justify-center gap-2">
                <h1 class="text-xl font-semibold">No listings found</h1>
                <p class="text-neutral-500">Create your first listing to get started</p>
            </div>
        </div>
    </div>
</template>
