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

const handleNotificationAction = async (conversationId: string, senderId: string, notificationId: string) => {
    await window.dispatchEvent(
        new CustomEvent('activate-chat', {
            detail: { conversationId: conversationId, senderId: senderId },
        }),
    );
    markAsRead(notificationId);
};
</script>

<template>
    <Head title="Notifications" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-foreground">Notifications</h1>
                <div class="flex gap-2">
                    <button @click="markAllAsRead" class="btn-accent rounded-lg px-4 py-2">Mark all as read</button>
                    <button @click="confirmModalOpen('deleteAll')" class="btn-destructive rounded-lg px-4 py-2">Delete all</button>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden rounded-lg border border-border bg-card shadow-sm">
                        <table class="min-w-full divide-y divide-border">
                            <thead class="bg-muted/50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-foreground sm:pl-6">Message</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-foreground">Date</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-background">
                                <tr v-for="notification in notifications" :key="notification.id" :class="{ 'bg-muted/30': !notification.read_at }">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-2 w-2 flex-shrink-0 rounded-full"
                                                :class="notification.read_at ? 'bg-muted' : 'bg-accent'"
                                            ></div>
                                            <div v-if="notification.data.message" class="flex flex-wrap text-sm text-foreground">
                                                <template v-if="notification.data.open_message">
                                                    <a
                                                        href="#"
                                                        class="hover:text-accent"
                                                        @click="
                                                            handleNotificationAction(
                                                                notification.data.conversationId,
                                                                notification.data.user_id,
                                                                notification.id,
                                                            )
                                                        "
                                                    >
                                                        {{ notification.data.message }}
                                                    </a>
                                                </template>
                                                <template v-else>
                                                    <a :href="notification.data.url" class="hover:text-accent">
                                                        {{ notification.data.message }}
                                                    </a>
                                                </template>
                                            </div>
                                            <div v-else-if="!isEmployer" class="flex flex-wrap text-sm text-foreground">
                                                <a :href="notification.data.url" class="hover:text-accent">
                                                    Your application for {{ notification.data.title }} at {{ notification.data.company }} has been
                                                    {{ notification.data.status }}
                                                </a>
                                            </div>
                                            <div v-else class="flex flex-wrap text-sm text-foreground">
                                                <a :href="notification.data.url" class="hover:text-accent">
                                                    You have a new application for {{ notification.data.title }} at
                                                    {{ notification.data.company }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-muted-foreground">
                                        {{ new Date(notification.created_at).toLocaleString() }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <button
                                            v-if="!notification.read_at"
                                            @click="markAsRead(notification.id)"
                                            class="mr-4 rounded-lg px-3 py-1.5 text-accent hover:text-accent/80"
                                        >
                                            Mark as read
                                        </button>
                                        <button
                                            @click="confirmModalOpen('delete', notification.id)"
                                            class="rounded-lg px-3 py-1.5 text-destructive hover:text-destructive/80"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="flex flex-1 justify-between sm:hidden">
                    <a v-if="notificationsLinks[0]?.url" :href="notificationsLinks[0]?.url" class="btn-secondary rounded-lg px-4 py-2">Previous</a>
                    <a
                        v-if="notificationsLinks[notificationsLinks.length - 1].url"
                        :href="notificationsLinks[notificationsLinks.length - 1].url"
                        class="btn-secondary ml-3 rounded-lg px-4 py-2"
                    >
                        Next
                    </a>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Showing
                            <span class="font-medium text-foreground">{{ notifications.length }}</span>
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
                                        ? 'z-10 bg-accent text-accent-foreground focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent'
                                        : 'text-foreground ring-1 ring-inset ring-border hover:bg-muted/50 focus:outline-offset-0',
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
