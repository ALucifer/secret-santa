import './bootstrap.js';
import './styles/app.css';
import './styles/application.scss'
import { createPinia } from "pinia";

import.meta.glob(['./images/**']);

const pinia = createPinia()

document.addEventListener('vue:before-mount', (e) => {
    const event = e as CustomEvent<{
        componentName: string;
        component: any;
        props: any;
        app: any;
    }>;

    const {
        app, // The Vue application instance
    } = event.detail;

    const modules = import.meta.glob('./vue/components/global/**/*.vue', { eager: true })

    for (const path in modules) {
        const component = (modules[path] as any).default
        const baseName = path.split('/').pop()?.replace(/\.\w+$/, '')

        if (baseName && component) {
            app.component(baseName, component)
        }
    }

    app.use(pinia)
})