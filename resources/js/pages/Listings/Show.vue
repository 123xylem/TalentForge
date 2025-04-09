<script setup lang="ts">
import { useTextFormatter } from '@/composables/useTextFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const { truncate } = useTextFormatter();

const page = usePage<SharedData>();
const { listing, isOwner } = page.props;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Listings',
        href: '/listings',
    },
    {
        title: listing?.title ?? 'Job Listing',
        href: `/listings/${listing?.id}`,
    },
];

const form = useForm({});

const deleteListing = () => {
    if (confirm('Are you sure you want to delete this listing?')) {
        form.delete(route('listings.destroy', listing?.id));
    }
};
</script>

<template>
    <Head title="Listing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">
                    {{ listing?.title }}
                    <span class="">
                        <button class="rounded-full bg-blue-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600">
                            Apply
                        </button>
                    </span>
                </h1>
                <p class="text-sm text-neutral-500">{{ listing?.description }}</p>
                <p class="text-sm text-neutral-500">{{ listing?.salary }}</p>
                <p class="text-sm text-neutral-500">{{ listing?.location }}</p>
            </div>
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-semibold">Skills Required</h2>
                <div class="flex flex-wrap gap-2">
                    <div v-for="skill in listing?.skills" :key="skill.id" class="rounded-full bg-gray-700 px-2 py-1 text-sm text-neutral-500">
                        {{ skill.name }}
                    </div>
                </div>
                <h2 class="text-lg font-semibold">Category</h2>
                <div class="flex flex-wrap gap-2">
                    <div
                        v-for="category in listing?.categories"
                        :key="category.id"
                        class="rounded-full bg-gray-700 px-2 py-1 text-sm text-neutral-500"
                    >
                        {{ category.name }}
                    </div>
                </div>
                <div v-if="isOwner" class="buttons-row flex gap-2 rounded-lg">
                    <Link :href="route('listings.edit', listing?.id)">
                        <button class="rounded-full bg-blue-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600">
                            Edit
                        </button>
                    </Link>
                    <button
                        @click="deleteListing"
                        class="rounded-full bg-red-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-red-600"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
