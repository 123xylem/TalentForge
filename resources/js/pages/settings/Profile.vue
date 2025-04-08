<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem, type SharedData, type Skill } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

// Vue components
import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import StatusContainer from '@/components/StatusContainer.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const {
    auth: { user },
    flash,
    availableSkills,
    userSkills,
    mustVerifyEmail,
    status,
} = page.props;

const form = useForm({
    name: user.name,
    email: user.email,
    type: user.type || 'job_hunter',
    company: user.company || '',
    bio: user.bio || '',
    website: user.website || '',
    location: user.location || '',
    phone: user.phone || '',
    skills: (userSkills as Skill[]).map((skill: Skill) => skill.id),
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />
        <StatusContainer :errors="form.errors" :success="flash.success" />
        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your profile information" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone">Phone number</Label>
                        <Input
                            id="phone"
                            type="tel"
                            class="mt-1 block w-full"
                            v-model="form.phone"
                            autocomplete="tel"
                            placeholder="+1 (555) 123-4567"
                        />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div v-if="form.type === 'employer'" class="grid gap-2">
                        <Label for="company">Company name</Label>
                        <Input id="company" class="mt-1 block w-full" v-model="form.company" placeholder="Company name" />
                        <InputError class="mt-2" :message="form.errors.company" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="bio">Bio</Label>
                        <textarea
                            id="bio"
                            v-model="form.bio"
                            class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            rows="4"
                            placeholder="Tell us about yourself"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.bio" />
                    </div>

                    <div v-if="form.type === 'job_hunter'" class="grid gap-2">
                        <Label for="skills">Skills</Label>
                        <select
                            id="skills"
                            v-model="form.skills"
                            multiple
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        >
                            <option v-for="skill in availableSkills" :key="skill.id" :value="skill.id">
                                {{ skill.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.skills" />
                        <div v-for="skill in userSkills" :key="skill.id">
                            <p class="text-sm text-neutral-500">{{ skill.name }}</p>
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="website">Website</Label>
                        <Input id="website" type="url" class="mt-1 block w-full" v-model="form.website" placeholder="https://example.com" />
                        <InputError class="mt-2" :message="form.errors.website" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="location">Location</Label>
                        <Input id="location" class="mt-1 block w-full" v-model="form.location" placeholder="City, Country" />
                        <InputError class="mt-2" :message="form.errors.location" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:!decoration-current dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
