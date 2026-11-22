<script setup lang="ts">
defineProps<{
    label: string;
    name: string;
    type?: string;
    error?: string;
    autocomplete?: string;
    autofocus?: boolean;
}>();
</script>

<template>
    <label class="flex flex-col gap-1.5">
        <span class="font-display text-lg text-lichen-200">{{ label }}</span>
        <input
            v-focus="autofocus === true"
            :type="type ?? 'text'"
            :name="name"
            :autocomplete="autocomplete"
            required
            class="rounded-md border border-bark-500/60 bg-forest-950/70 px-3 py-2 text-lg text-parchment-50 shadow-inner placeholder:text-parchment-300/40 focus:border-gold-400 focus:ring-1 focus:ring-gold-400 focus:outline-none"
        />
        <span v-if="error" class="text-base text-rose-300">{{ error }}</span>
    </label>
</template>
