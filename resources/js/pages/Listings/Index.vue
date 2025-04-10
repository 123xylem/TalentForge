<script setup lang="ts">
import PaginatedListingBlock from '@/components/PaginatedListingBlock.vue';
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
const { paginatedListings } = page.props;
</script>

<template>
    <Head title="Listings" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">Listings</h1>
                <h2 class="text-sm text-neutral-500">This is the listings page</h2>
                <div v-if="paginatedListings" class="listings-container grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <PaginatedListingBlock :paginatedListings="paginatedListings" />
                </div>
                <Pagination v-if="paginatedListings" :links="paginatedListings.links" />
                <div v-else class="flex flex-col items-center justify-center gap-2">
                    <h1 class="text-xl font-semibold">No listings found</h1>
                    <p class="text-neutral-500">Create your first listing to get started</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
