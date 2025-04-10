<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';

const { props } = usePage();
const isJobHunter = props.userType === 'job-hunter' || false;
// getListingData if !jobHunter

// getAppliedListings if jobHunter

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
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <PaginatedListingBlock v-if="!isJobHunter" :paginatedListings="props.paginatedListings" />
            </div>
        </div>
    </AppLayout>
</template>
