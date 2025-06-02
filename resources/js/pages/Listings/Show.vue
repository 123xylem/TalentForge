<script setup lang="ts">
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import ListingApplicationModal from '@/components/Listing/ListingApplicationModal.vue';
import StatusLabel from '@/components/StatusLabel.vue';
import { useTextFormatter } from '@/composables/useTextFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import { ref } from 'vue';

const { truncate } = useTextFormatter();
const page = usePage<SharedData>();
const { listing, isOwner, auth, userApplicationStatus = null, listingApplications = [] } = page.props;

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

const showConfirmationModal = ref(false);
const isApplicationModalOpen = ref(false);
const user = auth.user;
const showApplications = isOwner && listingApplications?.length > 0;
const status = ref<typeof userApplicationStatus>(userApplicationStatus);

//TODO: Delete listing
// const deleteListing = () => {
//     if (confirm('Are you sure you want to delete this listing?')) {
//         form.delete(route('listings.destroy', listing?.id));
//     }
// };

const modalRef = ref<InstanceType<typeof ListingApplicationModal> | null>(null);

const toggleApplicationModal = () => {
    if (isApplicationModalOpen.value) {
        modalRef.value?.closeModal();
    } else {
        modalRef.value?.openModal();
    }
};

const toggleListingClosed = () => {
    showConfirmationModal.value = true;
};

const form = useForm({
    ...listing,
    skills: [2, 3],
});

const updateApplicationStatus = (updatedStatus: string) => {
    status.value = updatedStatus;
};

const updateListingStatus = () => {
    showConfirmationModal.value = false;
    form.listingClosed = !listing?.listingClosed;

    form.put(route('listings.update', listing?.id), {
        preserveState: false,
        onSuccess: (message) => {
            console.log('success', listing?.listingClosed, message);
        },
        onError: (e) => {
            console.log('error', e);
        },
    });
};
</script>

<template>
    <Head title="Listing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <ConfirmationModal
            :title="listing?.listingClosed ? 'Reopen Listing' : 'Close Listing'"
            :description="listing?.listingClosed ? 'Are you sure you want to reopen this listing?' : 'Are you sure you want to close this listing?'"
            :confirmModal="showConfirmationModal"
            :confirmModalText="listing?.listingClosed == true ? 'Reopen' : 'Close'"
            @confirm="updateListingStatus"
            @cancel="showConfirmationModal = false"
        />

        <ListingApplicationModal
            ref="modalRef"
            :listing_id="listing?.id"
            :user="user"
            v-model:is-open="isApplicationModalOpen"
            @update:status="updateApplicationStatus"
        />

        <div class="flex flex-col gap-6 p-4 lg:flex-row">
            <!-- Job Listing Details -->
            <div class="flex-1 space-y-6">
                <div class="rounded-lg border bg-card p-6 shadow-sm">
                    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <h1 class="text-2xl font-semibold text-card-foreground">
                            {{ listing?.title }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-2">
                            <span v-if="!isOwner && user.type === 'job_hunter' && !status">
                                <button
                                    @click="toggleApplicationModal"
                                    class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                                >
                                    {{ isApplicationModalOpen ? 'Cancel' : 'Apply' }}
                                </button>
                            </span>
                            <StatusLabel v-if="status" :status="status" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <h2 class="text-lg font-semibold text-card-foreground">Description</h2>
                            <p class="text-muted-foreground">{{ listing?.description }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <h2 class="text-lg font-semibold text-card-foreground">Salary</h2>
                                <p class="text-muted-foreground">{{ listing?.salary }}</p>
                            </div>
                            <div class="space-y-2">
                                <h2 class="text-lg font-semibold text-card-foreground">Location</h2>
                                <p class="text-muted-foreground">{{ listing?.location }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h2 class="text-lg font-semibold text-card-foreground">Skills Required</h2>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="skill in listing?.skills"
                                    :key="skill.id"
                                    class="rounded-full bg-accent/10 px-3 py-1 text-sm text-accent-foreground"
                                >
                                    {{ skill.name }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h2 class="text-lg font-semibold text-card-foreground">Categories</h2>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="category in listing?.categories"
                                    :key="category.id"
                                    class="rounded-full bg-accent/10 px-3 py-1 text-sm text-accent-foreground"
                                >
                                    {{ category.name }}
                                </div>
                            </div>
                        </div>

                        <div v-if="isOwner" class="flex flex-wrap gap-4">
                            <Link :href="route('listings.edit', listing?.id)">
                                <button
                                    class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                                >
                                    Edit
                                </button>
                            </Link>
                            <button
                                @click="deleteListing"
                                class="inline-flex h-10 items-center justify-center rounded-lg bg-destructive px-4 py-2 text-sm font-medium text-destructive-foreground hover:bg-destructive/90"
                            >
                                Delete
                            </button>
                            <button
                                @click="toggleListingClosed"
                                class="inline-flex h-10 items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white"
                                :class="listing?.listingClosed ? 'bg-green-500 hover:bg-green-600' : 'bg-destructive hover:bg-destructive/90'"
                            >
                                {{ listing?.listingClosed ? 'Reopen' : 'Close' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications Section -->
            <div v-if="showApplications" class="flex-1 space-y-6">
                <div class="rounded-lg border bg-card p-6 shadow-sm">
                    <h2 class="mb-6 text-2xl font-semibold text-card-foreground">Applications</h2>
                    <div class="space-y-6">
                        <div
                            v-for="application in listingApplications"
                            :key="application.id"
                            class="rounded-lg border bg-card p-4 shadow-sm transition-all duration-200 hover:shadow-md"
                        >
                            <Link :href="route('listing-applications.show', application.id)" class="block">
                                <div class="space-y-4">
                                    <!-- Header -->
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-card-foreground">
                                                {{ application.name }}
                                            </h3>
                                            <StatusLabel v-if="application.status" :status="application.status" />
                                        </div>
                                        <span class="text-sm text-muted-foreground">
                                            Applied {{ new Date(application.created_at).toLocaleString() }}
                                        </span>
                                    </div>

                                    <!-- Contact Details -->
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        <div class="flex min-w-0 items-center gap-2 text-muted-foreground">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                            <span class="truncate">{{ application.email }}</span>
                                        </div>

                                        <div class="flex min-w-0 items-center gap-2 text-muted-foreground">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                                                />
                                            </svg>
                                            <span class="truncate">{{ application.phone }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 text-muted-foreground">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <span>{{ application.address }}</span>
                                        </div>
                                    </div>

                                    <!-- Documents -->
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                        <a
                                            v-if="application.cv"
                                            :href="application.cv"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 rounded-md bg-accent/10 px-3 py-2 text-sm font-medium text-accent-foreground hover:bg-accent/20"
                                        >
                                            <FileText class="h-4 w-4" />
                                            Download CV
                                        </a>

                                        <div v-if="application.cover_letter" class="flex-1">
                                            <p class="text-sm text-muted-foreground">
                                                <span class="font-medium">Cover Letter:</span>
                                                {{ truncate(application.cover_letter, 100) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
