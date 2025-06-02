<script setup lang="ts">
import FilterBar from '@/components/FilterBar.vue';
import ListingBlock from '@/components/Listing/ListingBlock.vue';
import Pagination from '@/components/Pagination.vue';
import { useFilterData } from '@/composables/useFilterData';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
const page = usePage<SharedData>();
const { userType } = page.props;
const isEmployer = userType === 'employer';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
const rows = 3;

const { filters, computedFilters, resetFilters, applyFilters, onPageChange, updateFilter, initializeFiltersFromUrl } = useFilterData(
    'dashboard.index',
    'listings',
);
const computedListings = computed(() => page.props.listings);
const countData = computed(() => page.props.listings.total);

// console.log('LISTINGS:', computedListings.value);

// Initialize filters from URL via composable on mount
onMounted(initializeFiltersFromUrl);

//TODO: Filter applicants for employer dashboard
// Todo: show applicants on employer dashboard
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="computedListings" class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <h1 class="text-2xl font-semibold">
                Your {{ isEmployer ? 'Listings' : 'Applications' }}
                <span class="text-sm text-neutral-500" v-if="countData"> {{ countData }} found</span>
                <span class="text-sm text-neutral-500" v-else>No listings found</span>
            </h1>
            <FilterBar
                v-bind="filters.value"
                @update:textSearch="updateFilter('textSearch', $event)"
                @update:locationSearch="updateFilter('locationSearch', $event)"
                @update:category="updateFilter('category', $event)"
                @update:skills="updateFilter('skills', $event)"
                @update:salary="updateFilter('salary', $event)"
                @update:applicationStatus="updateFilter('applicationStatus', $event)"
                :applicationFilter="true"
                @applyFilters="applyFilters"
                @resetFilters="resetFilters"
            />

            <ListingBlock :listings="computedListings" :rows="rows" />
        </div>
        <div v-else class="flex flex-col gap-4 rounded-xl p-4">
            <h1 class="text-2xl font-semibold">No listings found</h1>
        </div>
        <div class="listings-buttons m-auto flex items-center gap-4">
            <Link v-if="isEmployer" :href="route('listings.create')" class="btn-primary rounded-lg px-4 py-2">Create Listing</Link>
            <Link :href="route('listings.index')" class="btn-secondary rounded-lg px-4 py-2">All listings</Link>
        </div>

        <Pagination class="mt-4" v-if="computedListings" :links="computedListings.links" :filters="computedFilters" @pageChange="onPageChange" />
    </AppLayout>
</template>
