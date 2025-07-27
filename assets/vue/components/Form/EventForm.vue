<template>
  <div>
    <div class="flex flex-col gap-2 w-[300px]">
      <AppInput
        type="text"
        placeholder="Entrez le nom de votre evement"
        :invalid="stateForm.name"
        :showActions="false"
        v-model="form.name"
      />
      <AppInput
        type="date"
        :invalid="stateForm.date"
        :showActions="false"
        v-model="form.date"
      />
    </div>
    <button
      @click="handleSubmit"
      class="size-[100px] text-center vertical-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-xs bg-teal-600 hover:bg-teal-500 sm:ml-3 cursor-pointer"
    >Ajouter</button>
  </div>
</template>

<script setup lang="ts">
import {reactive, ref} from "vue";
import AppInput from "@app/components/AppInput.vue";
import { WishItemForm, WishItemType } from "@app/types";

const stateForm = ref({
  name: false,
  date: false,
})

const form = reactive({
    name: '',
    date: new Date(),
})

const emits = defineEmits<{
  (e: 'submit', data: WishItemForm): void
}>()

function handleSubmit(): void {
  if (form.name === '' && form.name.length < 3) {
    stateForm.value.name = true

    return
  }

  emits('submit', {
    type: WishItemType.EVENT,
    data: { ...form }
  })
}
</script>