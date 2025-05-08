<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
const channel = window.Echo.channel('messaging.1');
channel.listen('.my-event', function (data) {
    alert(JSON.stringify(data));
});

const props = defineProps({
    user: Object,
    isActive: Boolean,
    recipient: Object,
});

const emit = defineEmits(['activate']);

const message = ref('');
const isExpanded = ref(false);

const messageForm = useForm({
    user_id: props.user?.id,
    message: message.value,
});

const sendMessage = () => {
    messageForm.post(route('messages.store'), {
        onSuccess: (msg) => {
            console.log(msg, 'Message sent');
            messageForm.reset();
        },
        onError: (error) => {
            console.log(error, 'Error sending message');
        },
    });
};

watch(
    () => props.isActive,
    (newValue) => {
        isExpanded.value = newValue;
    },
);

const toggleExpand = () => {
    console.log('toggleExpand');
    emit('activate');
    isExpanded.value = !isExpanded.value;
};
</script>
<template>
    <div class="" id="messaging-container">
        <!-- Chat Button/Modal -->
        <div
            class="overflow-hidden rounded-lg bg-white shadow-lg transition-all duration-300 ease-in-out"
            :class="isExpanded ? 'bottom-0 top-0 h-[100%] w-[100%]' : ''"
        >
            <!-- Header -->
            <div v-if="isExpanded" class="flex cursor-pointer items-center justify-between bg-blue-500 p-2 text-black">
                <span>{{ recipient?.name }}</span>
                <div
                    v-if="isExpanded"
                    @click="toggleExpand"
                    class="ml-auto flex h-6 w-6 items-center justify-center rounded-full bg-gray-500 text-white"
                >
                    X
                </div>
            </div>
            <div v-else @click="toggleExpand" class="rounded bg-blue-500 px-1 py-1 text-sm hover:bg-blue-600">Chat</div>

            <!-- Messages Area -->
            <div v-if="isExpanded" class="h-100% overflow-y-auto p-4">
                <div class="messages space-y-2">
                    <div class="message rounded-lg bg-gray-100 p-2">
                        <div class="message-content text-black">Hello</div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div v-if="isExpanded" class="border-t p-2">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input
                        type="text"
                        v-model="message"
                        class="flex-1 rounded border p-2 text-black focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Type a message..."
                    />
                    <button type="submit" class="rounded bg-blue-500 px-4 py-2 text-black transition-colors hover:bg-blue-600">Send</button>
                </form>
            </div>
        </div>
    </div>
</template>
