<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

type Toast = { id: number; type: 'success' | 'error'; message: string };

const toasts = ref<Toast[]>([]);
let nextId = 0;

const stopListening = router.on('flash', (event) => {
    const toast = event.detail.flash.toast;

    if (!toast) {
        return;
    }

    const id = nextId++;
    toasts.value.push({ id, ...toast });
    setTimeout(() => {
        toasts.value = toasts.value.filter((item) => item.id !== id);
    }, 3500);
});

onBeforeUnmount(stopListening);
</script>

<template>
    <div
        class="pointer-events-none fixed top-20 left-1/2 z-50 flex -translate-x-1/2 flex-col items-center gap-2"
        aria-live="polite"
    >
        <TransitionGroup
            enter-from-class="opacity-0 -translate-y-2"
            leave-to-class="opacity-0"
            enter-active-class="transition duration-300"
            leave-active-class="transition duration-500"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="rounded-md border px-4 py-2 text-base shadow-lg backdrop-blur"
                :class="
                    toast.type === 'error'
                        ? 'border-rose-400/50 bg-berry-700/85 text-parchment-50'
                        : 'border-lichen-300/50 bg-moss-600/90 text-parchment-50'
                "
                role="status"
            >
                {{ toast.message }}
            </div>
        </TransitionGroup>
    </div>
</template>
