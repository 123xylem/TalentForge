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
    <div class="relative flex h-full flex-1 flex-col gap-2 rounded-lg border bg-card p-4 text-card-foreground transition-colors hover:bg-accent/5">
        <Link :href="route('listings.show', listingData.id)" class="block">
            <div class="flex flex-col gap-3">
                <!-- Header with title and status -->
                <div class="flex items-start justify-between gap-4">
                    <h2 class="line-clamp-2 text-lg font-semibold text-foreground">
                        {{ listingData.title }}
                    </h2>
                    <div class="flex shrink-0 items-center gap-2">
                        <div v-if="applicants > 0" class="flex items-center gap-1">
                            <UserRoundIcon class="h-4 w-4 text-muted-foreground" />
                            <span class="text-xs text-muted-foreground">{{ applicants }}</span>
                        </div>
                        <p v-if="listing.status" class="text-xs">
                            <StatusLabel :status="listing.status" />
                        </p>
                    </div>
                </div>

                <!-- Skills -->
                <div v-if="listingData.skills" class="flex flex-wrap gap-2">
                    <div
                        v-for="skill in listingData.skills?.slice(0, 3)"
                        :key="skill.id"
                        class="rounded-full bg-accent/10 px-2 py-1 text-xs text-accent-foreground"
                    >
                        {{ skill.name }}
                    </div>
                </div>

                <!-- Description -->
                <p class="line-clamp-2 text-sm text-muted-foreground">
                    {{ truncate(listingData.description, 100) }}
                </p>

                <!-- Details -->
                <div class="flex flex-col gap-1 text-sm text-muted-foreground">
                    <p>{{ listingData.salary }}</p>
                    <p>{{ listingData.location }}</p>
                </div>

                <!-- Footer -->
                <p class="mt-2 text-xs text-muted-foreground">Created: {{ new Date(listing.created_at ?? '').toLocaleDateString() }}</p>
            </div>
        </Link>
    </div>
</template>
