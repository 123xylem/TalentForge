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
    <div class="flex flex-col gap-4 py-4">
        <span v-if="title" class="text-lg font-semibold">{{ title }}</span>
        <form class="flex flex-col gap-4 rounded-lg bg-card p-4 shadow" @submit.prevent="buildFiltersAndApply">
            <!-- Search Inputs -->
            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <input
                        placeholder="Search"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        v-model="formTextSearch"
                        @input="$emit('update:textSearch', formTextSearch)"
                    />
                </div>
                <div class="flex-1">
                    <input
                        placeholder="Location"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        v-model="formLocationSearch"
                        @input="$emit('update:locationSearch', formLocationSearch)"
                    />
                </div>
            </div>

            <!-- Filters Row -->
            <div class="flex flex-wrap gap-4">
                <!-- Category Dropdown -->
                <div class="relative min-w-[200px] flex-1">
                    <button
                        type="button"
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @click="(getCategories(), closeDropsExcept('categories'))"
                    >
                        <span>Category</span>
                        <span class="ml-2">▼</span>
                    </button>
                    <div
                        v-if="openDropdowns.categories && dropdownsOpen"
                        class="absolute z-10 mt-2 w-full rounded-md border bg-popover p-2 shadow-md"
                    >
                        <div class="max-h-[200px] overflow-y-auto">
                            <label
                                v-for="category in categoriesList"
                                :key="category.id"
                                class="flex items-center px-3 py-2 hover:bg-accent hover:text-accent-foreground"
                            >
                                <input
                                    type="radio"
                                    :value="category.id"
                                    class="mr-2"
                                    :checked="formSelectedCategory === category.id"
                                    @change="(updateCategory(category.id), $emit('update:category', category.id))"
                                />
                                <span class="text-sm">{{ category.name }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Skills Dropdown -->
                <div class="relative min-w-[200px] flex-1">
                    <button
                        type="button"
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @click="(getSkills(), closeDropsExcept('skills'))"
                    >
                        <span>Skills</span>
                        <span class="ml-2">▼</span>
                    </button>
                    <div v-if="openDropdowns.skills && dropdownsOpen" class="absolute z-10 mt-2 w-full rounded-md border bg-popover p-2 shadow-md">
                        <div class="max-h-[200px] overflow-y-auto">
                            <label
                                v-for="skill in skillsList"
                                :key="skill.id"
                                class="flex items-center px-3 py-2 hover:bg-accent hover:text-accent-foreground"
                            >
                                <input
                                    type="checkbox"
                                    :value="skill.id"
                                    class="mr-2"
                                    :checked="formSelectedSkills.includes(skill.id)"
                                    @change="updateSkills(skill.id)"
                                />
                                <span class="text-sm">{{ skill.name }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Salary Dropdown -->
                <div class="relative min-w-[200px] flex-1">
                    <button
                        type="button"
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @click="closeDropsExcept('salary')"
                    >
                        <span>Salary</span>
                        <span class="ml-2">▼</span>
                    </button>
                    <div v-if="openDropdowns.salary && dropdownsOpen" class="absolute z-10 mt-2 w-full rounded-md border bg-popover p-2 shadow-md">
                        <div class="max-h-[200px] overflow-y-auto">
                            <label
                                v-for="salary in salaryOptions"
                                :key="salary"
                                class="flex items-center px-3 py-2 hover:bg-accent hover:text-accent-foreground"
                            >
                                <input
                                    type="radio"
                                    :value="salary"
                                    class="mr-2"
                                    :checked="formSelectedSalary === salary"
                                    @change="((formSelectedSalary = salary), $emit('update:salary', salary))"
                                />
                                <span class="text-sm">{{ salary }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Application Status Dropdown -->
                <div v-if="applicationFilter" class="relative min-w-[200px] flex-1">
                    <button
                        type="button"
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @click="closeDropsExcept('applicationStatus')"
                    >
                        <span>Status</span>
                        <span class="ml-2">▼</span>
                    </button>
                    <div
                        v-if="openDropdowns.applicationStatus && dropdownsOpen"
                        class="absolute z-10 mt-2 w-full rounded-md border bg-popover p-2 shadow-md"
                    >
                        <div class="max-h-[200px] overflow-y-auto">
                            <label
                                v-for="status in applicationStatusOptions"
                                :key="status"
                                class="flex items-center px-3 py-2 hover:bg-accent hover:text-accent-foreground"
                            >
                                <input
                                    type="radio"
                                    :value="status"
                                    class="mr-2"
                                    :checked="formSelectedApplicationStatus === status"
                                    @change="((formSelectedApplicationStatus = status), $emit('update:applicationStatus', status))"
                                />
                                <span class="text-sm">{{ status }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4">
                <button type="submit" class="flex-1 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90">
                    Search
                </button>
                <button
                    type="button"
                    @click="resetFilters"
                    class="flex-1 rounded-md bg-secondary px-4 py-2 text-sm font-medium text-secondary-foreground hover:bg-secondary/90"
                >
                    Reset
                </button>
            </div>
        </form>
    </div>
</template>
