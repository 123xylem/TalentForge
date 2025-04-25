<script setup lang="ts">
import ListingApplicationModal from '@/components/Listing/ListingApplicationModal.vue';
import StatusLabel from '@/components/StatusLabel.vue';
import { useTextFormatter } from '@/composables/useTextFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import { ref } from 'vue';

const { truncate } = useTextFormatter();

const page = usePage<SharedData>();
const { listing, isOwner, auth, userApplicationStatus = null, listingApplications = [] } = page.props;
const user = auth.user;
const showApplications = isOwner && listingApplications?.length > 0;
console.log('here', listingApplications, userApplicationStatus);
// const userApplied = !user.status.includes('read', 'unread');

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
//TODO: Delete listing
// const deleteListing = () => {
//     if (confirm('Are you sure you want to delete this listing?')) {
//         form.delete(route('listings.destroy', listing?.id));
//     }
// };

//TODO: Filter listings BY:
// - Category
// - Skills
// - Salary
// - Location
// - Application Status

const modalRef = ref<InstanceType<typeof ListingApplicationModal> | null>(null);

const openModal = () => {
    modalRef.value?.openModal();
};

const closeModal = () => {
    modalRef.value?.closeModal();
};

const toggleModal = () => {
    if (isModalOpen.value) {
        closeModal();
    } else {
        openModal();
    }
};

const isModalOpen = ref(false);
</script>

<template>
    <Head title="Listing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <ListingApplicationModal ref="modalRef" :listing_id="listing?.id" :user="user" v-model:is-open="isModalOpen" />
        <div class="grid h-full w-full justify-center gap-4 rounded-xl p-4" :class="!showApplications ? 'grid-cols-1' : 'grid-cols-2'">
            <!-- Job Listing Details -->
            <div class="flex flex-1 flex-col gap-4 rounded-lg p-2">
                <h2 class="text-lg font-semibold">Job Listing</h2>
                <div class="space-y-4">
                    <div class="transform rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md">
                        <h1 class="text-2xl font-semibold text-gray-900">
                            {{ listing?.title }}
                            <span v-if="!isOwner && user.type === 'job_hunter' && !userApplicationStatus" class="">
                                <button
                                    @click="toggleModal"
                                    class="rounded-full bg-blue-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                                >
                                    {{ isModalOpen ? 'Cancel' : 'Apply' }}
                                </button>
                            </span>
                            <StatusLabel v-if="userApplicationStatus" :status="userApplicationStatus" />
                        </h1>
                        <p class="text-sm text-neutral-500">{{ listing?.description }}</p>
                        <p class="text-sm text-neutral-500">{{ listing?.salary }}</p>
                        <p class="text-sm text-neutral-500">{{ listing?.location }}</p>
                        <div class="flex flex-col gap-4">
                            <h2 class="text-lg font-semibold text-gray-900">Skills Required</h2>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="skill in listing?.skills"
                                    :key="skill.id"
                                    class="rounded-full bg-gray-700 px-2 py-1 text-sm text-neutral-500"
                                >
                                    {{ skill.name }}
                                </div>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900">Category</h2>
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
                                    <button
                                        class="rounded-full bg-blue-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                                    >
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
                </div>
            </div>
            <!-- Applications -->
            <div v-if="showApplications" class="order-last flex flex-1 flex-col gap-4 rounded-lg p-2">
                <h2 class="text-lg font-semibold">Applications</h2>
                <div class="space-y-4">
                    <div
                        v-for="application in listingApplications"
                        :key="application.id"
                        class="transform rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all duration-200 hover:scale-[1.01] hover:shadow-md"
                    >
                        <Link :href="route('listing-applications.show', application.id)" class="block">
                            <!-- Header Section -->
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ application.name }}
                                    </h3>
                                    <StatusLabel v-if="application.status" :status="application.status" />
                                </div>
                                <span class="text-sm text-gray-500"> Applied {{ new Date(application.created_at).toLocaleDateString() }} </span>
                            </div>

                            <!-- Grid Layout for Details -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span>{{ application.email }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                                        />
                                    </svg>
                                    <span>{{ application.phone }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span>{{ application.address }}</span>
                                </div>
                            </div>

                            <!-- Documents Section -->
                            <div class="mt-4 flex flex-wrap gap-4">
                                <a
                                    v-if="application.cv"
                                    :href="application.cv"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 rounded-md bg-gray-50 px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100"
                                >
                                    <FileText class="h-4 w-4" />
                                    Download CV
                                </a>

                                <div v-if="application.cover_letter" class="flex-1">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Cover Letter:</span>
                                        {{ truncate(application.cover_letter, 100) }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
