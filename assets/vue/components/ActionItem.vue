<template>
  <div class="flex flex-col flex-1 items-center justify-center gap-2 rounded p-2 bg-neutral-50 hover:bg-neutral-100 hover:cursor-pointer">
    <slot name="icon"></slot>
    {{ label }}
  </div>
</template>

<script setup lang="ts">
defineProps<{
  label: String,
}>()
</script>

<style scoped>
div {
  font-size: 12px;
}
</style>