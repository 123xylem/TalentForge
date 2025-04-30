<script setup lang="ts">
import FilterBar from '@/components/FilterBar.vue';
import ListingBlock from '@/components/Listing/ListingBlock.vue';
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const page = usePage<SharedData>();
const { listings, userType } = page.props;
const isEmployer = userType === 'employer';

const rows = 3;

const computedListings = computed(() => {
    return listings;
});
console.log('LISTINGS:', listings.data.length, computedListings.value.length);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

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
    router.get(route('dashboard.index'), { page: 1 }, { only: ['listings'], preserveState: true, preserveScroll: true });
};

const applyFilters = () => {
    router.get(route('dashboard.index'), { ...computedFilters.value, page: 1 }, { only: ['listings'], preserveState: true, preserveScroll: true });
};

const onPageChange = (pageNum: number) => {
    router.get(
        route('dashboard.index'),
        { ...computedFilters.value, page: pageNum },
        { only: ['listings'], preserveState: true, preserveScroll: true },
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
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="computedListings" class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
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
            <h1 class="text-2xl font-semibold">Your {{ isEmployer ? 'Listings' : 'Applications' }}</h1>
            <ListingBlock :listings="computedListings" :rows="rows" />
        </div>
        <div v-else class="flex flex-col gap-4 rounded-xl p-4">
            <h1 class="text-2xl font-semibold">No listings found</h1>
        </div>
        <Pagination v-if="computedListings" :links="computedListings.links" :filters="computedFilters" @pageChange="onPageChange" />

        <div class="listings-buttons flex gap-4">
            <Link v-if="isEmployer" :href="route('listings.create')" class="btn btn-primary">Create Listing</Link>
            <Link :href="route('listings.index')" class="btn btn-secondary">All listings</Link>
        </div>
    </AppLayout>
</template>
