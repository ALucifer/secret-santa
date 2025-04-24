<template>
  <a :href="link">
    <slot></slot>
  </a>
</template>

<script setup lang="ts">
import Routing from 'fos-router'
import { ref } from "vue";

const props = defineProps<{ routeName: string, parameters: any }>()

const link = ref(Routing.generate(props.routeName, props.parameters))
</script>