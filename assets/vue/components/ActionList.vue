<template>
  <div class="flex gap-2 p-5 w-full">
    <ActionItem label="Lien du cadeau" @click="currentAction = 'gift'">
      <template #icon>
        <GiftIcon />
      </template>
    </ActionItem>
    <ActionItem label="Argent" @click="currentAction = 'money'">
      <template #icon>
        <MoneyIcon />
      </template>
    </ActionItem>
    <ActionItem label="Evenement / Sortie" @click="currentAction = 'event'">
      <template #icon>
        <CalendarIcon />
      </template>
    </ActionItem>
  </div>
</template>

<script setup lang="ts">
import CalendarIcon from "@app/icons/CalendarIcon.vue";
import ActionItem from "@app/components/ActionItem.vue";
import GiftIcon from "@app/icons/GiftIcon.vue";
import MoneyIcon from "@app/icons/MoneyIcon.vue";

const currentAction = defineModel()
</script>