<script setup lang="ts">
import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
const isVisible = ref(true);
const { props } = usePage<SharedData>();
const flash = props.flash;
console.log(flash, 'flash');
const clearMessage = () => {
    isVisible.value = false;
};

setTimeout(() => {
    clearMessage();
}, 2000);
</script>

<template>
    <div v-if="isVisible && flash && (flash.errors?.length || flash.success)" id="status-container">
        <!-- Error State -->
        <div v-if="flash.errors && flash.errors.length > 0" class="mb-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            <li v-for="error in flash.errors" :key="error">{{ error }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success State -->
        <div v-if="flash.success" class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">
            {{ flash.success }}
        </div>
    </div>
</template>
