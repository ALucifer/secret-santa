<template>
  <div
    :class="cssClasses"
    class="flex items-center border-2 rounded-[5px] overflow-hidden max-h-[40px]">
    <input
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :type="type"
      v-bind="$attrs"
      class="text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
    >
    <slot v-if="showActions" name="action">
      <button @click="$emit('send')">Ok</button>
    </slot>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

const { type = 'text', invalid, modelValue, showActions = true } = defineProps<{
  type?: String,
  invalid: boolean,
  modelValue: any,
  showActions?: boolean,
}>()
defineEmits(['send', 'update:modelValue'])

const cssClasses = computed(() => invalid ? 'border-1 border-red-300 bg-red-50 text-red-900': 'border border-gray-300 bg-gray-50 text-gray-900')
</script>

<style scoped>
input {
  border: none;
  outline: none;
  padding: 8px;
  flex: 1;
}

button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 8px 12px;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}
</style>