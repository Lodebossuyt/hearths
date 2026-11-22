<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import BrandMark from '@/components/BrandMark.vue';
import Fireflies from '@/components/Fireflies.vue';
import ToastStack from '@/components/ToastStack.vue';
import { lobby, logout } from '@/routes';

const page = usePage();
</script>

<template>
    <div class="relative flex min-h-screen min-w-[1024px] flex-col">
        <Fireflies />
        <ToastStack />

        <header
            class="relative z-10 flex items-center justify-between border-b border-gold-400/15 bg-forest-950/60 px-8 py-3 backdrop-blur-sm"
        >
            <Link
                :href="lobby()"
                class="flex items-center gap-2 font-display text-3xl text-parchment-50 transition hover:text-ember-300"
            >
                <BrandMark />
                <span>Hearths</span>
            </Link>

            <div v-if="page.props.auth.user" class="flex items-center gap-5">
                <span class="text-lg text-lichen-200 italic">
                    {{ page.props.auth.user.name }}
                </span>
                <Link
                    :href="logout()"
                    as="button"
                    class="font-display text-lg text-parchment-300 underline-offset-4 transition hover:text-ember-300 hover:underline"
                >
                    Wander off
                </Link>
            </div>
        </header>

        <main class="relative z-10 flex flex-1 flex-col">
            <slot />
        </main>
    </div>
</template>
