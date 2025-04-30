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
console.log(paginatedListingData.value, 'paginatedListingData');
const filters = ref({
    textSearch: '',
    locationSearch: '',
    category: '',
    skills: [],
    salary: '',
    applicationStatus: '',
});

const computedFilters = computed(() => {
    const filteredFilters = {};
    Object.keys(filters.value).forEach((key) => {
        if (key === 'skills') {
            if (Array.isArray(filters.value[key]) && filters.value[key].length > 0) {
                filteredFilters[key] = filters.value[key];
            }
        } else if (filters.value[key] !== '' && filters.value[key] !== []) {
            filteredFilters[key] = filters.value[key];
        }
    });
    return filteredFilters;
});
// Initialize filters from URL on mount
onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    filters.value.textSearch = params.get('textSearch') || '';
    filters.value.locationSearch = params.get('locationSearch') || '';
    filters.value.category = params.get('category') || '';
    filters.value.skills = params.getAll('skills[]') || [];
    filters.value.salary = params.get('salary') || '';
    filters.value.applicationStatus = params.get('applicationStatus') || '';
});

const resetForm = () => {
    filters.value = {
        textSearch: '',
        locationSearch: '',
        category: '',
        skills: [],
        salary: '',
        applicationStatus: '',
    };
    router.get(route('listings.index'), { page: 1 }, { only: ['paginatedListingData'], preserveState: true, preserveScroll: true });
};
const applyFilters = () => {
    console.log(computedFilters.value, 'FILTERED FILTERS');
    router.get(
        route('listings.index'),
        { ...computedFilters.value, page: 1 },
        { only: ['paginatedListingData'], preserveState: true, preserveScroll: true },
    );
};

const onPageChange = (pageNum: number) => {
    router.get(
        route('listings.index'),
        { ...computedFilters.value, page: pageNum },
        { only: ['paginatedListingData'], preserveState: true, preserveScroll: true },
    );
};

const updateFilter = (key: string, value: string) => {
    if (key === 'category') {
        filters.value['category'] = value;
    } else if (key === 'skills') {
        if (filters.value['skills'].includes(value)) {
            filters.value['skills'] = filters.value['skills'].filter((skill) => skill !== value);
        } else {
            filters.value['skills'].push(value);
        }
    } else {
        filters.value[key] = value;
    }
};
</script>

<template>
    <Head title="Listings" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <FilterBar
                v-bind="filters.value"
                @update:textSearch="updateFilter('textSearch', $event)"
                @update:locationSearch="updateFilter('locationSearch', $event)"
                @update:category="updateFilter('category', $event)"
                @update:skills="updateFilter('skills', $event)"
                @update:salary="updateFilter('salary', $event)"
                @update:applicationStatus="updateFilter('applicationStatus', $event)"
                @applyFilters="applyFilters"
                @resetForm="resetForm"
            />
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">Listings</h1>
                <h2 class="text-sm text-neutral-500">This is the listings page</h2>
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
