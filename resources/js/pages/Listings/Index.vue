<script setup lang="ts">
import FilterBar from '@/components/FilterBar.vue';
import ListingBlock from '@/components/Listing/ListingBlock.vue';
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Listings',
        href: '/listings',
    },
];

const page = usePage<SharedData>();
const { paginatedListingData } = page.props;

const rows = 3;
</script>

<template>
    <Head title="Listings" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <FilterBar title="Listings!" :applicationFilter="true" />
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">Listings</h1>
                <h2 class="text-sm text-neutral-500">This is the listings page</h2>
                <div v-if="paginatedListingData" class="listings-container">
                    <ListingBlock :listings="paginatedListingData" :rows="rows" />
                </div>
                <Pagination v-if="paginatedListingData" :links="paginatedListingData.links" />
                <div v-else class="flex flex-col items-center justify-center gap-2">
                    <h1 class="text-xl font-semibold">No listings found</h1>
                    <p class="text-neutral-500">Create your first listing to get started</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
