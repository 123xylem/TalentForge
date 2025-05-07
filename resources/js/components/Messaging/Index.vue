<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
const channel = window.Echo.channel('messaging.1');
channel.listen('.my-event', function (data) {
    alert(JSON.stringify(data));
});

const props = defineProps({
    user: Object,
});

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
</script>
<template>
    <div class="fixed bottom-4 right-4 z-50" id="messaging-container">
        <!-- Chat Button/Modal -->
        <div
            v-if="isExpanded"
            @click="isExpanded = false"
            class="ml-auto flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white"
        >
            X
        </div>
        <div
            class="overflow-hidden rounded-lg bg-white shadow-lg transition-all duration-300 ease-in-out"
            :class="isExpanded ? 'h-[500px] w-[200px]' : 'h-12 w-12'"
        >
            <!-- Header -->
            <div class="flex cursor-pointer items-center justify-between bg-blue-500 p-2 text-white" @click="isExpanded = !isExpanded">
                <span v-if="isExpanded">Chat</span>
                <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                    />
                </svg>
            </div>

            <!-- Messages Area -->
            <div v-if="isExpanded" class="h-[calc(500px-8rem)] overflow-y-auto p-4">
                <div class="messages space-y-2">
                    <div class="message rounded-lg bg-gray-100 p-2">
                        <div class="message-content">Hello</div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div v-if="isExpanded" class="border-t p-2">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input
                        type="text"
                        v-model="message"
                        class="flex-1 rounded border p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Type a message..."
                    />
                    <button type="submit" class="rounded bg-blue-500 px-4 py-2 text-white transition-colors hover:bg-blue-600">Send</button>
                </form>
            </div>
        </div>
    </div>
</template>
