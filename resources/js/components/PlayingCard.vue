<script setup lang="ts">
import { computed } from 'vue';
import { cardName, cardRankLabel, isRedCard, suitSymbol } from '@/lib/cards';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        code: string;
        size?: 'sm' | 'md' | 'lg';
        playable?: boolean;
        dimmed?: boolean;
        selected?: boolean;
        received?: boolean;
        interactive?: boolean;
    }>(),
    {
        size: 'md',
        playable: false,
        dimmed: false,
        selected: false,
        received: false,
        interactive: false,
    },
);

defineEmits<{ choose: [code: string] }>();

const rank = computed(() => cardRankLabel(props.code));
const suit = computed(() => suitSymbol(props.code));
const isRed = computed(() => isRedCard(props.code));

const sizeClasses = {
    sm: 'h-[4.5rem] w-12 text-sm',
    md: 'h-32 w-[5.6rem] text-lg',
    lg: 'h-36 w-[6.2rem] text-xl',
};
</script>

<template>
    <component
        :is="interactive ? 'button' : 'div'"
        :type="interactive ? 'button' : undefined"
        :disabled="interactive && dimmed ? true : undefined"
        :aria-label="cardName(code)"
        :aria-pressed="interactive ? selected : undefined"
        :class="
            cn(
                'relative flex shrink-0 flex-col justify-between rounded-lg border bg-parchment-50 px-1.5 py-1 font-display leading-none select-none',
                'border-bark-500/60 shadow-[0_4px_10px_rgb(0_0_0/0.45),inset_0_0_18px_rgb(122_95_69/0.25)]',
                'transition duration-200 ease-out',
                sizeClasses[size],
                isRed ? 'text-berry-600' : 'text-charcoal-900',
                interactive && !dimmed && 'cursor-pointer hover:-translate-y-3',
                playable && 'animate-ember',
                selected && '-translate-y-5 ring-2 ring-gold-300',
                received && !selected && 'ring-2 ring-lichen-300',
                dimmed &&
                    'cursor-not-allowed brightness-50 grayscale-[60%] saturate-50',
            )
        "
        @click="interactive && !dimmed && $emit('choose', code)"
    >
        <span class="flex flex-col items-start">
            <span>{{ rank }}</span>
            <span class="-mt-0.5">{{ suit }}</span>
        </span>
        <span
            class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-85"
            :class="size === 'sm' ? 'text-2xl' : 'text-5xl'"
            aria-hidden="true"
        >
            {{ suit }}
        </span>
        <span class="flex rotate-180 flex-col items-start">
            <span>{{ rank }}</span>
            <span class="-mt-0.5">{{ suit }}</span>
        </span>
        <span
            v-if="received"
            class="absolute -top-2 left-1/2 -translate-x-1/2 rounded-full bg-lichen-300 px-1.5 py-0.5 font-sans text-[0.6rem] tracking-wide text-forest-950 uppercase"
        >
            gift
        </span>
    </component>
</template>
