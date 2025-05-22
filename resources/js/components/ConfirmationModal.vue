<script setup lang="ts">
import { Button } from '@/components/ui/button';

const props = defineProps<{
    title: string;
    description: string;
    confirmModal: boolean;
    confirmModalText?: string;
}>();

const emit = defineEmits<{
    (e: 'confirm'): void;
    (e: 'cancel'): void;
}>();
</script>

<template>
    <div v-if="confirmModal">
        <!-- Overlay -->
        <div class="fixed inset-0 z-40 bg-black/50" @click="emit('cancel')"></div>

        <!-- Modal -->
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h2 class="text-lg font-medium text-gray-900">{{ props.title }}</h2>
                <p class="text-sm text-gray-500">{{ props.description }}</p>
                <div class="mt-4 flex justify-end gap-2">
                    <Button class="rounded-md bg-red-500 p-2 text-white" @click="emit('cancel')">Cancel</Button>
                    <Button class="rounded-md bg-green-500 p-2 text-white" @click="emit('confirm')">{{ props.confirmModalText || 'Confirm' }}</Button>
                </div>
            </div>
        </div>
    </div>
</template>
