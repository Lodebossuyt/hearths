<script setup lang="ts">
import ForestButton from '@/components/ForestButton.vue';
import Ornament from '@/components/Ornament.vue';
import type { HistoryEntry, SeatView } from '@/types';

defineProps<{ entry: HistoryEntry; seats: SeatView[] }>();
defineEmits<{ dismiss: [] }>();
</script>

<template>
    <div
        class="fixed inset-0 z-40 flex items-center justify-center bg-forest-950/70 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        :aria-label="`Round ${entry.number} summary`"
    >
        <div class="panel-torn w-[28rem] px-8 py-8 text-center">
            <h2 class="font-display text-4xl text-ember-300 text-glow">
                Round {{ entry.number }} is done
            </h2>
            <p
                v-if="entry.moonShooter !== null"
                class="mt-3 text-xl text-gold-300 italic"
            >
                ☾
                {{
                    seats.find((seat) => seat.position === entry.moonShooter)
                        ?.name
                }}
                shot the moon! Everyone else takes 26.
            </p>
            <Ornament class="my-5" />

            <ul class="flex flex-col gap-2 text-xl">
                <li
                    v-for="seat in seats"
                    :key="seat.position"
                    class="flex justify-between"
                >
                    <span :class="seat.isMe && 'text-ember-300'">{{
                        seat.name
                    }}</span>
                    <span class="tabular-nums">
                        +{{ entry.scores[seat.position] }}
                        <span class="text-parchment-300"
                            >→ {{ seat.score }}</span
                        >
                    </span>
                </li>
            </ul>

            <ForestButton class="mt-7" v-focus @click="$emit('dismiss')">
                Deal me in
            </ForestButton>
        </div>
    </div>
</template>
