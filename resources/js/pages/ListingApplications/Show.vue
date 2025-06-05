<script setup lang="ts">
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import StatusLabel from '@/components/StatusLabel.vue';
import { useTextFormatter } from '@/composables/useTextFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import { ref } from 'vue';

const { truncate } = useTextFormatter();
const page = usePage<SharedData>();
const { listing, listingApplication } = page.props;
// const userApplied = !user.status.includes('read', 'unread');
const shortlisted = listingApplication?.status === 'shortlisted';
const rejected = listingApplication?.status === 'rejected';
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
        href: `/listing-applications/${listingApplication?.id}`,
    },
];

const rejectForm = useForm({
    _method: 'patch',
    action: 'reject',
});
const progressForm = useForm({
    _method: 'patch',
    action: 'progress',
});

const confirmModal = ref(false);
const modalAction = ref('');
const confirmModalOpen = (action: string) => {
    confirmModal.value = true;
    modalAction.value = action;
};

const confirmModalClose = () => {
    confirmModal.value = false;
    modalAction.value = '';
};

const submitForm = (action: string) => {
    if (action === 'reject') {
        rejectForm.patch(route('listing-applications.update', listingApplication?.id), {
            onSuccess: () => {
                window.location.href = route('listings.show', listingApplication?.listing?.id);
            },
            onError: () => {
                // console.log ('error');
            },
        });
    } else {
        progressForm.patch(route('listing-applications.update', listingApplication?.id), {
            onSuccess: () => {
                window.location.href = route('listings.show', listingApplication?.listing?.id);
            },
            onError: () => {
                // console.log ('error');
            },
        });
    }
};
</script>

<template>
    <Head title="Listing Application" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mt-4 flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <h1 class="text-2xl font-semibold text-card-foreground">Application for: {{ listingApplication?.listing?.title }}</h1>
                        <StatusLabel v-if="listingApplication?.status" :status="listingApplication?.status" />
                    </div>
                </div>

                <!-- Application Details -->
                <div class="rounded-lg border bg-card p-6 shadow-sm">
                    <div class="grid gap-6">
                        <!-- Contact Information -->
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="flex min-w-0 items-center gap-2 text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                </svg>
                                <span class="truncate">{{ listingApplication?.applicant?.name }}</span>
                            </div>

                            <div class="flex min-w-0 items-center gap-2 text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                <span class="truncate">{{ listingApplication?.applicant?.email }}</span>
                            </div>

                            <div class="flex min-w-0 items-center gap-2 text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                                    />
                                </svg>
                                <span class="truncate">{{ listingApplication?.applicant?.phone }}</span>
                            </div>

                            <div class="flex min-w-0 items-center gap-2 text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span class="truncate">{{ listingApplication?.applicant?.location }}</span>
                            </div>
                        </div>

                        <!-- Skills -->
                        <div class="space-y-2">
                            <h2 class="text-lg font-semibold text-card-foreground">Skills</h2>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="skill in listingApplication?.applicant?.skills"
                                    :key="skill.id"
                                    class="rounded-full bg-accent/10 px-3 py-1 text-sm text-accent-foreground"
                                >
                                    {{ skill.name }}
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="space-y-4">
                            <h2 class="text-lg font-semibold text-card-foreground">Documents</h2>
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                <a
                                    v-if="listingApplication?.cv"
                                    :href="listingApplication.cv"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 rounded-lg bg-accent/10 px-4 py-2 text-sm font-medium text-accent-foreground hover:bg-accent/20"
                                >
                                    <FileText class="h-4 w-4" />
                                    Download CV
                                </a>

                                <div v-if="listingApplication?.cover_letter" class="flex-1">
                                    <p class="text-sm text-muted-foreground">
                                        <span class="font-medium">Cover Letter:</span>
                                        {{ truncate(listingApplication.cover_letter, 100) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-4">
                            <form v-if="!shortlisted" @submit.prevent="confirmModalOpen('progress')">
                                <button
                                    type="submit"
                                    class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                                >
                                    Progress Applicant
                                </button>
                            </form>
                            <form v-if="!rejected" @submit.prevent="confirmModalOpen('reject')">
                                <button
                                    type="submit"
                                    class="inline-flex h-10 items-center justify-center rounded-lg bg-destructive px-4 py-2 text-sm font-medium text-destructive-foreground hover:bg-destructive/90"
                                >
                                    Reject Applicant
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                <div v-if="Object.keys(submitForm.errors ?? {}).length > 0" class="rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                    <ul class="list-inside list-disc space-y-1 text-sm text-destructive">
                        <li v-for="(error, field) in submitForm.errors" :key="field">
                            {{ error }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :is-open="confirmModal"
            @confirm="submitForm(modalAction)"
            @cancel="confirmModalClose"
            :title="`Are you sure you want to ${modalAction} this application?`"
            :description="`This action cannot be undone.`"
            :confirmModal="confirmModal"
        />
    </AppLayout>
</template>
