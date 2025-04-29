<script setup lang="ts">
import { type Category, type Skill } from '@/types';
import { defineEmits, defineProps, ref } from 'vue';

const props = defineProps<{
    title: string;
    applicationFilter: boolean;
    textSearch: string;
    locationSearch: string;
    selectedCategories: string[];
    selectedSkills: string[];
    selectedSalary: string;
    applicationStatus: string;
}>();
console.log(props, 'props');

const skillsList = ref<Skill[]>([]);
const categoriesList = ref<Category[]>([]);

const emits = defineEmits(['applyFilters']);

const formTextSearch = ref('');
const formLocationSearch = ref('');
const formSelectedApplicationStatus = ref('');
const formSelectedCategories = ref([]);
const formSelectedSkills = ref([]);
const formSelectedSalary = ref('');

const isOpenCategoriesDropdown = ref(false);
const isOpenSkillsDropdown = ref(false);
const isOpenSalaryDropdown = ref(false);
const isOpenApplicationStatusDropdown = ref(false);
const dropdownsOpen = ref(true);
const salaryOptions = ['19,000', '29,000', '39,000', '49,000', '59,000', '69,000', '79,000', '89,000', '99,000', '109,000', '119,000', '149,000'];
const applicationStatusOptions = ['Applied', 'Rejected', 'Shortlisted'];

const toggleSkillsDropdown = () => {
    isOpenSkillsDropdown.value = !isOpenSkillsDropdown.value;
    if (isOpenSkillsDropdown.value && skillsList.value.length === 0) {
        fetch('/skills')
            .then((res) => res.json())
            .then((data) => {
                skillsList.value = data;
                console.log(skillsList.value, 'skillsList');
            });
    }
};

const toggleCategoriesDropdown = () => {
    isOpenCategoriesDropdown.value = !isOpenCategoriesDropdown.value;
    if (isOpenCategoriesDropdown.value && categoriesList.value.length === 0) {
        fetch('/categories')
            .then((res) => res.json())
            .then((data) => {
                categoriesList.value = data;
                console.log(categoriesList.value, 'categoriesList');
            });
    }
};

const closeCategoryDropdown = () => {
    isOpenCategoriesDropdown.value = false;
};

const toggleSalaryDropdown = () => {
    isOpenSalaryDropdown.value = !isOpenSalaryDropdown.value;
};

const closeDropdowns = () => {
    dropdownsOpen.value = false;
};

const buildFiltersAndApply = () => {
    const filters = {};

    if (formSelectedApplicationStatus.value) {
        filters.applicationStatus = formSelectedApplicationStatus.value;
    }
    if (formSelectedCategories.value) {
        filters.category = formSelectedCategories.value;
    }
    if (formSelectedSkills.value) {
        filters.skills = formSelectedSkills.value;
    }
    if (formSelectedSalary.value) {
        filters.salary = formSelectedSalary.value;
    }
    closeDropdowns();
    console.log(filters, 'filters here');
    dropdownsOpen.value = true;

    emits('applyFilters', filters);
};

//TODO add elastic search autocomplete?
</script>
<template>
    <div class="flex flex-wrap items-center gap-2 py-4">
        <span class="mr-4 text-lg font-semibold">{{ title }}</span>
        <div class="flex flex-wrap gap-2">
            <form class="flex w-full flex-wrap gap-2" @submit.prevent="buildFiltersAndApply">
                <input
                    type="text"
                    placeholder="Search"
                    class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                    :value="textSearch"
                    @input="$emit('update:textSearch', $event.target.value)"
                />
                <input
                    placeholder="Location"
                    class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                    :value="locationSearch"
                    @input="$emit('update:locationSearch', $event.target.value)"
                />

                <button type="submit">Search</button>
            </form>
            <!-- CATEGORY -->
            <div class="relative">
                <button
                    class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                    @click="toggleCategoriesDropdown"
                >
                    Category ▼
                </button>
                <div
                    v-if="isOpenCategoriesDropdown && dropdownsOpen"
                    class="absolute z-10 mt-2 flex w-[250px] flex-wrap rounded-md bg-white shadow-lg"
                >
                    <div class="flex w-[250px] flex-wrap gap-1 py-1">
                        <div v-for="category in categoriesList" :key="category.id" class="flex flex-wrap px-4 py-2 hover:bg-gray-100">
                            <label class="flex items-center">
                                <input type="checkbox" :value="category.id" :formSelectedCategories="selectedCategories" class="mr-2" />
                                <span class="text-sm text-gray-800">{{ category.name }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- SKILLS -->
            <div class="relative">
                <button
                    class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                    @click="toggleSkillsDropdown"
                >
                    Skills ▼
                </button>
                <div
                    v-if="isOpenSkillsDropdown && dropdownsOpen"
                    @blur="toggleSkillsDropdown"
                    class="absolute z-10 mt-2 flex w-[250px] flex-wrap rounded-md bg-white shadow-lg"
                >
                    <div class="flex w-[250px] flex-wrap gap-1 py-1">
                        <div v-for="skill in skillsList" :key="skill.id" class="flex flex-wrap px-4 py-2 hover:bg-gray-100">
                            <label class="flex items-center">
                                <input type="checkbox" :value="skill.id" :formSelectedSkills="selectedSkills" class="mr-2" />
                                <span class="text-sm text-gray-800">{{ skill.name }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- SALARY -->
            <div class="relative">
                <button
                    class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                    @click="toggleSalaryDropdown"
                >
                    Salary ▼
                </button>
                <div v-if="isOpenSalaryDropdown && dropdownsOpen" class="absolute z-10 mt-2 flex w-[350px] flex-wrap rounded-md bg-white shadow-lg">
                    <div class="flex flex-wrap gap-1 py-1">
                        <div v-for="salary in salaryOptions" :key="salary" class="flex min-w-max flex-wrap px-4 py-2 hover:bg-gray-100">
                            <label class="flex max-w-[120px] flex-wrap items-center">
                                <input type="radio" :id="salary" :value="salary" :formSelectedSalary="selectedSalary" />
                                <label class="p-2 text-sm text-gray-800" :for="salary"> {{ ' ' }} £{{ salary }} +</label>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- APPLICATION STATUS -->
            <div v-if="applicationFilter" class="relative">
                <button
                    class="rounded-full border border-gray-300 bg-white px-5 py-2 text-base font-medium text-gray-800 hover:border-blue-600 focus:outline-none"
                    @click="isOpenApplicationStatusDropdown = !isOpenApplicationStatusDropdown"
                >
                    Application Status ▼
                </button>
                <div
                    v-if="isOpenApplicationStatusDropdown && dropdownsOpen"
                    class="absolute z-10 mt-2 flex w-[350px] flex-wrap rounded-md bg-white shadow-lg"
                >
                    <div class="flex flex-wrap gap-1 py-1">
                        <div v-for="status in applicationStatusOptions" :key="status" class="flex min-w-max flex-wrap px-4 py-2 hover:bg-gray-100">
                            <label class="flex max-w-[120px] flex-wrap items-center">
                                <input type="radio" :id="status" :value="status" :formSelectedApplicationStatus="selectedApplicationStatus" />
                                <label class="p-2 text-sm text-gray-800" :for="status"> {{ ' ' }} {{ status }}</label>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
