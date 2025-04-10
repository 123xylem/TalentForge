<script setup lang="ts">
import ListingBlock from '@/components/Listing/ListingBlock.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
const page = usePage<SharedData>();
const { listings, userType } = page.props;
const isEmployer = userType === 'employer';
console.log(page.props, listings);
const rows = 3;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <h1 class="text-2xl font-semibold">Your {{ isEmployer ? 'Listings' : 'Applications' }}</h1>
            <ListingBlock :listings="listings" :rows="{ rows }" />
        </div>
    </AppLayout>
</template>
