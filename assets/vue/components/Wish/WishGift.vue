<template>
  <WishContainer content-container-css-classes="flex-1 flex flex-col justify-between overflow-hidden">
    <template #icon>
      <img v-if="image" :src="image">
      <GiftIcon v-else />
    </template>
    <template #content>
      <p class="truncate">{{ title }}</p>
      <div class="flex justify-end">
        <a :href="url" target="_blank">Lien</a>
      </div>
    </template>
  </WishContainer>
</template>

<script setup lang="ts">
import WishContainer from "@app/components/Wish/WishContainer.vue";
import GiftIcon from "@app/icons/GiftIcon.vue";

defineProps<{ title: string, url: string, image?: string }>()
</script>