<script setup lang="ts">
import MessagingModal from '@/components/Messaging/Index.vue';
import { usePage } from '@inertiajs/vue3';
import { Mail, MessageCircle } from 'lucide-vue-next';
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
const currentUser = usePage().props.user;
const showMessagingModal = ref(false);

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
    fetch(`/connections/${userId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
    }).then(() => {
        hasPendingRequest.value[userId] = true;
    });
};

const openMessaging = (userId: number) => {
    showMessaging.value = true;
    console.log(userId);
};
</script>

<template>
    <div id="messaging-container" class="fixed bottom-4 right-4 z-50 cursor-pointer hover:cursor-pointer">
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
                        <h3 class="text-sm font-medium text-black">{{ user.name }}</h3>
                        <!-- Add other user info -->
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
                        <!-- TODO onclick open messaging -->
                        <span v-else @click="openMessaging(user.id)" class="text-green-500"> Message <Mail class="h-4 w-4" /></span>
                        <MessagingModal v-if="showMessagingModal" :currentUser="currentUser" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
