<script setup lang="ts">
import { computed } from 'vue';
import PlayingCard from '@/components/PlayingCard.vue';
import { sideOf } from '@/lib/cards';
import type { SeatView, TableSide, TrickView } from '@/types';

const props = defineProps<{
    trick: TrickView | null;
    myPosition: number;
    seats: SeatView[];
}>();

const placement: Record<TableSide, string> = {
    bottom: 'bottom-2 left-1/2 -translate-x-1/2',
    top: 'top-2 left-1/2 -translate-x-1/2',
    left: 'left-4 top-1/2 -translate-y-1/2 -rotate-6',
    right: 'right-4 top-1/2 -translate-y-1/2 rotate-6',
};

const drift: Record<TableSide, string> = {
    bottom: 'translate-y-10',
    top: '-translate-y-10',
    left: '-translate-x-12',
    right: 'translate-x-12',
};

const isComplete = computed(() => props.trick?.winnerPosition !== null);

const winnerName = computed(() => {
    const winner = props.trick?.winnerPosition;

    return props.seats.find((seat) => seat.position === winner)?.name ?? '';
});

const driftClass = computed(() => {
    const winner = props.trick?.winnerPosition;

    return winner === null || winner === undefined
        ? ''
        : drift[sideOf(winner, props.myPosition)];
});
</script>

<template>
    <div
        class="relative mx-auto h-[23rem] w-[26rem] rounded-full border border-gold-400/15 bg-[radial-gradient(circle,rgb(196_105_42/0.18),rgb(26_36_27/0.6)_60%,transparent_72%)]"
    >
        <div
            class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center gap-1 text-center"
        >
            <p
                v-if="!trick"
                class="max-w-48 text-lg text-parchment-300/70 italic"
            >
                The table waits for the first lead.
            </p>
        </div>

        <div
            v-if="trick"
            :key="trick.number"
            class="absolute inset-0 transition duration-[1400ms] ease-out"
            :class="isComplete && [driftClass, 'opacity-60 delay-700']"
        >
            <div
                v-for="play in trick.plays"
                :key="play.card"
                class="absolute"
                :class="placement[sideOf(play.position, myPosition)]"
            >
                <PlayingCard
                    :code="play.card"
                    size="md"
                    :class="
                        trick.winnerPosition === play.position &&
                        'ring-2 ring-gold-300'
                    "
                />
            </div>
        </div>

        <p
            v-if="trick && isComplete"
            class="absolute -bottom-9 left-1/2 -translate-x-1/2 font-display text-lg whitespace-nowrap text-gold-300"
        >
            {{ winnerName }} takes the trick
        </p>
    </div>
</template>
