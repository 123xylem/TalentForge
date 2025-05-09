<script setup lang="ts">
import { type Category, type Skill } from '@/types';
import { defineEmits, defineProps, onMounted, ref } from 'vue';

// Summary FilterBar is used by parent to provide a query builder for filtering items
// Parent provides it with filter Props
// FilterBar can edit props via  EMIT events
// We can pass these filters from parent to pagination component so that it to can use the url params to filter the items

defineProps<{
    title?: string;
    applicationFilter?: boolean;
    filters?: {
        textSearch: string;
        locationSearch: string;
        selectedCategories: string[];
        selectedSkills: string[];
        selectedSalary: string;
        applicationStatus: string;
    };
}>();

//TODO: Finalise styles
// TODO: Salary type filter needs work
const skillsList = ref<Skill[]>([]);
const categoriesList = ref<Category[]>([]);

const emits = defineEmits(['applyFilters']);
const formSelectedApplicationStatus = ref('');
const formSelectedCategory = ref('');
const formSelectedSkills = ref<string[]>([]);
const formSelectedSalary = ref('');
const formTextSearch = ref('');
const formLocationSearch = ref('');

const openDropdowns = ref({
    categories: false,
    skills: false,
    salary: false,
    applicationStatus: false,
});

const dropdownsOpen = ref(true);
const salaryOptions = [
    'Any',
    '19,000',
    '29,000',
    '39,000',
    '49,000',
    '59,000',
    '69,000',
    '79,000',
    '89,000',
    '99,000',
    '109,000',
    '119,000',
    '149,000',
];
const applicationStatusOptions = ['Any', 'Applied', 'Rejected', 'Shortlisted'];

const getSkills = () => {
    if (skillsList.value.length === 0) {
        fetch('/skills')
            .then((res) => res.json())
            .then((data) => {
                skillsList.value = data;
            });
    }
};

const getCategories = () => {
    if (categoriesList.value.length === 0) {
        fetch('/categories')
            .then((res) => res.json())
            .then((data) => {
                categoriesList.value = [{ id: '', name: 'All' }, ...data];
            });
    }
};

const closeDropsExcept = (dropdown = null) => {
    Object.keys(openDropdowns.value).forEach((key) => {
        if (key !== dropdown) {
            openDropdowns.value[key] = false;
        }
    });
    if (dropdown) {
        //Toggle if open
        openDropdowns.value[dropdown] = !openDropdowns.value[dropdown];
    }
};

const buildFiltersAndApply = () => {
    const filters = {};

    if (formSelectedApplicationStatus.value) {
        filters.applicationStatus = formSelectedApplicationStatus.value;
    }
    if (formSelectedCategory.value) {
        filters.category = formSelectedCategory.value;
        if (formSelectedCategory.value === '') {
            filters.category = '';
        }
    }
    if (formSelectedSkills.value.length > 0) {
        filters.skills = formSelectedSkills.value;
    }
    if (formSelectedSalary.value) {
        filters.salary = formSelectedSalary.value;
    }
    closeDropsExcept();
    dropdownsOpen.value = true;

    emits('applyFilters', filters);
};

const updateSkills = (skillId: string) => {
    if (formSelectedSkills.value.includes(skillId)) {
        formSelectedSkills.value = formSelectedSkills.value.filter((skill) => skill !== skillId);
    } else {
        formSelectedSkills.value.push(skillId);
    }
};

const updateCategory = (categoryId: string) => {
    formSelectedCategory.value = categoryId;
};

const updateSalary = (salary: string) => {
    if (salary === 'Any') {
        formSelectedSalary.value = '';
    } else {
        formSelectedSalary.value = salary;
    }
};

const updateApplicationStatus = (applicationStatus: string) => {
    formSelectedApplicationStatus.value = applicationStatus;
};

const resetFilters = () => {
    formSelectedApplicationStatus.value = '';
    formSelectedCategory.value = '';
    formSelectedSkills.value = [];
    formSelectedSalary.value = '';
    formLocationSearch.value = '';
    formTextSearch.value = '';
    emits('resetFilters');
};

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    formTextSearch.value = params.get('textSearch') || '';
    formLocationSearch.value = params.get('locationSearch') || '';
    formSelectedCategory.value = params.get('category') || '';
    formSelectedSkills.value = params.getAll('skills[]') || [];
    formSelectedSalary.value = params.get('salary') || '';
    formSelectedApplicationStatus.value = params.get('applicationStatus') || '';
});

