<script setup lang="ts">
import Conversation from '@/components/Messaging/Conversation.vue';
import { usePage } from '@inertiajs/vue3';
import { MessageCircle } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';
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

const activeUserConvoId = ref<number | null>(null);
onMounted(() => {
    fetchUsersAndConnections();
});

const fetchUsersAndConnections = async () => {
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
};

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
    console.log('Active user convo id', activeUserConvoId.value, userId);

    if (activeUserConvoId.value === userId) {
        activeUserConvoId.value = null;
    } else {
        activeUserConvoId.value = userId;
    }
    console.log('Active user convo id', activeUserConvoId.value, userId);
};

const activateConversation = (userId: number) => {
    activeUserConvoId.value = userId;
    showMessaging.value = true;
};

const deactivateConversation = () => {
    activeUserConvoId.value = null;
};

const handleChatActivation = (event: CustomEvent) => {
    const userId = event.detail.senderId;
    activateConversation(userId);
};

onMounted(async () => {
    await fetchUsersAndConnections();
    window.addEventListener('activate-chat', handleChatActivation as EventListener);
});

onUnmounted(() => {
    window.removeEventListener('activate-chat', handleChatActivation as EventListener);
});
</script>

<template>
    <div id="messaging-user-list" class="fixed bottom-10 right-5 z-10 cursor-pointer hover:cursor-pointer">
        <div @click="showMessaging = !showMessaging">
            <div v-if="!showMessaging">
                <MessageCircle class="h-6 w-6" />
            </div>
        </div>
        <div v-if="showMessaging">
            <h2 class="flex cursor-default rounded-lg border-2 border-blue-500 bg-blue-500 p-1 font-bold text-white">
                Message
                <span
                    @click="showMessaging = !showMessaging"
                    class="ml-auto flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-red-500 text-white"
                    >X</span
                >
            </h2>
            <div id="messaging-user-rows" class="mt-1 grid gap-1">
                <div v-for="user in users" :key="user.id" class="flex items-center justify-between rounded-lg bg-white shadow">
                    <div id="messaging-user-row" class="flex w-full items-center justify-between" q>
                        <h3 class="p-1 text-sm font-medium text-black">
                            {{ user.name }}
                        </h3>

                        <button
                            v-if="!isConnected[user.id] && !hasPendingRequest[user.id]"
                            @click="sendConnectionRequest(user.id)"
                            class="m-1 ml-auto rounded bg-blue-500 p-1 text-sm text-white hover:bg-blue-600"
                        >
                            Connect
                        </button>
                        <span v-else-if="hasPendingRequest[user.id]" class="ml-auto p-1 text-gray-500"> Request Pending </span>
                        <button
                            v-else
                            @click="activateConversation(user.id)"
                            class="m-1 ml-auto rounded bg-blue-500 p-1 text-sm text-white hover:bg-blue-600"
                            :id="`chat-button-${user.id}`"
                        >
                            Chat!
                        </button>

                        <Conversation
                            v-if="isConnected[user.id]"
                            :key="user.id"
                            :is-active="activeUserConvoId === user.id"
                            @deactivate="deactivateConversation"
                            :recipient="user"
                            :current-user="currentUser"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
