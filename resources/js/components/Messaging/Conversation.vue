<script setup lang="ts">
import { Message } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    currentUser: { type: Number, required: true },
    isActive: { type: Boolean, required: true },
    recipient: { type: Object, required: true },
});

const emit = defineEmits(['activate', 'deactivate']);

const messageStream = ref<Message[]>([]);
const conversationId = ref(0);
const conversationNames = ref<{ [key: number]: string }>({});
const message = ref('');
const messageScroller = ref<HTMLDivElement | null>(null);
let currentChannel: any = null;

const messageForm = useForm({
    user_id: props.currentUser,
    content: '',
    conversation_id: conversationId.value,
    created_at: new Date().toISOString(),
});

onUnmounted(() => {
    cleanupChannel();
});

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
        scrollToBottom();
    }
});

// Message Handling
const sendMessage = () => {
    messageForm.content = message.value;
    messageForm.conversation_id = conversationId.value;
    messageStream.value = [...messageStream.value, messageForm.data() as Message];
    scrollToBottom();

    messageForm.post(route('messages.store'), {
        onSuccess: () => {
            messageForm.reset();
            message.value = '';
        },
        onError: (error) => {
            console.error(error, 'Error sending message');
        },
    });
};

const scrollToBottom = () => {
    if (messageScroller.value) {
        setTimeout(() => {
            requestAnimationFrame(() => {
                if (messageScroller.value) {
                    messageScroller.value.scrollTop = messageScroller.value.scrollHeight;
                }
            });
        }, 180);
    }
};

// Channel Management
const cleanupChannel = () => {
    if (currentChannel) {
        window.Echo.leave(`conversation.${conversationId.value}`);
        currentChannel = null;
    }
};

const subscribeToChannel = (newId: number) => {
    currentChannel = window.Echo.private(`conversation.${newId}`);

    currentChannel.listen('.new-message', function (data: Message) {
        if (data.user_id !== props.currentUser) {
            messageStream.value = [...messageStream.value, data];
            scrollToBottom();
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
};

//FetchMessages
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
            class="overflow-hidden rounded-lg bg-background shadow-lg transition-all duration-300 ease-in-out"
            :class="isActive ? 'z-100 fixed bottom-0 right-0 top-5 flex w-full max-w-[300px] flex-col' : 'z-10'"
            :style="isActive ? { height: '95dvh' } : {}"
        >
            <!-- Header -->
            <div v-if="isActive" class="flex flex-none cursor-pointer items-center justify-between bg-accent p-2 text-accent-foreground">
                <span>{{ recipient.name }}</span>
                <div
                    @click="closeConversation"
                    class="z-50 ml-auto flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-destructive text-destructive-foreground"
                >
                    X
                </div>
            </div>
            <!-- Messages Area -->
            <div v-if="isActive" ref="messageScroller" id="message-scroller" class="flex-1 overflow-y-auto p-4">
                <div v-for="message in messageStream" :key="message.id" class="flex flex-col">
                    <div
                        class="message mt-2 max-w-[80%] rounded-lg p-2"
                        :class="
                            message.user_id === currentUser
                                ? 'self-end bg-accent text-accent-foreground'
                                : 'self-start bg-secondary/20 text-foreground'
                        "
                    >
                        <div class="message-content">
                            <p class="text-xs italic">{{ message.user_id === currentUser ? 'You' : conversationNames[message.user_id] }}</p>
                            <p>{{ message.content }}</p>
                        </div>
                        <p class="w-full text-right text-xs opacity-70">{{ new Date(message.created_at).toLocaleString() }}</p>
                    </div>
                </div>
                <div id="anchor"></div>
            </div>

            <!-- Input Area -->
            <div v-if="isActive" class="flex-none border-t border-border p-2">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input
                        type="text"
                        v-model="message"
                        class="flex-1 rounded border border-input bg-background p-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                        placeholder="Type a message..."
                    />
                    <button
                        :disabled="message.length === 0"
                        type="submit"
                        class="rounded bg-accent px-4 py-2 text-accent-foreground transition-colors hover:bg-accent/90 disabled:opacity-50"
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
