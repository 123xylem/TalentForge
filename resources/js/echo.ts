import { useCsrf } from '@/composables/useCsrf';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const { csrf } = useCsrf();

window.Pusher = Pusher;

declare global {
    interface Window {
        Echo: any;
        Pusher: any;
    }
}

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authorizer: (channel: any) => {
        return {
            authorize: (socketId: string, callback: any) => {
                fetch('/broadcasting/auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Socket-ID': socketId,
                        'X-CSRF-TOKEN': csrf.value as string,
                    },
                    body: JSON.stringify({
                        socket_id: socketId,
                        channel_name: channel.name,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => callback(null, data))
                    .catch((error) => callback(error, null));
            },
        };
    },
});
