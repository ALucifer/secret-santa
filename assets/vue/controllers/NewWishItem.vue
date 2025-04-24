<template>
  <div class="bg-stone-200 w-[600px] h-[150px] relative">
    <ArrayLeftIcon
      v-if="currentAction"
      @click="currentAction = null"
      class="absolute top-[5px] left-[5px]"
    />
    <ActionList
      v-if="!currentAction"
      v-model="currentAction"
      class="flex h-full justify-center"
    />
    <div v-else class="flex gap-2 items-center justify-center h-full">
      <component
        :is="componentForm"
        class="flex"
        @submit="handleSubmit($event)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import ActionList from "@app/components/ActionList.vue";
import EventForm from "@app/components/Form/EventForm.vue";
import MoneyForm from "@app/components/Form/MoneyForm.vue";
import GiftForm from "@app/components/Form/GiftForm.vue";
import ArrayLeftIcon from "@app/icons/ArrayLeftIcon.vue";
import { WishItemForm } from "@app/types";
import { computed, ref } from "vue";


const currentAction = ref()

const componentForm = computed(
  () => {
    switch (currentAction.value) {
      case "event":
        return EventForm
      case 'money':
        return MoneyForm
      case 'gift':
        return GiftForm
      default:
        return null
    }
  }
)

function handleSubmit(event: WishItemForm) {
  console.log(event)
  currentAction.value = null
}
</script>