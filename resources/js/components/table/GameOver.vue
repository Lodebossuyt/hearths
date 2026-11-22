<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Ornament from '@/components/Ornament.vue';
import { lobby } from '@/routes';
import type { TableView } from '@/types';

const props = defineProps<{ table: TableView }>();

const winnerNames = computed(() =>
    props.table.seats
        .filter((seat) => props.table.winners.includes(seat.position))
        .map((seat) => seat.name),
);

const standings = computed(() =>
    [...props.table.seats].sort((a, b) => a.score - b.score),
);
</script>

<template>
    <div
        class="fixed inset-0 z-40 flex items-center justify-center bg-forest-950/75 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-label="Game over"
    >
        <div class="panel-torn w-[30rem] px-8 py-9 text-center">
            <template v-if="table.status === 'finished'">
                <p class="text-lg tracking-[0.3em] text-lichen-300 uppercase">
                    The fire burns low
                </p>
                <h2 class="mt-2 font-display text-4xl text-ember-300 text-glow">
                    {{ winnerNames.join(' & ') }}
                    {{ winnerNames.length > 1 ? 'share the win' : 'wins' }}
                </h2>
                <Ornament class="my-5" />
                <ol class="flex flex-col gap-2 text-xl">
                    <li
                        v-for="seat in standings"
                        :key="seat.position"
                        class="flex justify-between"
                        :class="
                            table.winners.includes(seat.position) &&
                            'text-gold-300'
                        "
                    >
                        <span>{{ seat.name }}</span>
                        <span class="tabular-nums">{{ seat.score }}</span>
                    </li>
                </ol>
            </template>

            <template v-else>
                <h2 class="font-display text-4xl text-rose-300">
                    The game was abandoned
                </h2>
                <p class="mt-3 text-xl text-parchment-300 italic">
                    {{ table.abandonedBy ?? 'Someone' }} scattered the embers.
                    No one wins this one.
                </p>
                <Ornament class="my-5" />
            </template>

            <Link
                :href="lobby()"
                class="mt-7 inline-block rounded-md border border-ember-400/70 bg-gradient-to-b from-ember-400 to-ember-600 px-5 py-2 font-display text-lg text-forest-950 transition hover:from-ember-300 hover:to-ember-400"
            >
                Back to the clearing
            </Link>
        </div>
    </div>
</template>
