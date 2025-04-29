<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
const props = defineProps<{
    title: string;
    applicationFilter: boolean;
}>();
console.log(props, 'props');

const textSearch = ref('');
const category = ref('');
const skills = ref('');
const salary = ref('');
const locationSearch = ref('');
const applicationStatus = ref('');
const isOpenCategoryDropdown = ref(false);
const isOpenSkillsDropdown = ref(false);
const isOpenSalaryDropdown = ref(false);
const isOpenApplicationStatusDropdown = ref(false);

const applyFilters = () => {
    const filters = {
        textSearch: textSearch.value,
        locationSearch: locationSearch.value,
        applicationStatus: applicationStatus.value,
        category: category.value,
        skills: skills.value,
        salary: salary.value,
    };
    console.log(filters);
    router.get(route('listings.index'), { filters: filters });
};
</script>
<template>
    <div class="flex flex-wrap items-center gap-2 py-4">
        <span class="mr-4 text-lg font-semibold">{{ title }}</span>
        <div class="flex flex-wrap gap-2">
            <form class="flex w-full flex-wrap gap-2" @submit.prevent="applyFilters">
                <input
                    type="text"
                    placeholder="Search"
                    class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                    v-model="textSearch"
                />
                <input
                    placeholder="Location"
                    class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                    v-model="locationSearch"
                />

                <button type="submit">Search</button>
            </form>
            <button
                class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                @click="isOpenCategoryDropdown = !isOpenCategoryDropdown"
                @blur="[closeCategoryDropdown, applyFilters]"
            >
                Category ▼
            </button>
            <button
                class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                @click="isOpenSkillsDropdown = !isOpenSkillsDropdown"
                @blur="[closeSkillsDropdown, applyFilters]"
            >
                Skills ▼
            </button>
            <button
                class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                @click="isOpenSalaryDropdown = !isOpenSalaryDropdown"
                @blur="[closeSalaryDropdown, applyFilters]"
            >
                Salary ▼
            </button>
            <button
                v-if="applicationFilter"
                class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                @click="isOpenApplicationStatusDropdown = !isOpenApplicationStatusDropdown"
                @blur="[closeApplicationStatusDropdown, applyFilters]"
            >
                Application Status ▼
            </button>
            <button
                class="rounded-full border-none bg-transparent px-5 py-2 text-base font-medium text-gray-600 hover:bg-gray-100 focus:outline-none"
                @click="resetFilters"
            >
                Reset
            </button>
        </div>
    </div>
</template>
