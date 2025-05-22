<script setup lang="ts">
import Conversation from '@/components/Messaging/Conversation.vue';
import { usePage } from '@inertiajs/vue3';
import { MessageCircle } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';
interface User {
    id: number;
    name: string;
}

const users = ref<User[]>([]);
const isConnected = ref<Record<number, boolean>>({});
const hasPendingRequest = ref<Record<number, boolean>>({});
const showMessaging = ref(false);
const currentUser = (usePage().props.auth as { user: { id: number } }).user.id;

const activeUserConvoId = ref<number | null>(null);

const fetchUsersAndConnections = async () => {
    //TODO: add cache to user list
    try {
        fetch('/users', {})
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

const activateConversation = (userId: number) => {
    activeUserConvoId.value = userId;
    showMessaging.value = true;
};

const deactivateConversation = () => {
    activeUserConvoId.value = null;
};

// Updates user connection status and opens a chat window.
const handleChatActivation = (event: CustomEvent) => {
    if (hasPendingRequest.value[event.detail.senderId]) {
        hasPendingRequest.value[event.detail.senderId] = false;
        isConnected.value[event.detail.senderId] = true;
    }
    const userId = event.detail.senderId;
    activateConversation(userId);
};

//Listeners that can activate a chat window when requested from notifications.

onMounted(async () => {
    await fetchUsersAndConnections();
    window.addEventListener('activate-chat', handleChatActivation as EventListener);
});

onUnmounted(() => {
    window.removeEventListener('activate-chat', handleChatActivation as EventListener);
});
</script>

<template>
    <div id="messaging-user-list" class="fixed bottom-20 right-10 z-50">
        <div @click="showMessaging = !showMessaging" class="cursor-pointer">
            <div v-if="!showMessaging" class="h-6 w-6 rounded-full bg-black shadow-lg hover:cursor-pointer">
                <MessageCircle class="h-6 w-6 text-white" />
            </div>
        </div>
        <div v-if="showMessaging" class="rounded-lg bg-white shadow-lg">
            <h2 class="flex cursor-default rounded-lg border-2 border-blue-500 bg-blue-500 p-1 font-bold text-white">
                Message
                <span
                    @click="showMessaging = !showMessaging"
                    class="ml-auto flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-red-500 text-white"
                    >X</span
                >
            </h2>
            <div id="messaging-user-rows" class="mt-1 grid max-h-[60vh] gap-1 overflow-y-auto">
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
<style scoped>
@media (max-width: 640px) {
    #messaging-user-list {
        bottom: 1rem;
        right: 1rem;
        max-width: 90vw;
    }

    #messaging-user-rows {
        max-height: 50vh;
    }
}
</style>
