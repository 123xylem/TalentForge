<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const notifications = ref([]);
const showNotifications = ref(false);

// Get notifications from auth user
notifications.value = usePage().props.auth.user.notifications;

// Use computed instead of watch
const unreadNotifications = computed(() => notifications.value.filter((notification) => notification.read_at === null));
console.log(notifications.value, 'unread:', unreadNotifications.value, 'a');

const form = useForm({
    notification_id: '',
});

const submitOne = (id: string) => {
    event?.preventDefault();
    form.notification_id = id;
    form.patch(route('notifications.markAsRead', id), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            notifications.value = usePage().props.auth.user.notifications;
        },
    });
};

const submitAll = () => {
    form.patch(route('notifications.markAllAsRead'), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            notifications.value = usePage().props.auth.user.notifications;
        },
    });
};
</script>
<!-- //TODO add url to notifications -->

<template>
    <div v-if="unreadNotifications.length > 0" class="relative ml-auto">
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
            <span class="absolute -right-1 -top-1 rounded-full bg-red-500 px-2 py-1 text-xs text-white">
                {{ unreadNotifications?.length }}
            </span>
        </button>
        <!-- Dropdown -->
        <div v-if="unreadNotifications?.length && showNotifications" class="absolute right-0 z-50 mt-2 w-80 rounded-md bg-white shadow-lg">
            <div class="py-1">
                <form @submit.prevent="submitAll" method="POST" class="absolute right-2 top-2 w-fit">
                    <input
                        type="submit"
                        class="ml-auto rounded-full bg-red-500 px-2 py-1 text-xs text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                        value="Clear All"
                    />
                </form>
                <div v-for="notification in unreadNotifications" :key="notification.id" class="px-4 py-2">
                    <form class="flex flex-wrap items-center gap-2 text-gray-900 hover:cursor-pointer" method="POST">
                        <div v-if="notification.data.employerAction" class="text-sm text-gray-900">
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
                            @click="submitOne(notification.id)"
                            type="submit"
                            class="rounded-full bg-blue-500 px-2 py-1 text-xs text-neutral-500 text-white hover:cursor-pointer hover:bg-blue-600"
                            value="Clear"
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
