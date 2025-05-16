<script setup lang="ts">
import { Message } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref, watch } from 'vue';

// Props and State
const props = defineProps({
    currentUser: { type: Number, required: true },
    isActive: { type: Boolean, required: true },
    recipient: { type: Object, required: true },
});

const emit = defineEmits(['activate', 'deactivate']);

// Refs
const messageStream = ref<Message[]>([]);
const conversationId = ref(0);
const conversationNames = ref<{ [key: number]: string }>({});
const message = ref('');
const messageScroller = ref<HTMLDivElement | null>(null);
let currentChannel: any = null;

// Form Setup
const messageForm = useForm({
    user_id: props.currentUser,
    content: '',
    conversation_id: conversationId.value,
    created_at: new Date().toISOString(),
});

// Lifecycle Hooks
onMounted(async () => {
    if (props.isActive) {
        toggleExpand();
    }
});

onUnmounted(() => {
    cleanupChannel();
});

// Watchers
watch(
    () => props.isActive,
    (newValue) => {
        if (newValue) {
            toggleExpand();
        }
    },
);

watch(conversationId, async (newId) => {
    if (newId) {
        subscribeToChannel(newId);
        fetchConvoMessages(newId);
        if (messageScroller.value) {
            setTimeout(() => {
                if (messageScroller.value) {
                    messageScroller.value.scrollTop = messageScroller.value.scrollHeight;
                }
            }, 140);
        }
    }
});

// Message Handling
const sendMessage = () => {
    messageForm.content = message.value;
    messageForm.conversation_id = conversationId.value;
    messageStream.value = [...messageStream.value, messageForm.data() as Message];
    messageForm.post(route('messages.store'), {
        onSuccess: () => {
            messageForm.reset();
            message.value = '';
        },
        onError: (error) => {
            console.log(error, 'Error sending message');
        },
    });
};

// Channel Management
const cleanupChannel = () => {
    if (currentChannel) {
        currentChannel.unsubscribe();
        currentChannel = null;
    }
};

const subscribeToChannel = (newId: number) => {
    currentChannel = window.Echo.private(`conversation.${newId}`);
    currentChannel.listen('.new-message', function (data: Message) {
        if (data.user_id !== props.currentUser) {
            messageStream.value = [...messageStream.value, data];
        }
    });
};

// Conversation Management
const toggleExpand = async () => {
    if (conversationId.value != 0) {
        emit('deactivate');
        return;
    }
    getOrCreateConversation();
};

const getOrCreateConversation = async () => {
    const response = await fetch(
        route('conversations.getOrCreateOne', {
            recipient_id: props.recipient?.id,
            user_id: props.currentUser,
        }),
        {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        },
    );
    const data = await response.json();
    conversationId.value = Number(data.conversation_id);
    conversationNames.value = data.names;
    emit('activate');
};

// Data Fetching
const fetchConvoMessages = async (newId: number) => {
    await fetch(route('messages.index', { conversation_id: newId }), {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then((res) => res.json())
        .then((data) => {
            messageStream.value = data;
        });
};

const closeConversation = () => {
    conversationId.value = 0;
    emit('deactivate');
};
</script>
<template>
    <div class="" id="messaging-container" :data-user-id="recipient.id">
        <!-- Chat Button/Modal -->
        <div
            class="overflow-hidden rounded-lg bg-white shadow-lg transition-all duration-300 ease-in-out"
            :class="isActive ? 'z-100 fixed bottom-1 right-0 top-1 h-[100%] w-full max-w-[300px]' : 'z-10'"
        >
            <!-- Header -->
            <div
                v-if="isActive"
                class="absolute left-0 right-0 top-0 z-10 flex cursor-pointer items-center justify-between bg-blue-500 p-2 text-black"
            >
                <span class="text-white">{{ recipient.name }}</span>
                <div
                    @click="closeConversation"
                    class="z-50 ml-auto flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-gray-500 text-white"
                >
                    X
                </div>
            </div>
            <!-- Messages Area -->
            <div v-if="isActive" ref="messageScroller" id="message-scroller" class="y-2 max-h-[87%] cursor-default overflow-scroll p-4">
                <div v-for="message in messageStream" :key="message.id">
                    <div
                        class="message mt-2 rounded-lg p-2"
                        :class="
                            message.user_id === currentUser
                                ? 'self-start bg-green-500 text-left text-white'
                                : 'self-end bg-blue-100 text-right text-black'
                        "
                    >
                        <div class="message-content">
                            <p class="text-xs italic">{{ message.user_id === currentUser ? 'You' : conversationNames[message.user_id] }}</p>
                            <p>{{ message.content }}</p>
                        </div>
                        <p class="w-full text-right text-xs">{{ new Date(message.created_at).toLocaleString() }}</p>
                    </div>
                </div>
                <div id="anchor"></div>
            </div>

            <!-- Input Area -->
            <div v-if="isActive" class="border-t p-2">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input
                        type="text"
                        v-model="message"
                        class="flex-1 rounded border p-2 text-black focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Type a message..."
                    />
                    <button
                        :disabled="message.length === 0"
                        type="submit"
                        class="rounded bg-blue-500 px-4 py-2 text-black transition-colors hover:cursor-pointer hover:bg-blue-600"
                    >
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
<style scoped>
#message-scroller {
    scroll-behavior: smooth;
}
#message-scroller * {
    overflow-anchor: none;
}
#message-scroller > #anchor {
    overflow-anchor: auto;
    height: 1px;
}
</style>
