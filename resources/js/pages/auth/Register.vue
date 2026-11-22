<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ForestButton from '@/components/ForestButton.vue';
import TextField from '@/components/TextField.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
</script>

<template>
    <AuthLayout
        title="Join the circle"
        subtitle="Choose the name the other wanderers will know you by."
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-error="['password', 'password_confirmation']"
            class="flex flex-col gap-5"
            #default="{ errors, processing }"
        >
            <TextField
                label="Display name"
                name="name"
                autocomplete="nickname"
                autofocus
                :error="errors.name"
            />
            <TextField
                label="Email"
                name="email"
                type="email"
                autocomplete="email"
                :error="errors.email"
            />
            <TextField
                label="Password"
                name="password"
                type="password"
                autocomplete="new-password"
                :error="errors.password"
            />
            <TextField
                label="Confirm password"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                :error="errors.password_confirmation"
            />

            <ForestButton type="submit" :disabled="processing" class="mt-2">
                Register
            </ForestButton>
        </Form>

        <p class="mt-6 text-center text-lg text-parchment-300">
            Already have a seat?
            <Link
                :href="login()"
                class="text-ember-300 underline-offset-4 hover:underline"
            >
                Log in
            </Link>
        </p>
    </AuthLayout>
</template>
