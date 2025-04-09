<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { SharedData } from '@/types/Skill';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const { props } = usePage<SharedData>();
const { availableSkills, availableCategories, flash } = props;
const form = useForm({
    title: '',
    description: '',
    location: '',
    salary: '',
    company: '',
    skills: [],
    categories: [],
    slug: '',
});

const submit = () => {
    //TODO make slug unique or remove slug constraint
    form.slug = form.title.toLowerCase().replace(/ /g, '-') + '-' + form.company.toLowerCase().replace(/ /g, '-');
    form.post(route('listings.store'));
};

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
</script>

<template>
    <AuthBase title="Create Job listing" description="Enter details below to create a listing">
        <Head title="Create Job Listing" />
        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <input type="hidden" name="_token" :value="csrf" />

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <Input
                        id="title"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="title"
                        v-model="form.title"
                        placeholder="Job title"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Input id="description" type="description" required :tabindex="2" v-model="form.description" placeholder="Job description" />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="location">Location</Label>
                    <Input id="location" type="location" :tabindex="3" autocomplete="location" v-model="form.location" placeholder="Location" />
                    <InputError :message="form.errors.location" />
                </div>

                <div class="grid gap-2">
                    <Label for="company">Company</Label>
                    <Input id="company" type="company" :tabindex="4" autocomplete="company" v-model="form.company" placeholder="Company" />
                    <InputError :message="form.errors.company" />
                </div>

                <div class="grid gap-2">
                    <Label for="salary">Salary</Label>
                    <Input id="salary" type="text" required :tabindex="5" v-model="form.salary" placeholder="$100,000 - $120,000" />
                    <InputError :message="form.errors.salary" />
                </div>

                <div class="grid gap-2 md:col-span-2 lg:col-span-1">
                    <Label for="skills">Skills</Label>
                    <select
                        multiple
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        id="skills"
                        :tabindex="7"
                        v-model="form.skills"
                        placeholder="Skills"
                    >
                        <option v-for="skill in availableSkills" :key="skill.id" :value="skill.id">{{ skill.name }}</option>
                    </select>
                    <InputError :message="form.errors.skills" />
                </div>

                <div class="grid gap-2 md:col-span-2 lg:col-span-1">
                    <Label for="categories">Category</Label>
                    <select
                        id="categories"
                        multiple
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :tabindex="7"
                        v-model="form.categories"
                        placeholder="Category"
                    >
                        <option v-for="category in availableCategories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                    <InputError :message="form.errors.categories" />
                </div>

                <div class="md:col-span-2 lg:col-span-3">
                    <Button type="submit" class="mt-2 w-full" :tabindex="10" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Create Listing
                    </Button>
                </div>
            </div>
        </form>
    </AuthBase>
</template>
