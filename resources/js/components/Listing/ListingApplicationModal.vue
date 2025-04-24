<template>
    <div v-if="isOpen">
        <h1>Application Modal</h1>
        <form @submit.prevent="submit" class="space-y-6 rounded-md border-black p-2">
            <div class="grid gap-2">
                <div class="grid gap-2">
                    <Label class="cursor-pointer rounded-md" for="cv">
                        {{ form.cv ? 'Profile CV' : 'Upload CV' }}
                    </Label>
                    <Input id="cv" type="file" :class="{ hidden: form.cv }" @change="handleCvChange" />
                    <InputError :message="form.errors.cv" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="cover_letter">Cover Letter</Label>
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
                <Button type="submit" :disabled="form.processing"> Submit Application </Button>
            </div>
        </form>
    </div>
</template>

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

const emit = defineEmits(['update:is-open']);

const openModal = () => {
    emit('update:is-open', true);
};

const closeModal = () => {
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
        preserveState: false,
        onSuccess: () => {
            closeModal();
            emit('update:is-open', false);
            window.location.href = route('listings.show', props.listing_id);
        },
    });
};
</script>
