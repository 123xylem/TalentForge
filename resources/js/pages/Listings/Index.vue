<script setup lang="ts">
import FilterBar from '@/components/FilterBar.vue';
import ListingBlock from '@/components/Listing/ListingBlock.vue';
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Job Listings', href: '/listings' }];

const page = usePage<SharedData>();
const paginatedListingData = computed(() => page.props.paginatedListingData);

const filters = ref({
    textSearch: '',
    locationSearch: '',
    category: [],
    skills: [],
    salary: '',
    applicationStatus: '',
});

// Initialize filters from URL on mount
onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    filters.value.textSearch = params.get('textSearch') || '';
    filters.value.locationSearch = params.get('locationSearch') || '';
    filters.value.category = params.getAll('category[]') || [];
    filters.value.skills = params.getAll('skills[]') || [];
    filters.value.salary = params.get('salary') || '';
    filters.value.applicationStatus = params.get('applicationStatus') || '';
});

const applyFilters = () => {
    router.get(route('listings.index'), { ...filters.value, page: 1 }, { only: ['paginatedListingData'], preserveState: true, preserveScroll: true });
};

const onPageChange = (pageNum: number) => {
    router.get(
        route('listings.index'),
        { ...filters.value, page: pageNum },
        { only: ['paginatedListingData'], preserveState: true, preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Listings" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <FilterBar
                v-bind="filters.value"
                @update:textSearch="filters.value.textSearch = $event"
                @update:locationSearch="filters.value.locationSearch = $event"
                @update:category="filters.value.category = $event"
                @update:skills="filters.value.skills = $event"
                @update:salary="filters.value.salary = $event"
                @update:applicationStatus="filters.value.applicationStatus = $event"
                @applyFilters="applyFilters"
            />
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">Listings</h1>
                <h2 class="text-sm text-neutral-500">This is the listings page</h2>
                <div v-if="paginatedListingData.value" class="listings-container">
                    <ListingBlock :listings="paginatedListingData.value" :rows="3" />
                </div>
                <Pagination
                    v-if="paginatedListingData.value"
                    :links="paginatedListingData.value.links"
                    :filters="filters.value"
                    @pageChange="onPageChange"
                />
                <div v-else class="flex flex-col items-center justify-center gap-2">
                    <h1 class="text-xl font-semibold">No listings found</h1>
                    <p class="text-neutral-500">Create your first listing to get started</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
