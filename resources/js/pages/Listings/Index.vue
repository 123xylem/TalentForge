<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { useTextFormatter } from '@/composables/useTextFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
const { truncate } = useTextFormatter();

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
                    <div v-for="listing in paginatedListings.data" :key="listing.id" class="w-full">
                        <div class="flex flex-1 flex-col gap-2 rounded-lg border p-4 hover:cursor-pointer hover:bg-gray-900">
                            <Link :href="route('listings.show', listing.id)" class="block">
                                <h2 class="text-lg font-semibold">{{ listing.title }}</h2>
                                <div class="flex flex-row gap-2">
                                    <div
                                        v-for="skill in listing.skills?.slice(0, 3)"
                                        :key="skill.id"
                                        class="rounded-full bg-gray-700 px-2 py-1 text-xs text-neutral-500"
                                    >
                                        {{ skill.name }}
                                    </div>
                                </div>
                                <p class="text-sm text-neutral-500">
                                    {{ truncate(listing.description, 100) }}
                                </p>
                                <p class="text-sm text-neutral-500">{{ listing.salary }}</p>
                                <p class="text-sm text-neutral-500">{{ listing.location }}</p>
                                <p class="text-sm text-neutral-500">{{ new Date(listing.created_at ?? '').toLocaleDateString() }}</p>
                            </Link>
                        </div>
                    </div>
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
