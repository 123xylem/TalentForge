<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const showEmployerFields = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    type: 'job_hunter',
    company_name: '',
    location: '',
    website: '',
    password: '',
    password_confirmation: '',
});

watch(
    () => form.type,
    (value) => {
        showEmployerFields.value = value === 'employer';
    },
);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <input type="hidden" name="_token" :value="csrf" />

            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Phone number</Label>
                    <Input id="phone" type="tel" :tabindex="3" autocomplete="tel" v-model="form.phone" placeholder="+1 (555) 123-4567" />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="type">Account type</Label>
                    <select
                        id="type"
                        v-model="form.type"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :tabindex="4"
                    >
                        <option value="job_hunter">Job Seeker</option>
                        <option value="employer">Employer</option>
                    </select>
                    <InputError :message="form.errors.type" />
                </div>

                <!-- Employer-specific fields -->
                <div v-if="form.type === 'employer'" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="company_name">Company Name</Label>
                        <Input id="company_name" type="text" :tabindex="5" v-model="form.company_name" placeholder="Company name" />
                        <InputError :message="form.errors.company_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="location">Location</Label>
                        <Input id="location" type="text" :tabindex="6" v-model="form.location" placeholder="City, Country" />
                        <InputError :message="form.errors.location" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="website">Website</Label>
                        <Input id="website" type="url" :tabindex="7" v-model="form.website" placeholder="https://example.com" />
                        <InputError :message="form.errors.website" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="8"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="9"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" :tabindex="10" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="11">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
