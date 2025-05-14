import { defineStore } from "pinia";
import {ref} from "vue";

export const useWishStore = defineStore(
    'wish',
    () => {
        const total = ref(0)

        function increment() {
            total.value++
        }

        function decrease() {
            total.value--
        }

        function fill(count: number) {
            total.value = count
        }

        return {
            total,
            increment,
            decrease,
            fill
        }
    }
)