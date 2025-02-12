import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Import Quasar
import { Quasar, Notify } from 'quasar'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/dist/quasar.css'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Quasar, {
                plugins: {
                    Notify
                },
                config: {
                    brand: {
                        primary: '#4F46E5',
                        secondary: '#9333EA',
                        accent: '#3B82F6',
                        dark: '#1E293B',
                        positive: '#22C55E',
                        negative: '#EF4444',
                        info: '#3B82F6',
                        warning: '#F59E0B'
                    },
                    notify: {
                        position: 'top-right',
                        timeout: 2500
                    }
                }
            })
            .mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});
