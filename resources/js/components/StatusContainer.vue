<script setup lang="ts">
interface Props {
    errors?: string[];
    success?: string;
}
//TODO: Make this work for both errors and success (need page refresh?)

defineProps<Props>();
</script>

<template>
    <div>
        <!-- Error State -->
        <div v-if="errors && errors.length > 0" class="mb-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            <li v-for="error in errors" :key="error">{{ error }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success State -->
        <div v-if="success" class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">
            {{ success }}
        </div>
    </div>
</template>
