<template>
  <a :href="link">
    <slot></slot>
  </a>
</template>

<script setup lang="ts">
import Routing from 'fos-router'

const props = defineProps<{ routeName: string, parameters?: any }>()

const link = Routing.generate(props.routeName, props.parameters, true)
console.log(link)
</script>