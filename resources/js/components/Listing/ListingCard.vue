<script setup lang="ts">
import StatusLabel from '@/components/StatusLabel.vue';
import { useTextFormatter } from '@/composables/useTextFormatter';
import { type Listing, type ListingApplication } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { UserRoundIcon } from 'lucide-vue-next';
import { computed, defineProps } from 'vue';
const { truncate } = useTextFormatter();

const props = defineProps<{
    listing: ListingApplication | Listing;
    applicants: number;
}>();

const page = usePage();
const listingData = computed(() => (props.listing.listing?.id ? props.listing.listing : props.listing));
// const listingRoute = computed(() =>
//     page.props.userType === 'employer' ? route('listing-applications.show', props.listing.id) : route('listings.show', listingData.id),
// );
</script>

<template>
    <div class="relative flex h-full flex-1 flex-col gap-2 rounded-lg border p-4 hover:cursor-pointer hover:bg-gray-900">
        <Link :href="route('listings.show', listingData.id)" class="block">
            <div v-if="applicants > 0" class="absolute right-4 top-4 flex flex-row gap-2">
                <UserRoundIcon class="h-4 w-4 text-neutral-500" />
                <span class="text-xs text-neutral-500"> {{ applicants }} </span>
            </div>
            <h2 class="text-lg font-semibold">
                {{ listingData.title }}
            </h2>
            <p v-if="listing.status" class="absolute right-4 top-4 text-xs text-neutral-500">
                <StatusLabel :status="listing.status" />
            </p>

            <div v-if="listingData.skills" class="flex flex-row gap-2">
                <div
                    v-for="skill in listingData.skills?.slice(0, 3)"
                    :key="skill.id"
                    class="rounded-full bg-gray-700 px-2 py-1 text-xs text-neutral-500"
                >
                    {{ skill.name }}
                </div>
            </div>
            <p class="text-sm text-neutral-500">
                {{ truncate(listingData.description, 100) }}
            </p>
            <p class="text-sm text-neutral-500">{{ listingData.salary }}</p>
            <p class="text-sm text-neutral-500">{{ listingData.location }}</p>
            <p class="mt-2 text-xs text-neutral-500">Created: {{ new Date(listing.created_at ?? '').toLocaleDateString() }}</p>
        </Link>
    </div>
</template>
