<script setup lang="ts">
import BrandMark from '@/components/BrandMark.vue';
import Fireflies from '@/components/Fireflies.vue';
import Ornament from '@/components/Ornament.vue';
import ToastStack from '@/components/ToastStack.vue';

defineProps<{ title: string; subtitle: string }>();
</script>

<template>
    <div
        class="relative flex min-h-screen min-w-[1024px] items-center justify-center px-6 py-12"
    >
        <Fireflies />
        <ToastStack />

        <div class="relative z-10 w-full max-w-md">
            <div class="mb-6 flex flex-col items-center gap-2 text-center">
                <div class="scale-150"><BrandMark /></div>
                <h1 class="mt-3 font-display text-5xl text-parchment-50">
                    Hearths
                </h1>
                <p class="text-lg text-lichen-200 italic">
                    Hearts, played by firelight in the mossy dark.
                </p>
            </div>

            <section class="panel-torn px-8 py-8">
                <h2
                    class="text-center font-display text-3xl text-ember-300 text-glow"
                >
                    {{ title }}
                </h2>
                <p class="mt-1 text-center text-parchment-300 italic">
                    {{ subtitle }}
                </p>
                <Ornament class="my-5" />
                <slot />
            </section>
        </div>
    </div>
</template>
