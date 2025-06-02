<template>
    <button
        :class="[
            'inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50',
            sizeClasses[size],
            variantClasses[variant],
            className
        ]"
        :disabled="disabled"
        v-bind="$attrs"
    >
        <slot />
    </button>
</template>

<script setup>
const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'accent', 'destructive', 'ghost', 'link'].includes(value)
    },
    size: {
        type: String,
        default: 'default',
        validator: (value) => ['sm', 'default', 'lg', 'icon'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    },
    className: {
        type: String,
        default: ''
    }
})

const sizeClasses = {
    sm: 'h-8 px-3 text-xs',
    default: 'h-10 px-4 py-2',
    lg: 'h-12 px-6 text-lg',
    icon: 'h-10 w-10'
}

const variantClasses = {
    primary: 'bg-primary text-primary-foreground hover:bg-primary/90',
    secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/90',
    accent: 'bg-accent text-accent-foreground hover:bg-accent/90',
    destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
    ghost: 'hover:bg-accent hover:text-accent-foreground',
    link: 'text-primary underline-offset-4 hover:underline'
}
</script> 