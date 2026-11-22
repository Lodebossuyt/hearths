import { createInertiaApp } from '@inertiajs/vue3';
import { echo } from '@inertiajs/vue3/echo';
import { configureEcho } from '@laravel/echo-vue';

const appName = import.meta.env.VITE_APP_NAME || 'Hearths';

configureEcho({ broadcaster: 'reverb' });

void createInertiaApp({
    title: (title) => (title ? `${title} · ${appName}` : appName),
    live: echo(),
    withApp: (app) => {
        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
    },
    progress: {
        color: '#f0a04b',
    },
});
