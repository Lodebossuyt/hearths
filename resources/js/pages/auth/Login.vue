<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ForestButton from '@/components/ForestButton.vue';
import TextField from '@/components/TextField.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
</script>

<template>
    <AuthLayout
        title="Return to the fire"
        subtitle="Your seat by the hearth is still warm."
    >
        <Head title="Log in" />

        <Form
            v-bind="store.form()"
            :reset-on-error="['password']"
            class="flex flex-col gap-5"
            #default="{ errors, processing }"
        >
            <TextField
                label="Email"
                name="email"
                type="email"
                autocomplete="email"
                autofocus
                :error="errors.email"
            />
            <TextField
                label="Password"
                name="password"
                type="password"
                autocomplete="current-password"
                :error="errors.password"
            />

            <label class="flex items-center gap-2 text-lg text-parchment-300">
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    class="h-4 w-4 accent-ember-400"
                />
                Remember me
            </label>

            <ForestButton type="submit" :disabled="processing" class="mt-2">
                Log in
            </ForestButton>
        </Form>

        <p class="mt-6 text-center text-lg text-parchment-300">
            First time in these woods?
            <Link
                :href="register()"
                class="text-ember-300 underline-offset-4 hover:underline"
            >
                Register
            </Link>
        </p>
    </AuthLayout>
</template>
