<template>
  <div>
    <AppInput type="url" :invalid="inputState" v-model="urlValue" placeholder="Lien de votre cadeau" @send="handleSend" />
  </div>
</template>

<script setup lang="ts">
import AppInput from "@app/components/global/form/AppInput.vue";
import { WishItemForm, WishItemType } from "@app/types";
import {ref} from "vue";

const emits = defineEmits<{
  (e: 'submit', data: WishItemForm): void
}>()

const urlValue = ref<string>('')
const inputState = ref<boolean>(false)

function handleSend(): void {
  try {
    const url = new URL(urlValue.value).toString()

    emits('submit', { type: WishItemType.GIFT, data: { url }})
    inputState.value = false
    urlValue.value = ''
  } catch {
    inputState.value = true
  }
}
</script>