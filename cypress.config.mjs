import { defineConfig } from "cypress";
import viteConfig from './cypress/vite.config.mts'

export default defineConfig({
    component: {
        devServer: {
            framework: "vue",
            bundler: "vite",
            viteConfig, // <-- important
        },
        specPattern: "cypress/component/**/*.cy.{js,jsx,ts,tsx}",
        supportFile: "cypress/support/component.ts",
    },
});