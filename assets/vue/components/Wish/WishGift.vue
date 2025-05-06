<template>
  <WishContainer content-container-css-classes="flex-1 flex flex-col justify-between">
    <template #icon>
      <GiftIcon />
    </template>
    <template #content>
      <p>{{ description }}</p>
      <div class="flex justify-end">
        <a :href="url" target="_blank">Lien</a>
      </div>
    </template>
  </WishContainer>
</template>

<script setup lang="ts">
import WishContainer from "@app/components/Wish/WishContainer.vue";
import GiftIcon from "@app/icons/GiftIcon.vue";

defineProps<{ description: string, url: string, image: string }>()
</script>