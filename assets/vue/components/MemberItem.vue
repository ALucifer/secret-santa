<template>
  <CardItem>
    {{ member.email }}
  </CardItem>
</template>

<script setup lang="ts">
import CardItem from "@app/components/CardItem.vue";

defineProps<{ member: { id: number, email: string } }>()
</script>