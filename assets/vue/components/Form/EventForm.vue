<template>
  <div>
    <div class="flex flex-col gap-2 w-[300px]">
      <AppInput
        type="text"
        placeholder="Entrez le nom de votre evement"
        :invalid="stateForm.label"
        :showActions="false"
        v-model="form.label"
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
import {WishItemForm, WishItemFormType} from "@app/types";

const stateForm = ref({
  label: false,
  date: false,
})

const form = reactive({
    label: '',
    date: new Date(),
})

const emits = defineEmits<{
  (e: 'submit', data: WishItemForm): void
}>()

function handleSubmit(): void {
  if (form.label === '' && form.label.length < 3) {
    stateForm.value.label = true

    return
  }

  emits('submit', {
    type: WishItemFormType.EVENT,
    data: form
  })
}
</script>