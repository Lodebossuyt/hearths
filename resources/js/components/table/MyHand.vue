<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import ForestButton from '@/components/ForestButton.vue';
import PlayingCard from '@/components/PlayingCard.vue';
import { pass, play } from '@/routes/rooms';
import type { RoundView, SeatView } from '@/types';

const props = defineProps<{
    roomCode: string;
    round: RoundView;
    seats: SeatView[];
    myPosition: number;
    isGameActive: boolean;
}>();

const CARDS_TO_PASS = 3;
const selected = ref<string[]>([]);
const isSending = ref(false);

watch(
    () => props.round.number,
    () => (selected.value = []),
);

const isPassing = computed(
    () =>
        props.isGameActive &&
        props.round.status === 'passing' &&
        !props.round.hasPassed,
);

const isMyTurn = computed(
    () =>
        props.isGameActive &&
        props.round.status === 'playing' &&
        props.round.turnPosition === props.myPosition,
);

function nameAt(position: number | null): string {
    return props.seats.find((seat) => seat.position === position)?.name ?? '';
}

const passPhrases = {
    left: 'to your left',
    right: 'to your right',
    across: 'across the fire',
    none: '',
};

const statusMessage = computed(() => {
    if (!props.isGameActive) {
        return '';
    }

    if (props.round.status === 'passing') {
        if (!props.round.hasPassed) {
            return `Choose three cards to pass ${passPhrases[props.round.passDirection]}, to ${nameAt(props.round.passRecipientPosition)}.`;
        }

        const waitingOn = props.seats
            .filter((seat) => !seat.hasPassed)
            .map((seat) => seat.name);

        return `Your cards are on their way. Waiting for ${waitingOn.join(', ')}…`;
    }

    if (isMyTurn.value) {
        return 'Your turn. Play one of the glowing cards.';
    }

    return `Waiting for ${nameAt(props.round.turnPosition)}…`;
});

/**
 * Client-side check first: only cards the server marked playable can be chosen.
 * The server validates again, so a bypassed client simply gets a rejection toast.
 */
function chooseCard(code: string): void {
    if (isPassing.value) {
        selected.value = selected.value.includes(code)
            ? selected.value.filter((card) => card !== code)
            : selected.value.length < CARDS_TO_PASS
              ? [...selected.value, code]
              : selected.value;

        return;
    }

    const card = props.round.hand.find((handCard) => handCard.code === code);

    if (!isMyTurn.value || !card?.playable || isSending.value) {
        return;
    }

    router.post(
        play.url(props.roomCode),
        { card: code },
        {
            preserveScroll: true,
            onStart: () => (isSending.value = true),
            onFinish: () => (isSending.value = false),
        },
    );
}

function submitPass(): void {
    if (selected.value.length !== CARDS_TO_PASS) {
        return;
    }

    router.post(
        pass.url(props.roomCode),
        { cards: selected.value },
        {
            preserveScroll: true,
            onStart: () => (isSending.value = true),
            onFinish: () => (isSending.value = false),
            onSuccess: () => (selected.value = []),
        },
    );
}

function isDimmed(code: string, playable: boolean): boolean {
    if (isPassing.value) {
        return (
            selected.value.length >= CARDS_TO_PASS &&
            !selected.value.includes(code)
        );
    }

    return isMyTurn.value && !playable;
}
</script>

<template>
    <div class="flex flex-col items-center gap-4">
        <div class="flex min-h-12 items-center gap-4">
            <p
                class="text-xl italic"
                :class="
                    isMyTurn ? 'text-ember-300 text-glow' : 'text-lichen-200'
                "
            >
                {{ statusMessage }}
            </p>
            <ForestButton
                v-if="isPassing"
                :disabled="selected.length !== CARDS_TO_PASS || isSending"
                @click="submitPass"
            >
                Pass {{ selected.length }}/3
            </ForestButton>
        </div>

        <div class="flex items-end pt-6 pl-14">
            <PlayingCard
                v-for="card in round.hand"
                :key="card.code"
                :code="card.code"
                size="lg"
                class="-ml-14"
                interactive
                :playable="isMyTurn && card.playable"
                :dimmed="isDimmed(card.code, card.playable)"
                :selected="selected.includes(card.code)"
                :received="card.received"
                @choose="chooseCard"
            />
        </div>
    </div>
</template>
