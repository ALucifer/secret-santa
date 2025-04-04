<template>
  <div>
    <AppInput :invalid="false" :modelValue="moneyValue" type="number" />
  </div>
</template>

<script setup lang="ts">
import AppInput from "@app/components/AppInput.vue";
import { ref } from "vue";

const moneyValue = ref(0)
</script>