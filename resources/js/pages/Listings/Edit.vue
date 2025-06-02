<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useCsrf } from '@/composables/useCsrf';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const page = usePage<SharedData>();
const { listing, availableSkills, availableCategories } = page.props;
const { csrf } = useCsrf();

// Initialize form directly with the data
const form = useForm({
    title: listing?.title ?? '',
    description: listing?.description ?? '',
    location: listing?.location ?? '',
    salary: listing?.salary ?? '',
    company: listing?.company ?? '',
    skills: listing?.skills?.map((skill: { id: number }) => skill.id) ?? [],
    categories: listing?.categories?.map((category: { id: number }) => category.id) ?? [],
    slug: listing?.slug ?? '',
});

const submit = () => {
    form.put(route('listings.update', listing?.id));
};
</script>

<template>
    <Head title="Edit Listing" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div class="flex flex-col gap-4">
                    <h1 class="text-2xl font-semibold">Edit Listing</h1>
                    <form @submit.prevent="submit" class="flex flex-col gap-8">
                        <input type="hidden" name="_token" :value="csrf" />

                        <div class="grid gap-8 md:grid-cols-2">
                            <div class="grid gap-3">
                                <Label for="title" class="text-base">Title</Label>
                                <Input
                                    id="title"
                                    type="text"
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="title"
                                    v-model="form.title"
                                    placeholder="Job title"
                                    class="h-11"
                                />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div class="grid gap-3">
                                <Label for="company" class="text-base">Company</Label>
                                <Input
                                    id="company"
                                    type="company"
                                    :tabindex="4"
                                    autocomplete="company"
                                    v-model="form.company"
                                    placeholder="Company"
                                    class="h-11"
                                />
                                <InputError :message="form.errors.company" />
                            </div>

                            <div class="grid gap-3">
                                <Label for="location" class="text-base">Location</Label>
                                <Input
                                    id="location"
                                    type="location"
                                    :tabindex="3"
                                    autocomplete="location"
                                    v-model="form.location"
                                    placeholder="Location"
                                    class="h-11"
                                />
                                <InputError :message="form.errors.location" />
                            </div>

                            <div class="grid gap-3">
                                <Label for="salary" class="text-base">Salary</Label>
                                <Input
                                    id="salary"
                                    type="text"
                                    required
                                    :tabindex="5"
                                    v-model="form.salary"
                                    placeholder="$100,000 - $120,000"
                                    class="h-11"
                                />
                                <InputError :message="form.errors.salary" />
                            </div>

                            <div class="grid gap-3 md:col-span-2">
                                <Label for="description" class="text-base">Description</Label>
                                <Input
                                    id="description"
                                    type="description"
                                    required
                                    :tabindex="2"
                                    v-model="form.description"
                                    placeholder="Job description"
                                    class="h-32"
                                />
                                <InputError :message="form.errors.description" />
                            </div>

                            <div class="grid gap-3">
                                <Label for="skills" class="text-base">Skills</Label>
                                <select
                                    multiple
                                    class="h-48 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    id="skills"
                                    :tabindex="7"
                                    v-model="form.skills"
                                    placeholder="Skills"
                                >
                                    <option v-for="skill in availableSkills" :key="skill.id" :value="skill.id">{{ skill.name }}</option>
                                </select>
                                <InputError :message="form.errors.skills" />
                            </div>

                            <div class="grid gap-3">
                                <Label for="categories" class="text-base">Category</Label>
                                <select
                                    id="categories"
                                    multiple
                                    class="h-48 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    :tabindex="7"
                                    v-model="form.categories"
                                    placeholder="Category"
                                >
                                    <option v-for="category in availableCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.categories" />
                            </div>

                            <div class="md:col-span-2">
                                <Button type="submit" class="mt-4 w-full rounded-lg px-4 py-2" :tabindex="10" :disabled="form.processing">
                                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                    Edit Listing
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
