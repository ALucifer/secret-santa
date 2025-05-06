<template>
  <WishContainer>
    <template #icon><MoneyIcon /></template>
    <template #content>
      <p>De l'argent :</p>
      <p class="flex-1 flex items-center justify-center font-semibold font-stretch-expanded text-3xl">{{ price }} €</p>
    </template>
  </WishContainer>
</template>
<script setup lang="ts">
import WishContainer from "@app/components/Wish/WishContainer.vue";
import MoneyIcon from "@app/icons/MoneyIcon.vue";

defineProps<{ price: number }>()
</script>