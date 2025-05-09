<script setup lang="ts">
import Conversation from '@/components/Messaging/Conversation.vue';
import { usePage } from '@inertiajs/vue3';
import { MessageCircle } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
interface User {
    id: number;
    name: string;
    // Add other user fields you need
}

const users = ref<User[]>([]);
const isConnected = ref<Record<number, boolean>>({});
const hasPendingRequest = ref<Record<number, boolean>>({});
const showMessaging = ref(false);
const currentUser = usePage().props.auth.user.id;

const activeConversationId = ref<number | null>(null);
onMounted(() => {
    // Fetch users and their connection status
    try {
        fetch('/users', {
            headers: {
                'Cache-Control': 'no-cache',
                Pragma: 'no-cache',
            },
        })
            .then((res) => res.json())
            .then((data) => {
                users.value = data.users.data;
                isConnected.value = data.connections;
                hasPendingRequest.value = data.pendingRequests;
            });
    } catch (error) {
        console.error(error);
    }
});

const sendConnectionRequest = (userId: number) => {
    hasPendingRequest.value[userId] = true;
    fetch(`/connections/${userId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
    });
};

const changeActiveConversation = (userId: number) => {
    return activeConversationId.value == userId ? (activeConversationId.value = null) : (activeConversationId.value = userId);
};
</script>

<template>
    <div id="messaging-container" class="fixed bottom-10 right-5 z-50 cursor-pointer hover:cursor-pointer">
        <div @click="showMessaging = !showMessaging">
            <div v-if="showMessaging" class="ml-auto flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white">X</div>
            <div v-else>
                <MessageCircle class="h-6 w-6" />
            </div>
        </div>
        <div v-if="showMessaging">
            <h2 class="font-bold">Message</h2>
            <div class="grid gap-1">
                <div v-for="user in users" :key="user.id" class="flex items-center justify-between rounded-lg bg-white p-1 shadow">
                    <div>
                        <h3 v-if="activeConversationId != user.id" class="text-sm font-medium text-black">{{ user.name }}</h3>
                    </div>
                    <div>
                        <button
                            v-if="!isConnected[user.id] && !hasPendingRequest[user.id]"
                            @click="sendConnectionRequest(user.id)"
                            class="rounded bg-blue-500 px-1 py-1 text-sm text-white hover:bg-blue-600"
                        >
                            Connect
                        </button>
                        <span v-else-if="hasPendingRequest[user.id]" class="text-gray-500"> Request Pending </span>
                        <Conversation
                            v-if="isConnected[user.id]"
                            :key="user.id"
                            :is-active="activeConversationId === user.id"
                            @activate="changeActiveConversation(user.id)"
                            :recipient="user"
                            :currentUser="currentUser"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
