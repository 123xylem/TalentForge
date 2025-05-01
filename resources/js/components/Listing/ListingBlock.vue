<script setup lang="ts">
import ListingCard from '@/components/Listing/ListingCard.vue';
import { type PaginatedListingData } from '@/types';
import { defineProps } from 'vue';

const props = defineProps<{
    listings: PaginatedListingData;
    rows: number;
}>();
</script>
<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-{{ props.rows }}">
        <div v-for="listing in props.listings.data" :key="listing.id" class="w-full">
            <ListingCard :listing="listing" :applicants="listing.applicants_count || 0" />
        </div>
    </div>
</template>
