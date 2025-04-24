<template>
  <div>
    <AppInput :invalid="inputState" v-model="moneyValue" type="number" @send="handleSend" min="1" />
  </div>
</template>

<script setup lang="ts">
import AppInput from "@app/components/AppInput.vue";
import {WishItemForm, WishItemFormType} from "@app/types";
import {ref} from "vue";

const moneyValue = ref<number>(0)
const inputState = ref<boolean>(false)

const emits = defineEmits<{
  (e: 'submit', data: WishItemForm): void
}>()

function handleSend(): void {
  if (moneyValue.value <= 0) {
    inputState.value = true;
    return
  }
  emits('submit', { type: WishItemFormType.MONEY, data: { value: moneyValue.value }})
}
</script>