<template>
    <div
        :class="[
            'mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8',
            className
        ]"
    >
        <slot />
    </div>
</template>

<script setup>
defineProps({
    className: {
        type: String,
        default: ''
    }
})
</script> 