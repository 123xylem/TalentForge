<script setup lang="ts">
import { useTextFormatter } from '@/composables/useTextFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';

const { truncate } = useTextFormatter();

const page = usePage<SharedData>();
const { listing, isOwner, auth, userApplicationStatus = null, flash, listingApplication, applicants } = page.props;
const user = auth.user;
console.log(listingApplication, applicants);
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
    {
        title: 'Listing Applications',
        href: `/listing-applications/${listingApplication.id}`,
    },
];

// const deleteListing = () => {
//     if (confirm('Are you sure you want to delete this listing?')) {
//         form.delete(route('listings.destroy', listing?.id));
//     }
// };
// const modalRef = ref<InstanceType<typeof ListingApplicationModal> | null>(null);

// const openModal = () => {
//     modalRef.value?.openModal();
// };

// const closeModal = () => {
//     modalRef.value?.closeModal();
// };

// const toggleModal = () => {
//     if (isModalOpen.value) {
//         closeModal();
//     } else {
//         openModal();
//     }
// };

// const isModalOpen = ref(false);
</script>

<template>
    <Head title="Listing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">
                    {{ listingApplication.listing?.title }}
                </h1>
                <p class="text-sm text-neutral-500">{{ listingApplication?.description }}</p>
                <p class="text-sm text-neutral-500">{{ listingApplication?.salary }}</p>
                <p class="text-sm text-neutral-500">{{ listingApplication?.location }}</p>
            </div>

            <div v-if="isOwner" class="buttons-row flex gap-2 rounded-lg">
                <Link :href="route('listings.edit', listing?.id)">
                    <button class="rounded-full bg-blue-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600">
                        Edit
                    </button>
                </Link>
                <!-- <button
                        @click="deleteListing"
                        class="rounded-full bg-red-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-red-600"
                    >
                        Delete
                    </button> -->
            </div>
        </div>
    </AppLayout>
</template>
