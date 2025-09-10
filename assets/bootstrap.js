import { startStimulusApp } from "vite-plugin-symfony/stimulus/helpers"
import { registerVueControllerComponents } from "vite-plugin-symfony/stimulus/helpers/vue"

// register Vue components before startStimulusApp
registerVueControllerComponents(import.meta.glob('./vue/controllers/**/*.vue'))
startStimulusApp();
