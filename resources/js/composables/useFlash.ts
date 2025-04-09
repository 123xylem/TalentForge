import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface FlashMessage {
    success?: string;
    errors?: string[];
}

export function useFlash() {
    const page = usePage();
    console.log('page', page.props);
    const flash = ref<FlashMessage>({
        success: page.props.flash?.success,
        errors: page.props.flash?.errors,
    });
    alert(page.props.flash);
    // Watch for changes in page props
    watch(
        () => page.props.flash,
        (newFlash) => {
            console.log('newFlash', newFlash);
            alert(newFlash);

            if (newFlash) {
                flash.value = {
                    success: newFlash.success,
                    errors: newFlash.errors,
                };
            }
        },
        { immediate: true },
    );

    const clear = () => {
        // Clear Inertia's page props flash
        delete page.props.flash;

        page.props.flash = {
            success: undefined,
            errors: undefined,
        };
    };
    return {
        flash,
        clear,
    };
}
