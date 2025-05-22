<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    listing_id: Number,
    user: Object,
    isOpen: Boolean,
});

const form = useForm({
    cv: props.user?.cv ?? null,
    cover_letter: null,
    listing_id: props.listing_id,
    user_id: props.user?.id ?? null,
    status: 'applied',
});

const handleCvChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        form.cv = file;
    }
};

const emit = defineEmits(['update:is-open', 'update:status']);

const openModal = () => {
    emit('update:is-open', true);
};

const closeModal = () => {
    form.reset();
    emit('update:is-open', false);
};

defineExpose({ openModal, closeModal });

//TODO: Handle AJAX redirects with flash messages/Prop updates
//MAYBE FIXED with preserveState: false (to ensure rerender... plus flash message should be : ->with('flash', [
//     'success' => 'Notification marked as read',
// ])
const submit = () => {
    console.log('submit', form.data());
    form.post(route('listing-applications.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            emit('update:is-open', false);
            emit('update:status', 'applied');
        },
        onError: (e) => {
            console.log('error', e);
        },
    });
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6">
            <h1 class="mb-4 text-xl font-bold text-gray-900">Application Modal</h1>
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid gap-2">
                    <div class="grid gap-2">
                        <Label class="cursor-pointer rounded-md text-gray-900" for="cv">
                            {{ form.cv ? 'Profile CV' : 'Upload CV' }}
                        </Label>
                        <Input
                            v-if="!form.cv"
                            id="cv"
                            type="file"
                            @change="handleCvChange"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                        />
                        <div v-else class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Current CV: {{ form.cv.name || 'Profile CV' }}</span>
                            <Button type="button" variant="outline" size="sm" @click="form.cv = null"> Change </Button>
                        </div>
                        <InputError :message="form.errors.cv" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="cover_letter" class="text-gray-900">Cover Letter:</Label>
                    <textarea
                        id="cover_letter"
                        v-model="form.cover_letter"
                        class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        rows="4"
                        placeholder="Tell us why you're interested in this position"
                    ></textarea>
                    <InputError :message="form.errors.cover_letter" />
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing" class="rounded-md bg-blue-500 text-white hover:bg-blue-600">
                        Submit Application
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
