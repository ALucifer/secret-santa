import { defineStore } from "pinia";
import { ref } from "vue";

interface Modal {
    name: string,
    open: boolean,
}

export const useModalStore = defineStore(
    'modal',
    () => {
        const modals = ref<Modal[]>([])

        function open(name: string) {
            console.log(modals.value)
            modals.value.push({ name: name, open: true })
        }

        function isOpen(name: string) {
            return modals.value.find(m => m.name === name)?.open ?? false
        }

        function close(name: string) {
            const index = modals.value.findIndex(m => m.name === name)

            if (index !== -1) {
                modals.value.splice(index, 1)
            }
        }

        return {
            open,
            isOpen,
            close,
        }
    }
)