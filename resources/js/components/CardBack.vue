<script setup lang="ts">
withDefaults(defineProps<{ size?: 'xs' | 'sm' }>(), { size: 'sm' });
</script>

<template>
    <svg
        :class="size === 'xs' ? 'h-12 w-8' : 'h-[4.5rem] w-12'"
        viewBox="0 0 48 72"
        aria-hidden="true"
        class="shrink-0 drop-shadow-[0_3px_5px_rgb(0_0_0/0.5)]"
    >
        <rect
            x="0.5"
            y="0.5"
            width="47"
            height="71"
            rx="5"
            fill="#1a241b"
            stroke="#8a7036"
        />
        <rect
            x="4"
            y="4"
            width="40"
            height="64"
            rx="3"
            fill="none"
            stroke="#c3a25a"
            stroke-opacity="0.45"
            stroke-dasharray="2 2"
        />
        <!-- moth -->
        <g transform="translate(24 30)" fill="#c47f86" fill-opacity="0.75">
            <path d="M0 -2 C-9 -12 -15 -4 -11 3 C-8 7 -3 5 0 2 Z" />
            <path d="M0 -2 C9 -12 15 -4 11 3 C8 7 3 5 0 2 Z" />
            <path
                d="M0 2 C-5 5 -8 11 -4 12 C-2 12 -1 8 0 5 Z"
                fill-opacity="0.55"
            />
            <path d="M0 2 C5 5 8 11 4 12 C2 12 1 8 0 5 Z" fill-opacity="0.55" />
            <rect
                x="-0.8"
                y="-4"
                width="1.6"
                height="12"
                rx="0.8"
                fill="#ece2c8"
            />
        </g>
        <!-- ring of toadstools -->
        <g fill="#9b2f3f">
            <path d="M10 56 a4 3 0 0 1 8 0 Z" />
            <path d="M20 59 a4 3 0 0 1 8 0 Z" />
            <path d="M30 56 a4 3 0 0 1 8 0 Z" />
        </g>
        <g fill="#ece2c8">
            <rect x="13.2" y="56" width="1.6" height="4" />
            <rect x="23.2" y="59" width="1.6" height="4" />
            <rect x="33.2" y="56" width="1.6" height="4" />
            <circle cx="12.5" cy="54.6" r="0.7" />
            <circle cx="25" cy="57.4" r="0.7" />
            <circle cx="35.5" cy="54.6" r="0.7" />
        </g>
        <!-- fern sprigs -->
        <path
            d="M24 12 v8 M24 14 l-3 -2 M24 14 l3 -2 M24 17 l-3 -2 M24 17 l3 -2"
            stroke="#a7b78e"
            stroke-opacity="0.6"
            stroke-width="0.8"
            fill="none"
        />
    </svg>
</template>
