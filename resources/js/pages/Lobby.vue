<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ForestButton from '@/components/ForestButton.vue';
import Ornament from '@/components/Ornament.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { show, store } from '@/routes/rooms';
import { store as joinRoom } from '@/routes/rooms/seat';
import type { LobbyRoom } from '@/types';

defineOptions({ layout: AppLayout });

defineProps<{
    rooms: LobbyRoom[];
    currentRoomCode: string | null;
}>();
</script>

<template>
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-8 py-12">
        <Head title="The clearing" />

        <div class="text-center">
            <h1 class="font-display text-5xl text-parchment-50">
                The clearing
            </h1>
            <p class="mt-2 text-xl text-lichen-200 italic">
                Hearths with a free seat, waiting for wanderers.
            </p>
            <Ornament class="mt-5" />
        </div>

        <div
            v-if="currentRoomCode"
            class="panel-torn flex items-center justify-between px-6 py-4"
        >
            <p class="text-xl">
                You are seated at hearth
                <span class="font-display text-ember-300">{{
                    currentRoomCode
                }}</span>
                .
            </p>
            <Link
                :href="show(currentRoomCode)"
                class="rounded-md border border-ember-400/70 bg-gradient-to-b from-ember-400 to-ember-600 px-4 py-2 font-display text-lg text-forest-950 shadow-[0_0_18px_rgb(240_160_75/0.3)] transition hover:from-ember-300 hover:to-ember-400"
            >
                Return to it
            </Link>
        </div>

        <div v-else class="flex justify-center">
            <Form v-bind="store.form()" #default="{ processing }">
                <ForestButton type="submit" :disabled="processing">
                    Gather round a new hearth
                </ForestButton>
            </Form>
        </div>

        <section class="flex flex-col gap-4">
            <p
                v-if="rooms.length === 0"
                class="py-10 text-center text-xl text-parchment-300 italic"
            >
                The clearing is quiet. Light a hearth and others will come.
            </p>

            <article
                v-for="room in rooms"
                :key="room.code"
                class="panel-torn flex items-center justify-between gap-6 px-6 py-5"
            >
                <div class="flex flex-col gap-1">
                    <h2 class="font-display text-2xl text-gold-300">
                        Hearth {{ room.code }}
                    </h2>
                    <p class="text-lg text-parchment-100">
                        {{ room.players.join(', ') }}
                    </p>
                    <p class="text-base text-lichen-300 italic">
                        {{ room.freeSeats }}
                        {{ room.freeSeats === 1 ? 'seat' : 'seats' }} free
                    </p>
                </div>

                <Form
                    v-if="!currentRoomCode"
                    v-bind="joinRoom.form(room.code)"
                    #default="{ processing }"
                >
                    <ForestButton
                        type="submit"
                        variant="moss"
                        :disabled="processing"
                    >
                        Take a seat
                    </ForestButton>
                </Form>
            </article>
        </section>
    </div>
</template>
