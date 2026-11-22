<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ForestButton from '@/components/ForestButton.vue';
import Ornament from '@/components/Ornament.vue';
import { show } from '@/routes/rooms';
import { destroy as leaveRoom, store as joinRoom } from '@/routes/rooms/seat';
import type { TableView } from '@/types';

const props = defineProps<{
    table: TableView;
    isAway: (userId: number) => boolean;
}>();

const copied = ref(false);

const seats = computed(() =>
    [0, 1, 2, 3].map((position) =>
        props.table.seats.find((seat) => seat.position === position),
    ),
);

const isSeated = computed(() => props.table.myPosition !== null);

async function copyInviteLink(): Promise<void> {
    try {
        await navigator.clipboard.writeText(
            `${window.location.origin}${show.url(props.table.code)}`,
        );
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch {
        copied.value = false;
    }
}
</script>

<template>
    <div class="mx-auto flex w-full max-w-3xl flex-col gap-8 px-8 py-12">
        <div class="text-center">
            <p class="text-lg tracking-[0.3em] text-lichen-300 uppercase">
                Hearth
            </p>
            <h1 class="font-display text-6xl text-ember-300 text-glow">
                {{ table.code }}
            </h1>
            <p class="mt-3 text-xl text-lichen-200 italic">
                The fire is lit. The game begins when four have gathered.
            </p>
            <Ornament class="mt-5" />
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div
                v-for="(seat, position) in seats"
                :key="position"
                class="panel-torn flex items-center gap-4 px-5 py-5"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-9 w-9 shrink-0"
                    aria-hidden="true"
                >
                    <path
                        d="M3 13 a9 7 0 0 1 18 0 Z"
                        :fill="seat ? '#9b2f3f' : '#364a36'"
                    />
                    <rect
                        x="10"
                        y="13"
                        width="4"
                        height="8"
                        rx="1.5"
                        :fill="seat ? '#ece2c8' : '#56704f'"
                    />
                    <template v-if="seat">
                        <circle cx="8" cy="9.5" r="1.2" fill="#ece2c8" />
                        <circle cx="14.5" cy="8" r="1" fill="#ece2c8" />
                    </template>
                </svg>
                <div>
                    <p
                        class="font-display text-2xl"
                        :class="seat ? 'text-parchment-50' : 'text-moss-500'"
                    >
                        {{ seat ? seat.name : 'An empty toadstool' }}
                    </p>
                    <p
                        v-if="seat"
                        class="text-base italic"
                        :class="
                            isAway(seat.userId)
                                ? 'text-parchment-300/60'
                                : 'text-lichen-300'
                        "
                    >
                        {{
                            seat.isMe
                                ? 'You'
                                : isAway(seat.userId)
                                  ? 'Away'
                                  : 'By the fire'
                        }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center gap-4">
            <ForestButton variant="ghost" @click="copyInviteLink">
                {{ copied ? 'Link copied' : 'Copy invite link' }}
            </ForestButton>

            <Form
                v-if="!isSeated"
                v-bind="joinRoom.form(table.code)"
                #default="{ processing }"
            >
                <ForestButton type="submit" :disabled="processing">
                    Take a seat
                </ForestButton>
            </Form>

            <Form
                v-else
                v-bind="leaveRoom.form(table.code)"
                #default="{ processing }"
            >
                <ForestButton
                    type="submit"
                    variant="berry"
                    :disabled="processing"
                >
                    Leave this hearth
                </ForestButton>
            </Form>
        </div>
    </div>
</template>
