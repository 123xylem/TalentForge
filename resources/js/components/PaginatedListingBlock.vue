<script setup lang="ts">
import { useTextFormatter } from '@/composables/useTextFormatter';

import { type PaginatedListings } from '@/types';
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';
const { truncate } = useTextFormatter();

const props = defineProps<{
    paginatedListings: PaginatedListings;
}>();
</script>
<template>
    <div v-for="listing in props.paginatedListings.data" :key="listing.id" class="w-full">
        <div class="flex flex-1 flex-col gap-2 rounded-lg border p-4 hover:cursor-pointer hover:bg-gray-900">
            <Link :href="route('listings.show', listing.id)" class="block">
                <h2 class="text-lg font-semibold">{{ listing.title }}</h2>
                <div class="flex flex-row gap-2">
                    <div
                        v-for="skill in listing.skills?.slice(0, 3)"
                        :key="skill.id"
                        class="rounded-full bg-gray-700 px-2 py-1 text-xs text-neutral-500"
                    >
                        {{ skill.name }}
                    </div>
                </div>
                <p class="text-sm text-neutral-500">
                    {{ truncate(listing.description, 100) }}
                </p>
                <p class="text-sm text-neutral-500">{{ listing.salary }}</p>
                <p class="text-sm text-neutral-500">{{ listing.location }}</p>
                <p class="text-sm text-neutral-500">{{ new Date(listing.created_at ?? '').toLocaleDateString() }}</p>
            </Link>
        </div>
    </div>
</template>
