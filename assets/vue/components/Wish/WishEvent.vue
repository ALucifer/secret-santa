<template>
  <WishContainer @remove="$emit('remove')">
    <template #icon>
      <CalendarIcon />
    </template>
    <template #content>
      <p>{{ name }}</p>
      <p class="flex-1 flex">
        le :<span class="flex-1 flex items-center justify-center font-semibold font-stretch-expanded text-xl">{{ dateFormatted }}</span>
      </p>
    </template>
  </WishContainer>
</template>

<script setup lang="ts">
import CalendarIcon from "@app/icons/CalendarIcon.vue";
import WishContainer from "@app/components/Wish/WishContainer.vue";
import { useDateFormat } from "@vueuse/core";

const props = defineProps<{ name: string, date: string }>()
defineEmits(['remove'])

const dateFormatted = useDateFormat(props.date, 'DD MMM YYYY')
</script>