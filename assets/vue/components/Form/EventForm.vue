<template>
  <div>
    <div class="flex flex-col gap-2 w-[300px]">
      <AppInput
        type="text"
        placeholder="Entrez le nom de votre evement"
        :invalid="false"
        :modelValue="form.data.label"
        :showActions="false"
        @update:modelValue="$event => (form.data.label = $event)"
      />
      <AppInput
        type="date"
        :invalid="false"
        :modelValue="form.data.date"
        :showActions="false"
        @update:modelValue="$event => (form.data.date = $event)"
      />
    </div>
    <button
      @click="$emit('submit', { ...form })"
      class="size-[100px] text-center vertical-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-xs bg-teal-600 hover:bg-teal-500 sm:ml-3 cursor-pointer"
    >Ajouter</button>
  </div>
</template>

<script setup lang="ts">
import { reactive } from "vue";
import AppInput from "@app/components/AppInput.vue";

const form = reactive({
  type: 'event',
  data: {
    label: '',
    date: new Date(),
  },
})
</script>