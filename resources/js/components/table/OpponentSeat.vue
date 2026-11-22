<script setup lang="ts">
import CardBack from '@/components/CardBack.vue';
import type { SeatView } from '@/types';

defineProps<{
    seat: SeatView;
    away: boolean;
    showPassed: boolean;
    vertical?: boolean;
}>();
</script>

<template>
    <div
        class="flex flex-col items-center gap-3 rounded-xl px-4 py-3 transition"
        :class="seat.isTurn && 'animate-ember bg-forest-800/50'"
    >
        <div class="flex items-center gap-2">
            <span
                class="h-2.5 w-2.5 rounded-full"
                :class="
                    away
                        ? 'bg-parchment-300/30'
                        : 'bg-lichen-300 shadow-[0_0_6px_rgb(167_183_142/0.8)]'
                "
                :title="away ? 'Away' : 'By the fire'"
            />
            <span class="font-display text-2xl text-parchment-50">
                {{ seat.name }}
            </span>
            <span v-if="away" class="text-base text-parchment-300/60 italic">
                away
            </span>
        </div>

        <div class="flex items-center gap-3 text-base text-lichen-200">
            <span>Score {{ seat.score }}</span>
            <span
                v-if="showPassed"
                class="rounded-full px-2 py-0.5 text-sm"
                :class="
                    seat.hasPassed
                        ? 'bg-moss-600 text-lichen-200'
                        : 'bg-bark-700/70 text-parchment-300'
                "
            >
                {{ seat.hasPassed ? 'passed' : 'choosing…' }}
            </span>
        </div>

        <div :class="vertical ? 'flex flex-col' : 'flex'">
            <CardBack
                v-for="index in seat.cardCount"
                :key="index"
                size="xs"
                :class="
                    index > 1 && (vertical ? '-mt-[2.4rem]' : '-ml-[1.35rem]')
                "
            />
        </div>
    </div>
</template>
