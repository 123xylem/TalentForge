<script setup lang="ts">
import { type PaginatedListing } from '@/types';
import { Link } from '@inertiajs/vue3';

interface Props {
    links: PaginatedListing['links'];
}

const props = defineProps<Props>();

const getLabel = (label: string) => {
    if (label.includes('&laquo;')) return '← Previous';
    if (label.includes('&raquo;')) return 'Next →';
    return label;
};
</script>

<template>
    <nav class="relative col-span-full flex w-full justify-center">
        <template v-for="link in links" :key="link.label">
            <Link
                preserve-scroll
                :href="link.url ?? ''"
                class="flex items-center justify-center rounded-lg px-3 py-2 text-sm"
                :class="{
                    'bg-gray-200 text-gray-900': link.active,
                    'text-gray-600 hover:bg-gray-100': !link.active && link.url,
                    'hidden text-gray-300': !link.url,
                }"
            >
                {{ getLabel(link.label) }}
            </Link>
        </template>
    </nav>
</template>
