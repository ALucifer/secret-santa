<template>
  <div>
    <AppInput :invalid="false" :modelValue="urlValue" placeholder="Lien de votre cadeau" />
  </div>
</template>

<script setup lang="ts">
import AppInput from "@app/components/AppInput.vue";
import { ref } from "vue";

const urlValue = ref<string>('')
</script>