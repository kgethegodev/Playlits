import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true })
        const page = pages[`./pages/${name}.vue`]

        if (!page) {
            throw new Error(`Page not found: ./Pages/${name}.vue`)
        }

        return page.default
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
