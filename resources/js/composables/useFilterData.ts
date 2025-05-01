import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

export interface FilterState {
    textSearch: string;
    locationSearch: string;
    category: string;
    skills: string[];
    salary: string;
    applicationStatus: string;
}

export function useFilterData(url: string, dataType: string) {
    const filters = ref<FilterState>({
        textSearch: '',
        locationSearch: '',
        category: '',
        skills: [],
        salary: '',
        applicationStatus: '',
    });

    const computedFilters = computed(() => {
        const filteredFilters: Partial<FilterState> = {};
        (Object.keys(filters.value) as Array<keyof FilterState>).forEach((key) => {
            if (key === 'skills') {
                if (filters.value.skills.length > 0) {
                    filteredFilters[key] = filters.value[key];
                }
            } else if (filters.value[key] !== '') {
                filteredFilters[key] = filters.value[key];
            }
        });
        return filteredFilters;
    });

    const resetFilters = () => {
        filters.value = {
            textSearch: '',
            locationSearch: '',
            category: '',
            skills: [],
            salary: '',
            applicationStatus: '',
        };
        router.get(route(url), { page: 1 }, { only: [dataType], preserveState: true, preserveScroll: true });
    };

    const applyFilters = () => {
        router.get(route(url), { ...computedFilters.value, page: 1 }, { only: [dataType], preserveState: true, preserveScroll: true });
    };

    const onPageChange = (pageNum: number) => {
        router.get(route(url), { ...computedFilters.value, page: pageNum }, { only: [dataType], preserveState: true, preserveScroll: true });
    };

    const updateFilter = (key: keyof FilterState, value: string) => {
        console.log(key, value);
        if (key === 'skills') {
            const skills = filters.value.skills;
            if (skills.includes(value)) {
                filters.value.skills = skills.filter((skill) => skill !== value);
            } else {
                filters.value.skills.push(value);
            }
        } else {
            (filters.value[key] as string) = value;
        }
    };

    const initializeFiltersFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        filters.value.textSearch = params.get('textSearch') || '';
        filters.value.locationSearch = params.get('locationSearch') || '';
        filters.value.category = params.get('category') || '';
        filters.value.skills = params.getAll('skills[]') || [];
        filters.value.salary = params.get('salary') || '';
        filters.value.applicationStatus = params.get('applicationStatus') || '';
    };

    return {
        filters,
        computedFilters,
        resetFilters,
        applyFilters,
        onPageChange,
        updateFilter,
        initializeFiltersFromUrl,
    };
}
