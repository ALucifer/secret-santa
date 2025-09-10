import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';
import './styles/app.css';
import './styles/application.scss'
import { createPinia } from "pinia";

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

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

    const req = require.context('./vue/components/global', true, /\.vue$/)

    req.keys().forEach(key => {
        const mod = req(key);
        const component = mod.default || mod;
        // Nom du composant = nom de fichier sans extension
        const baseName = key.replace(/^.\//, '').split('/').pop();

        if (baseName) {
            app.component(baseName.replace(/\.\w+$/, ''), component);
        }
    })

    app.use(pinia)
})