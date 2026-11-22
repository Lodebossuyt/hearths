<script setup lang="ts">
import { cn } from '@/lib/utils';

withDefaults(
    defineProps<{
        variant?: 'ember' | 'moss' | 'ghost' | 'berry';
        type?: 'button' | 'submit';
        disabled?: boolean;
    }>(),
    { variant: 'ember', type: 'button', disabled: false },
);

const variants = {
    ember: 'border-ember-400/70 bg-gradient-to-b from-ember-400 to-ember-600 text-forest-950 shadow-[0_0_18px_rgb(240_160_75/0.3)] hover:from-ember-300 hover:to-ember-400',
    moss: 'border-lichen-300/40 bg-moss-600 text-parchment-50 hover:bg-moss-500',
    ghost: 'border-gold-400/30 bg-transparent text-parchment-100 hover:border-gold-300/60 hover:bg-forest-800/60',
    berry: 'border-rose-400/50 bg-berry-700 text-parchment-50 hover:bg-berry-600',
};
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="
            cn(
                'inline-flex items-center justify-center gap-2 rounded-md border px-4 py-2 font-display text-lg tracking-wide transition',
                'focus-visible:ring-2 focus-visible:ring-gold-300 focus-visible:outline-none',
                'disabled:cursor-not-allowed disabled:opacity-45',
                variants[variant],
            )
        "
    >
        <slot />
    </button>
</template>
