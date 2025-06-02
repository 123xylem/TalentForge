import { ref } from 'vue';

export function useCsrf() {
    const csrf = ref(document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

    return {
        csrf,
    };
}
