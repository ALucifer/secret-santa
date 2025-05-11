import {defineStore} from "pinia";
import {ref} from "vue";
import {WishType} from "@app/types";

interface item {
    id: number,
    state: 'PENDING' | 'SUCCESS' | 'ERROR',
    data: {
        type: WishType
    }
}

export const useTaskStore = defineStore(
    'task',
    () => {
        const items = ref<item[]>([])

        function add(item: item) {
            items.value.push(item)
        }

        function remove(itemId: number) {
            items.value = items.value.filter((i) => i.id !== itemId)
        }

        return { add, items, remove }
    }
)