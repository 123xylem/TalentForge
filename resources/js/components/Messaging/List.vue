<script setup lang="ts">
import Conversation from '@/components/Messaging/Conversation.vue';
import { useCsrf } from '@/composables/useCsrf';
import { useTextFormatter } from '@/composables/useTextFormatter';
import { useForm, usePage } from '@inertiajs/vue3';
import { MessageCircle } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';

const { truncate } = useTextFormatter();
const { csrf } = useCsrf();

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

const form = useForm({
    _method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrf.value || '',
    },
});

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
            'X-CSRF-TOKEN': csrf.value || '',
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
            <div v-if="!showMessaging" class="rounded-full shadow-[0_0_18px_rgba(255,255,255,0.25)] shadow-lg hover:bg-accent/90">
                <MessageCircle class="h-8 w-8" />
            </div>
        </div>
        <div v-if="showMessaging" class="rounded-lg border border-border bg-background shadow-lg">
            <h2 class="flex cursor-default rounded-t-lg border-b border-border bg-accent p-2 font-bold text-accent-foreground">
                Message
                <span
                    @click="showMessaging = !showMessaging"
                    class="ml-auto flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-destructive text-destructive-foreground"
                    >X</span
                >
            </h2>
            <div id="messaging-user-rows" class="mt-2 grid max-h-[60vh] gap-2 overflow-y-auto p-2">
                <div
                    v-for="user in users"
                    :key="user.id"
                    class="flex items-center justify-between rounded-lg border border-border bg-background p-2 shadow-sm"
                >
                    <div id="messaging-user-row" class="flex w-full items-center justify-between">
                        <h3 class="text-sm font-medium text-foreground">
                            {{ truncate(user.name, 10) }}
                        </h3>

                        <button
                            v-if="!isConnected[user.id] && !hasPendingRequest[user.id]"
                            @click="sendConnectionRequest(user.id)"
                            class="ml-2 rounded bg-accent px-2 py-1 text-sm text-accent-foreground hover:bg-accent/90"
                        >
                            Connect
                        </button>
                        <span v-else-if="hasPendingRequest[user.id]" class="ml-auto text-sm text-muted-foreground"> Request Pending </span>
                        <button
                            v-else
                            @click="activateConversation(user.id)"
                            class="ml-auto rounded bg-accent px-2 py-1 text-sm text-accent-foreground hover:bg-accent/90"
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
        min-width: 170px;
    }
}
</style>
