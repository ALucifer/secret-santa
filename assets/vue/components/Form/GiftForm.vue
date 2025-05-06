<template>
  <div>
    <AppInput type="url" :invalid="inputState" v-model="urlValue" placeholder="Lien de votre cadeau" @send="handleSend" />
  </div>
</template>

<script setup lang="ts">
import AppInput from "@app/components/AppInput.vue";
import {WishItemForm, WishType} from "@app/types";
import {ref} from "vue";

const emits = defineEmits<{
  (e: 'submit', data: WishItemForm): void
}>()

const urlValue = ref<string>('')
const inputState = ref<boolean>(false)

function handleSend(): void {
  try {
    const url = new URL(urlValue.value)

    emits('submit', { type: WishType.GIFT, data: { url }})
    inputState.value = false
    urlValue.value = ''
  } catch {
    inputState.value = true
  }
}
</script>