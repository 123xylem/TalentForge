<script setup lang="ts">
import FilterBar from '@/components/FilterBar.vue';
import ListingBlock from '@/components/Listing/ListingBlock.vue';
import Pagination from '@/components/Pagination.vue';
import { useFilterData } from '@/composables/useFilterData';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Job Listings', href: '/listings' }];

const page = usePage<SharedData>();
const countData = computed(() => page.props.paginatedListingData.total);
const paginatedListingData = computed(() => page.props.paginatedListingData);
console.log(paginatedListingData.value, 'paginatedListingData');
const { filters, computedFilters, resetFilters, applyFilters, onPageChange, updateFilter, initializeFiltersFromUrl } = useFilterData(
    'listings.index',
    'paginatedListingData',
);
//TODO do filters get called every time page is loaded?
onMounted(initializeFiltersFromUrl);
</script>

<template>
    <Head title="Listings" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">
                    Listings
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
                    @applyFilters="applyFilters"
                    @resetFilters="resetFilters"
                />

                <div v-if="paginatedListingData" class="listings-container">
                    <ListingBlock :listings="paginatedListingData" :rows="3" />
                </div>
                <Pagination v-if="paginatedListingData" :links="paginatedListingData.links" :filters="computedFilters" @pageChange="onPageChange" />
                <div v-else class="flex flex-col items-center justify-center gap-2">
                    <h1 class="text-xl font-semibold">No listings found</h1>
                    <p class="text-neutral-500">Create your first listing to get started</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
