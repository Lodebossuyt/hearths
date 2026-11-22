<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ForestButton from '@/components/ForestButton.vue';
import { abandon } from '@/routes/rooms';
import type { TableView } from '@/types';

const props = defineProps<{ table: TableView }>();

const passLabels = {
    left: 'to the left',
    right: 'to the right',
    across: 'across the fire',
    none: 'no passing',
};

const isAbandoning = ref(false);

function abandonGame(): void {
    if (
        !window.confirm(
            'Abandon this game? It ends for everyone at the table, with no winner.',
        )
    ) {
        return;
    }

    router.post(
        abandon.url(props.table.code),
        {},
        {
            onStart: () => (isAbandoning.value = true),
            onFinish: () => (isAbandoning.value = false),
        },
    );
}

function nameAt(position: number): string {
    return (
        props.table.seats.find((seat) => seat.position === position)?.name ?? ''
    );
}
</script>

<template>
    <aside class="panel-torn flex w-80 shrink-0 flex-col gap-5 px-5 py-6">
        <div>
            <p class="text-sm tracking-[0.25em] text-lichen-300 uppercase">
                Hearth {{ table.code }}
            </p>
            <h2 class="font-display text-3xl text-parchment-50">
                <template v-if="table.round">
                    Round {{ table.round.number }}
                </template>
                <template v-else>Scores</template>
            </h2>
        </div>

        <dl v-if="table.round" class="grid grid-cols-2 gap-y-1 text-lg">
            <dt class="text-parchment-300">Pass</dt>
            <dd class="text-right">
                {{ passLabels[table.round.passDirection] }}
            </dd>
            <dt class="text-parchment-300">Hearts</dt>
            <dd
                class="text-right"
                :class="table.round.heartsBroken ? 'text-rose-300' : ''"
            >
                {{ table.round.heartsBroken ? 'broken ♥' : 'unbroken' }}
            </dd>
            <dt class="text-parchment-300">Your points</dt>
            <dd class="text-right font-display text-xl text-ember-300">
                {{ table.round.myPoints }}
            </dd>
        </dl>

        <table class="w-full text-base">
            <thead>
                <tr class="border-b border-gold-400/20 text-lichen-200">
                    <th class="py-1 text-left font-normal">#</th>
                    <th
                        v-for="seat in table.seats"
                        :key="seat.position"
                        class="max-w-14 truncate py-1 text-right font-display font-normal"
                        :class="seat.isMe && 'text-ember-300'"
                        :title="seat.name"
                    >
                        {{ seat.name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="entry in table.history"
                    :key="entry.number"
                    class="border-b border-gold-400/10"
                >
                    <td class="py-0.5 text-parchment-300">
                        {{ entry.number }}
                        <span
                            v-if="entry.moonShooter !== null"
                            :title="`${nameAt(entry.moonShooter)} shot the moon`"
                            >☾</span
                        >
                    </td>
                    <td
                        v-for="seat in table.seats"
                        :key="seat.position"
                        class="py-0.5 text-right tabular-nums"
                    >
                        {{ entry.scores[seat.position] }}
                    </td>
                </tr>
                <tr v-if="table.history.length === 0">
                    <td
                        :colspan="table.seats.length + 1"
                        class="py-2 text-center text-parchment-300/70 italic"
                    >
                        No rounds finished yet.
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="font-display text-xl">
                    <td class="pt-2 text-parchment-300">Σ</td>
                    <td
                        v-for="seat in table.seats"
                        :key="seat.position"
                        class="pt-2 text-right tabular-nums"
                        :class="
                            seat.isMe ? 'text-ember-300' : 'text-parchment-50'
                        "
                    >
                        {{ seat.score }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <p class="text-sm text-parchment-300/70 italic">
            The game ends once someone reaches 100. Lowest score wins.
        </p>

        <ForestButton
            v-if="table.status === 'playing'"
            variant="ghost"
            class="mt-auto w-full text-base"
            :disabled="isAbandoning"
            @click="abandonGame"
        >
            Abandon game
        </ForestButton>
    </aside>
</template>
