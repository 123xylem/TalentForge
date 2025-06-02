<script setup lang="ts">
import type { Notification } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
const notifications = ref<Notification[]>([]);
const showNotifications = ref(false);

// Get notifications from auth user
notifications.value = (usePage().props.auth as any).user.notifications.data;
const unreadNotifications = computed(() => notifications.value.filter((notification) => notification.read_at === null)) ?? {};
const form = useForm({
    notification_id: '',
});

//TODO: deleteOne instead of markAsRead
//TODO: markasRead once clicked by default
const submitOne = (id: string) => {
    event?.preventDefault();
    form.notification_id = id;
    notifications.value
        .filter((notification) => notification.id === id)
        .map((notification) => {
            notification.read_at = 'Temp Now';
        });

    form.patch(route('notifications.markAsRead', id), {
        preserveScroll: true,
        onSuccess: () => {
            notifications.value = notifications.value.filter((n) => n.id !== id);
        },
        // Dont update Page as we want notifications to be updated without refreshsss
        onError: (e) => {
            console.error(e, 'error marking notification as read');
        },
    });
};

const submitAll = () => {
    notifications.value.map((notification) => {
        notification.read_at = 'Temp Now';
    });
    form.patch(route('notifications.markAllAsRead'), {
        preserveScroll: true,
        // preserveState: false,
        onError: (e) => {
            console.log(e, 'error');
        },
    });
};

const handleConnectionRequest = async (id: string, url: string, userId = '') => {
    const form = useForm({
        _token: usePage().props.csrf_token,
    });

    try {
        await form.post(url, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: async () => {
                // Remove notification from UI
                notifications.value = notifications.value.filter((n) => n.id !== id);
                submitOne(id);
                window.dispatchEvent(
                    new CustomEvent('activate-chat', {
                        detail: { senderId: userId },
                    }),
                );
                showNotifications.value = false;
            },
            onError: (errors) => {
                console.error('Connection request failed:', errors);
            },
        });
    } catch (error) {
        console.error('Error in handleConnectionRequest:', error);
    }
};

const handleNotificationAction = async (conversationId: string, senderId: string, notificationId: string) => {
    showNotifications.value = false;
    await window.dispatchEvent(
        new CustomEvent('activate-chat', {
            detail: { conversationId: conversationId, senderId: senderId },
        }),
    );
    submitOne(notificationId);
    showNotifications.value = false;
};

const hideReadNotifications = ref(false);
const visibleNotifications = computed(() => {
    if (hideReadNotifications.value) {
        return notifications.value.filter((notification) => notification.read_at === null);
    }
    return notifications.value;
});

const toggleHideReadNotifications = () => {
    hideReadNotifications.value = !hideReadNotifications.value;
};
</script>
<template>
    <div class="relative ml-auto">
        <button @click="showNotifications = !showNotifications" class="relative p-2">
            <span class="sr-only">Notifications</span>
            <svg class="h-6 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
            </svg>
            <span v-if="unreadNotifications.length > 0" class="absolute -right-1 -top-1 rounded-full bg-red-500 px-2 py-1 text-xs text-white">
                {{ unreadNotifications?.length }}
            </span>
        </button>
        <!-- Dropdown -->
        <div v-if="showNotifications && notifications.length > 0" class="absolute right-0 z-50 mt-2 w-80 rounded-md bg-white shadow-lg">
            <div class="py-1">
                <div class="flex items-center justify-between gap-2 px-2">
                    <div class="mr-auto px-4 py-2 text-left text-gray-500"><a href="/notifications">View All</a></div>
                    <button
                        @click="toggleHideReadNotifications"
                        class="ml-auto rounded-full bg-red-500 px-2 py-1 text-xs text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                    >
                        {{ hideReadNotifications ? 'Show Read' : 'Hide Read' }}
                    </button>

                    <form @submit.prevent="submitAll" method="POST" class="w-fit">
                        <input
                            type="submit"
                            class="ml-auto rounded-full bg-red-500 px-2 py-1 text-xs text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                            value="Read All"
                        />
                    </form>
                </div>
                <div v-for="notification in visibleNotifications" :key="notification.id" class="p">
                    <form
                        class="flex flex-wrap items-center gap-2 px-4 py-2 text-gray-900 hover:cursor-pointer"
                        :class="{ 'bg-gray-100': notification.read_at === null }"
                        method="POST"
                    >
                        <div v-if="notification.data.message" class="text-sm text-gray-900">
                            <div v-if="notification.data.connectionRequest">
                                {{ notification.data.message }}
                                <div v-if="notification.read_at === null" class="flex gap-2">
                                    <a
                                        class="touch-manipulation rounded-full bg-blue-500 px-3 py-2 text-xs text-white hover:bg-blue-600 active:bg-blue-700"
                                        @click="handleConnectionRequest(notification.id, notification.data.url, notification.data.user_id)"
                                        @touchstart.prevent
                                    >
                                        Accept
                                    </a>
                                    <a
                                        class="rounded-full bg-red-500 px-2 py-1 text-xs text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                                        @click="handleConnectionRequest(notification.id, notification.data.decline_url)"
                                    >
                                        Decline
                                    </a>
                                </div>
                            </div>
                            <div v-else-if="notification.data.open_message">
                                <a
                                    class="hover:cursor-pointer hover:underline"
                                    href="#"
                                    @click="handleNotificationAction(notification.data.conversationId, notification.data.user_id, notification.id)"
                                >
                                    {{ notification.data.message }}
                                </a>
                            </div>
                            <div v-else>
                                <a :href="notification.data.url">
                                    {{ notification.data.message }}
                                </a>
                            </div>
                        </div>
                        <div v-else-if="notification.data.employerAction" class="text-sm text-gray-900">
                            <a :href="notification.data.url">
                                Your application for {{ notification.data.title }} at {{ notification.data.company }} has been
                                {{ notification.data.status }}
                            </a>
                        </div>
                        <div v-else class="text-sm text-gray-900">
                            <a :href="notification.data.url">
                                You have a new application for {{ notification.data.title }} at {{ notification.data.company }}
                            </a>
                        </div>
                        <div class="w-full text-xs text-gray-500">
                            {{ new Date(notification.created_at).toLocaleString() }}
                        </div>
                        <input
                            v-if="notification.read_at === null"
                            @click="submitOne(notification.id)"
                            type="submit"
                            class="rounded-full bg-blue-500 px-2 py-1 text-xs text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                            value="Read"
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
