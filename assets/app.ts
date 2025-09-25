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

    app.use(pinia)
    }
)