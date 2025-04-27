<script setup lang="ts">
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import StatusLabel from '@/components/StatusLabel.vue';
import { useTextFormatter } from '@/composables/useTextFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type Skill } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import { ref } from 'vue';

const { truncate } = useTextFormatter();
const page = usePage<SharedData>();
const { listing, auth, listingApplication } = page.props;
const user = auth.user;
console.log(listingApplication);
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
                console.log('rejectForm success');
            },
        });
    } else {
        progressForm.patch(route('listing-applications.update', listingApplication?.id), {
            onSuccess: () => {
                window.location.href = route('listings.show', listingApplication?.listing?.id);
            },
        });
    }
};
</script>

<template>
    <Head title="Listing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">Application for: {{ listingApplication?.listing?.title }}</h1>
                <div class="relative flex flex-col gap-2">
                    <div class="status-icon absolute right-2 top-2">
                        <div class="status-icon-inner">
                            <StatusLabel v-if="listingApplication?.status" :status="listingApplication?.status" />
                        </div>
                    </div>
                    <p class="text-sm text-neutral-500">Name: {{ listingApplication?.applicant?.name }}</p>
                    <p class="text-sm text-neutral-500">Email: {{ listingApplication?.applicant?.email }}</p>
                    <p class="text-sm text-neutral-500">Phone: {{ listingApplication?.applicant?.phone }}</p>
                    <p class="text-sm text-neutral-500">Address: {{ listingApplication?.applicant?.location }}</p>
                    <!-- TODO: add skills match computed variable based on skills length for listing and applicant -->
                    <p class="text-sm text-neutral-500">
                        Skills: {{ listingApplication?.applicant?.skills?.map((skill: Skill) => skill.name).join(', ') }}
                    </p>
                    <p class="text-sm text-neutral-500">
                        CV: <a :href="listingApplication?.cv" target="_blank"><FileText class="h-4 w-4" /> Download CV</a>
                    </p>
                    <p class="text-sm text-neutral-500">Cover Letter: {{ truncate(listingApplication?.cover_letter, 100) }}</p>
                </div>
            </div>

            <div class="buttons-row flex gap-2 rounded-lg">
                <form v-if="!shortlisted" @submit.prevent="confirmModalOpen('progress')">
                    <button
                        type="submit"
                        class="rounded-full bg-blue-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                    >
                        Progress Applicant
                    </button>
                </form>
                <form v-if="!rejected" @submit.prevent="confirmModalOpen('reject')">
                    <button type="submit" class="rounded-full bg-red-500 px-2 py-1 text-sm text-white hover:cursor-pointer hover:bg-red-600">
                        Reject Applicant
                    </button>
                </form>
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
