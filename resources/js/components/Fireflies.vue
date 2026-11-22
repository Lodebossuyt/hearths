<script setup lang="ts">
/**
 * Slowly drifting fireflies. Positions are derived from the index so server and
 * client render the same markup.
 */
const fireflies = Array.from({ length: 18 }, (_, index) => ({
    left: (index * 37 + 11) % 100,
    top: (index * 53 + 23) % 100,
    delay: (index * 1.7) % 14,
    duration: 11 + ((index * 3) % 9),
    size: 2 + (index % 3),
}));
</script>

<template>
    <div
        class="pointer-events-none fixed inset-0 z-0 overflow-hidden"
        aria-hidden="true"
    >
        <span
            v-for="(firefly, index) in fireflies"
            :key="index"
            class="absolute animate-drift rounded-full bg-ember-300 shadow-[0_0_8px_3px_rgb(247_194_124/0.55)]"
            :style="{
                left: `${firefly.left}%`,
                top: `${firefly.top}%`,
                width: `${firefly.size}px`,
                height: `${firefly.size}px`,
                animationDelay: `${firefly.delay}s`,
                animationDuration: `${firefly.duration}s`,
            }"
        />
    </div>
</template>
