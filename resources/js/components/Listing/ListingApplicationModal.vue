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
    // console.log ('submit', form.data());
    form.post(route('listing-applications.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            emit('update:is-open', false);
            emit('update:status', 'applied');
        },
        onError: (e) => {
            // console.log ('error', e);
        },
    });
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md rounded-lg bg-card p-6 shadow-lg transition-all">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-xl font-semibold text-card-foreground">Submit Application</h1>
                <Button variant="ghost" size="icon" @click="closeModal" class="h-8 w-8">
                    <span class="sr-only">Close</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </Button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label for="cv" class="text-card-foreground">
                            {{ form.cv ? 'Profile CV' : 'Upload CV' }}
                        </Label>
                        <Input v-if="!form.cv" id="cv" type="file" @change="handleCvChange" class="h-10 w-full" />
                        <div v-else class="flex flex-wrap items-center gap-2">
                            <span class="text-sm text-muted-foreground">Current CV: {{ form.cv.name || 'Profile CV' }}</span>
                            <Button type="button" variant="outline" size="sm" @click="form.cv = null"> Change </Button>
                        </div>
                        <InputError :message="form.errors.cv" />
                    </div>

                    <div class="space-y-2">
                        <Label for="cover_letter" class="text-card-foreground">Cover Letter</Label>
                        <textarea
                            id="cover_letter"
                            v-model="form.cover_letter"
                            class="min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            placeholder="Tell us why you're interested in this position"
                        ></textarea>
                        <InputError :message="form.errors.cover_letter" />
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="closeModal"> Cancel </Button>
                    <Button type="submit" :disabled="form.processing"> Submit Application </Button>
                </div>
            </form>
        </div>
    </div>
</template>
