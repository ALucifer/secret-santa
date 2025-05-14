<template>
  <div class="flex gap-2 p-5 bg-stone-200 w-[400px] relative" @mouseover="showRemove = true" @mouseleave="showRemove = false">
    <div class="bg-neutral-50 size-[96px] flex items-center justify-center">
      <slot name="icon"></slot>
    </div>
    <div :class="contentContainerCssClasses">
      <slot name="content"></slot>
    </div>
    <XMarkIcon v-if="showRemove && useAction" class="absolute top-0 right-0 text-red-700 cursor-pointer" @click="$emit('remove')" />
  </div>
</template>

<script setup lang="ts">
import XMarkIcon from "@app/icons/XMarkIcon.vue";
import { ref } from "vue";

const { contentContainerCssClasses = 'flex-1 flex flex-col', useAction = true } = defineProps<{
  contentContainerCssClasses?: string,
  useAction?: boolean
}>()

defineEmits(['remove'])

const showRemove = ref<boolean>(false)
</script>