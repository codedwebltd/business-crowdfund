import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { useToast } from '@/composables/useToast';

const appName = import.meta.env.VITE_APP_NAME || 'CrowdPower';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin);

        // Handle flash messages globally
        app.mixin({
            mounted() {
                const { success, error, warning, info } = useToast();
                const flash = this.$page.props.flash;

                if (flash) {
                    if (flash.success) success(flash.success);
                    if (flash.error) error(flash.error);
                    if (flash.warning) warning(flash.warning);
                    if (flash.info) info(flash.info);
                }
            }
        });

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
