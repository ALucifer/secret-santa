<template>
  <div class="flex gap-2 p-5 bg-stone-200 w-[400px]">
    <div class="bg-neutral-50 size-[96px] flex items-center justify-center">
      <slot name="icon"></slot>
    </div>
    <div :class="contentContainerCssClasses">
      <slot name="content"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
const { contentContainerCssClasses = 'flex-1 flex flex-col' } = defineProps<{
  contentContainerCssClasses?: string
}>()
</script>