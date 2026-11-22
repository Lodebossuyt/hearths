<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import GameOver from '@/components/table/GameOver.vue';
import MyHand from '@/components/table/MyHand.vue';
import OpponentSeat from '@/components/table/OpponentSeat.vue';
import RoundSummary from '@/components/table/RoundSummary.vue';
import ScoreBoard from '@/components/table/ScoreBoard.vue';
import TrickArea from '@/components/table/TrickArea.vue';
import WaitingRoom from '@/components/table/WaitingRoom.vue';
import { useTablePresence } from '@/composables/useTablePresence';
import AppLayout from '@/layouts/AppLayout.vue';
import { sideOf } from '@/lib/cards';
import type { TableView } from '@/types';

defineOptions({ layout: AppLayout });

const props = defineProps<{ table: TableView }>();

const { isAway } = useTablePresence(
    props.table.code,
    props.table.myPosition !== null,
);

const myPosition = computed(() => props.table.myPosition ?? 0);
const isGameActive = computed(() => props.table.status === 'playing');
const isGameOver = computed(
    () =>
        props.table.status === 'finished' || props.table.status === 'abandoned',
);

function opponentOn(side: 'left' | 'top' | 'right') {
    return props.table.seats.find(
        (seat) => sideOf(seat.position, myPosition.value) === side,
    );
}

/*
 * The Round summary is shown once per Round; which ones were dismissed is a
 * per-viewer convenience kept in localStorage.
 */
const storageKey = `hearths:${props.table.code}:dismissed-round`;
const dismissedRound = ref(Number.POSITIVE_INFINITY);

onMounted(() => {
    try {
        dismissedRound.value = Number(localStorage.getItem(storageKey) ?? 0);
    } catch {
        dismissedRound.value = 0;
    }
});

const latestRound = computed(() => props.table.history.at(-1) ?? null);

const summaryToShow = computed(() =>
    latestRound.value &&
    latestRound.value.number > dismissedRound.value &&
    !isGameOver.value
        ? latestRound.value
        : null,
);

function dismissSummary(): void {
    dismissedRound.value = latestRound.value?.number ?? 0;

    try {
        localStorage.setItem(storageKey, String(dismissedRound.value));
    } catch {
        // Storage may be unavailable; the summary simply shows again on reload.
    }
}
</script>

<template>
    <div class="flex flex-1 flex-col">
        <Head :title="`Hearth ${table.code}`" />

        <WaitingRoom
            v-if="table.status === 'waiting'"
            :table="table"
            :is-away="isAway"
        />

        <div v-else class="flex flex-1 gap-6 px-6 py-6">
            <section
                class="grid flex-1 grid-cols-[1fr_auto_1fr] grid-rows-[auto_1fr_auto] items-center gap-4"
            >
                <div class="col-start-2 row-start-1 flex justify-center">
                    <OpponentSeat
                        v-if="opponentOn('top')"
                        :seat="opponentOn('top')!"
                        :away="isAway(opponentOn('top')!.userId)"
                        :show-passed="table.round?.status === 'passing'"
                    />
                </div>

                <div class="col-start-1 row-start-2 flex justify-end">
                    <OpponentSeat
                        v-if="opponentOn('left')"
                        :seat="opponentOn('left')!"
                        :away="isAway(opponentOn('left')!.userId)"
                        :show-passed="table.round?.status === 'passing'"
                        vertical
                    />
                </div>

                <div class="col-start-2 row-start-2">
                    <TrickArea
                        :trick="table.round?.trick ?? null"
                        :my-position="myPosition"
                        :seats="table.seats"
                    />
                </div>

                <div class="col-start-3 row-start-2 flex justify-start">
                    <OpponentSeat
                        v-if="opponentOn('right')"
                        :seat="opponentOn('right')!"
                        :away="isAway(opponentOn('right')!.userId)"
                        :show-passed="table.round?.status === 'passing'"
                        vertical
                    />
                </div>

                <div class="col-span-3 row-start-3 pb-2">
                    <MyHand
                        v-if="table.round"
                        :room-code="table.code"
                        :round="table.round"
                        :seats="table.seats"
                        :my-position="myPosition"
                        :is-game-active="isGameActive"
                    />
                </div>
            </section>

            <ScoreBoard :table="table" />
        </div>

        <RoundSummary
            v-if="summaryToShow"
            :entry="summaryToShow"
            :seats="table.seats"
            @dismiss="dismissSummary"
        />

        <GameOver v-if="isGameOver" :table="table" />
    </div>
</template>
