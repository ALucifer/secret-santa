<template>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" :stroke :fill :class="cssClass">
    <path stroke-linecap="round" stroke-linejoin="round" d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
  </svg>
</template>

<script setup lang="ts">
const { cssClass = 'size-6', fill = 'none', stroke = 'currentColor' } = defineProps<{ cssClass?: string, fill?: string, stroke?: string }>()
</script>