import '../css/app.css';
import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
	title: (title) => (title ? `${title} - ${appName}` : appName),
	resolve: (name) =>
		resolvePageComponent(
			`./Pages/${name}.vue`,
			import.meta.glob('./Pages/**/*.vue')
		),
	setup({ el, App, props, plugin }) {
		const pinia = createPinia();
		pinia.use(piniaPluginPersistedstate);

		createApp({ render: () => h(App, props) })
			.use(plugin)
			.use(ZiggyVue, window.Ziggy)
			.use(pinia)
			.mount(el);
	},
	progress: {
		color: '#4B5563',
	},
});
