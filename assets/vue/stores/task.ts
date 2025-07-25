import {defineStore} from "pinia";
import {ref} from "vue";
import { Item } from "@app/types";


export const useTaskStore = defineStore(
    'task',
    () => {
        const items = ref<Item[]>([])

        function add(item: Item) {
            items.value.push(item)
        }

        function remove(itemId: number) {
            items.value = items.value.filter((i) => i.id !== itemId)
        }

        return { add, items, remove }
    }
)