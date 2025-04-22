<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type Skill } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { capitalize } from 'vue';
const page = usePage<SharedData>();
const { listing, auth, userApplicationStatus = null, flash, listingApplication, applicants } = page.props;
const user = auth.user;
console.log(listingApplication, applicants);
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
                            <div class="status-icon-inner-inner p-2" :class="shortlisted ? 'bg-green-500' : rejected ? 'bg-red-500' : 'bg-gray-500'">
                                {{ capitalize(listingApplication?.status) }}
                            </div>
                        </div>
                    </div>
                    <!-- //TODO: show cv download link -->
                    <p class="text-sm text-neutral-500">Name: {{ listingApplication?.applicant?.name }}</p>
                    <p class="text-sm text-neutral-500">Email: {{ listingApplication?.applicant?.email }}</p>
                    <p class="text-sm text-neutral-500">Phone: {{ listingApplication?.applicant?.phone }}</p>
                    <p class="text-sm text-neutral-500">Address: {{ listingApplication?.applicant?.location }}</p>
                    <!-- TODO: add skills match computed variable based on skills length for listing and applicant -->
                    <p class="text-sm text-neutral-500">
                        Skills: {{ listingApplication?.applicant?.skills?.map((skill: Skill) => skill.name).join(', ') }}
                    </p>
                    <p class="text-sm text-neutral-500">CV: {{ listingApplication?.cv }}</p>
                    <p class="text-sm text-neutral-500">Cover Letter: {{ listingApplication?.cover_letter }}</p>
                </div>
            </div>

            <!-- //TODO: add confirmation modal -->
            <div class="buttons-row flex gap-2 rounded-lg">
                <form v-if="!shortlisted" @submit.prevent="progressForm.patch(route('listing-applications.update', listingApplication?.id))">
                    <button
                        type="submit"
                        class="rounded-full bg-blue-500 px-2 py-1 text-sm text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                    >
                        Progress Applicant
                    </button>
                </form>
                <form v-if="!rejected" @submit.prevent="rejectForm.patch(route('listing-applications.update', listingApplication?.id))">
                    <button type="submit" class="rounded-full bg-red-500 px-2 py-1 text-sm text-white hover:cursor-pointer hover:bg-red-600">
                        Reject Applicant
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
