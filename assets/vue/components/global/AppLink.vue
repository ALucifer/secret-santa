<template>
  <a :href="link">
    <slot></slot>
  </a>
</template>

<script setup lang="ts">
const props = defineProps<{ routeName: string, parameters?: any }>()
const link = (window as any).Routing.generate(props.routeName, props.parameters ?? {}, true)
</script>