//TODO add elastic search autocomplete?
</script>
<template>
    <div class="flex flex-wrap items-center gap-2 py-4">
        <span class="mr-4 text-lg font-semibold">{{ title }}</span>
        <div class="flex gap-2">
            <form class="flex flex-wrap items-center gap-2 rounded-lg bg-white px-3 py-2 shadow" @submit.prevent="buildFiltersAndApply">
                <input
                    placeholder="Search"
                    class="rounded-full border border-gray-300 bg-white px-3 py-1 text-sm text-gray-800 focus:border-blue-500 focus:outline-none"
                    v-model="formTextSearch"
                    @input="$emit('update:textSearch', formTextSearch)"
                    style="min-width: 120px"
                />
                <input
                    placeholder="Location"
                    class="rounded-full border border-gray-300 bg-white px-3 py-1 text-sm text-gray-800 focus:border-blue-500 focus:outline-none"
                    v-model="formLocationSearch"
                    @input="$emit('update:locationSearch', formLocationSearch)"
                    style="min-width: 120px"
                />
                <!-- Category Dropdown -->
                <div class="relative">
                    <button
                        type="button"
                        class="rounded-full border border-gray-300 bg-white px-3 py-1 text-sm text-gray-800 hover:border-blue-600 focus:outline-none"
                        @click="(getCategories(), closeDropsExcept('categories'))"
                    >
                        Category ▼
                    </button>
                    <div
                        v-if="openDropdowns.categories && dropdownsOpen"
                        class="absolute z-10 mt-2 flex max-w-[300px] flex-row flex-wrap rounded-md bg-white p-2 shadow-lg"
                    >
                        <label
                            v-for="category in categoriesList"
                            :key="category.id"
                            class="flex min-w-[120px] items-center px-3 py-1 hover:bg-gray-100"
                        >
                            <input
                                type="radio"
                                :value="category.id"
                                class="mr-2"
                                :checked="formSelectedCategory === category.id"
                                @change="(updateCategory(category.id), $emit('update:category', category.id))"
                            />
                            <span class="text-sm text-gray-800">{{ category.name }}</span>
                        </label>
                    </div>
                </div>
                <!-- Skills Dropdown -->
                <div class="relative">
                    <button
                        type="button"
                        class="rounded-full border border-gray-300 bg-white px-3 py-1 text-sm text-gray-800 hover:border-blue-600 focus:outline-none"
                        @click.stop="(getSkills(), closeDropsExcept('skills'))"
                    >
                        Skills ▼
                    </button>
                    <div
                        v-if="openDropdowns.skills && dropdownsOpen"
                        class="absolute left-[-150px] z-10 mt-2 grid w-[450px] grid-cols-3 gap-2 rounded-md bg-white p-2 shadow-lg"
                    >
                        <label v-for="skill in skillsList" :key="skill.id" class="flex items-center px-3 py-1 hover:bg-gray-100">
                            <input
                                type="checkbox"
                                :value="skill.id"
                                @change="(updateSkills(skill.id), $emit('update:skills', skill.id))"
                                class="mr-2"
                                :checked="formSelectedSkills.includes(skill.id)"
                            />
                            <span class="text-sm text-gray-800">{{ skill.name }}</span>
                        </label>
                    </div>
                </div>
                <!-- Salary Dropdown -->
                <div class="relative">
                    <button
                        type="button"
                        class="rounded-full border border-gray-300 bg-white px-3 py-1 text-sm text-gray-800 hover:border-blue-600 focus:outline-none"
                        @click="closeDropsExcept('salary')"
                    >
                        Salary ▼
                    </button>
                    <div
                        v-if="openDropdowns.salary && dropdownsOpen"
                        class="absolute z-10 mt-2 flex max-w-[350px] flex-row flex-wrap rounded-md bg-white p-2 shadow-lg"
                    >
                        <div class="flex flex-row flex-wrap gap-x-2 gap-y-1 py-1">
                            <label v-for="salary in salaryOptions" :key="salary" class="flex min-w-[120px] items-center px-3 py-1 hover:bg-gray-100">
                                <input
                                    type="radio"
                                    :id="salary"
                                    @change="(updateSalary(salary), $emit('update:salary', salary === 'Any' ? '' : salary))"
                                    :value="salary"
                                    :checked="salary === formSelectedSalary"
                                />
                                <span class="ml-2 text-sm text-gray-800">
                                    {{ salary == 'Any' ? 'Any' : '£' + salary + ' +' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Application Status Dropdown -->
                <div v-if="applicationFilter" class="relative">
                    <button
                        type="button"
                        class="rounded-full border border-gray-300 bg-white px-3 py-1 text-sm text-gray-800 hover:border-blue-600 focus:outline-none"
                        @click="closeDropsExcept('applicationStatus')"
                    >
                        Status ▼
                    </button>
                    <div
                        v-if="openDropdowns.applicationStatus && dropdownsOpen"
                        class="absolute z-10 mt-2 flex max-w-[350px] flex-row flex-wrap rounded-md bg-white p-2 shadow-lg"
                    >
                        <div class="flex flex-row flex-wrap gap-x-2 gap-y-1 py-1">
                            <label
                                v-for="status in applicationStatusOptions"
                                :key="status"
                                class="flex min-w-[120px] items-center px-3 py-1 hover:bg-gray-100"
                            >
                                <input
                                    type="radio"
                                    :id="status"
                                    :value="status"
                                    :checked="status === formSelectedApplicationStatus"
                                    @change="
                                        (updateApplicationStatus(status),
                                        $emit('update:applicationStatus', status === 'Any' ? '' : status.toLowerCase()))
                                    "
                                />
                                <span class="ml-2 text-sm text-gray-800">{{ status }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="rounded-full bg-blue-600 px-4 py-1 text-sm font-medium text-white hover:bg-blue-700">Search</button>
                <button
                    type="button"
                    @click="resetFilters"
                    class="rounded-full bg-gray-200 px-4 py-1 text-sm font-medium text-gray-700 hover:bg-gray-300"
                >
                    Reset
                </button>
            </form>
        </div>
    </div>
</template>
