<script setup lang="ts">
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { BreadcrumbItem, Notification, NotificationsLinks } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
console.log(usePage().props.flash, 'index flash top page');
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Notifications',
        href: '/settings/notifications',
    },
];

const confirmModalIsOpen = ref(false);
const modalAction = ref('');
const notificationId = ref('');
const notifications = ref<Notification[]>([]);
const notificationsLinks = ref<NotificationsLinks[]>([]);

console.log(usePage().props.auth.user.notifications);

notifications.value = usePage().props.auth.user.notifications.data;
notificationsLinks.value = usePage().props.auth.user.notifications.links;

const confirmModalOpen = (action: string, id?: string) => {
    confirmModalIsOpen.value = true;
    modalAction.value = action;
    notificationId.value = id ?? '';
    console.log(notificationId.value, modalAction.value);
};

const confirmModalClose = () => {
    confirmModalIsOpen.value = false;
    modalAction.value = '';
};

const markAsRead = (notificationId: string) => {
    notifications.value = notifications.value.map((notification) => {
        if (notification.id == notificationId) {
            notification.read_at = 'read';
        }
        return notification;
    });
    router.patch(route('notifications.markAsRead', { notification: notificationId }));
};

const markAllAsRead = () => {
    notifications.value = notifications.value.map((notification) => {
        notification.read_at = 'read';
        return notification;
    });
    router.patch(route('notifications.markAllAsRead'));
};

const deleteNotification = (notificationId?: string, action?: string) => {
    console.log(notificationId, action);
    if (action === 'deleteAll') {
        router.delete(route('notifications.destroyAll'), {
            onSuccess: () => {
                router.get(route('notifications.index'), { preserveState: false });
            },
        });
        confirmModalClose();
    } else {
        router.delete(route('notifications.destroy', { notification: notificationId }), {
            onSuccess: () => {
                console.log(usePage().props.flash);
                router.get(route('notifications.index'), { preserveState: false, replace: true });
                // window.location.href = route('notifications.index');
            },
        });
        confirmModalClose();
    }
};

const isEmployer = computed(() => {
    return usePage().props.auth.user.type === 'employer';
});
</script>

<template>
    <Head title="Notifications" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Notifications</h1>
                <button @click="markAllAsRead" class="rounded-md bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                    Mark all as read
                </button>
                <button
                    @click="confirmModalOpen('deleteAll')"
                    class="rounded-md bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600"
                >
                    Delete all
                </button>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Message</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="notification in notifications" :key="notification.id">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                            <div class="flex items-center">
                                                <div
                                                    class="h-2 w-2 flex-shrink-0 rounded-full"
                                                    :class="notification.read_at ? 'bg-gray-400' : 'bg-blue-500'"
                                                ></div>
                                                <div v-if="notification.data.message" class="flex flex-wrap text-sm text-gray-900">
                                                    <a :href="notification.data.url">
                                                        {{ notification.data.message }}
                                                    </a>
                                                </div>
                                                <div v-else-if="!isEmployer" class="flex flex-wrap text-sm text-gray-900">
                                                    <a :href="notification.data.url">
                                                        Your application for {{ notification.data.title }} at {{ notification.data.company }} has been
                                                        {{ notification.data.status }}
                                                    </a>
                                                </div>
                                                <div v-else class="flex flex-wrap text-sm text-gray-900">
                                                    <a :href="notification.data.url">
                                                        You have a new application for {{ notification.data.title }} at
                                                        {{ notification.data.company }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ new Date(notification.created_at).toLocaleString() }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <button
                                                v-if="!notification.read_at"
                                                @click="markAsRead(notification.id)"
                                                class="mr-4 text-blue-600 hover:text-blue-900"
                                            >
                                                Mark as read
                                            </button>
                                            <button @click="confirmModalOpen('delete', notification.id)" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="flex flex-1 justify-between sm:hidden">
                    <a
                        v-if="notificationsLinks[0]?.url"
                        :href="notificationsLinks[0]?.url"
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Previous
                    </a>
                    <a
                        v-if="notificationsLinks[notificationsLinks.length - 1].url"
                        :href="notificationsLinks[notificationsLinks.length - 1].url"
                        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Next
                    </a>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ notifications.length }}</span>
                            notifications
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <a
                                v-for="(link, index) in notificationsLinks"
                                :key="index"
                                :href="link.url"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold"
                                :class="[
                                    link.active
                                        ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                                        : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                                    index === 0 ? 'rounded-l-md' : '',
                                    index === notificationsLinks.length - 1 ? 'rounded-r-md' : '',
                                ]"
                                v-html="link.label"
                            ></a>
                        </nav>
                    </div>
                </div>
            </div>
            <ConfirmationModal
                :confirmModal="confirmModalIsOpen"
                :title="`Are you sure you want to ${modalAction}?`"
                :description="`This action cannot be undone.`"
                @confirm="deleteNotification(notificationId, modalAction)"
                @cancel="confirmModalClose"
            ></ConfirmationModal>
        </div>
    </AppLayout>
</template>
