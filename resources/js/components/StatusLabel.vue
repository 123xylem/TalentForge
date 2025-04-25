<script setup lang="ts">
interface Props {
    status: 'unread' | 'applied' | 'shortlisted' | 'rejected';
}

const props = defineProps<Props>();
const statusClasses = {
    unread: 'bg-gray-700',
    applied: 'bg-gray-700',
    shortlisted: 'bg-green-500',
    rejected: 'bg-red-500',
};
</script>

<template>
    <span class="rounded-full px-2 py-1 text-xs text-neutral-500 text-white" :class="statusClasses[status]">
        {{ status.charAt(0).toUpperCase() + status.slice(1) }}
    </span>
</template>
