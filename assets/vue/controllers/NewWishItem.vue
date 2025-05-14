<template>
  <div class="flex flex-col bg-stone-200 w-[600px] min-h-[150px] relative" v-if="total < 10">
    <ArrayLeftIcon
      v-if="currentAction"
      @click="currentAction = null"
      class="absolute top-[5px] left-[5px]"
    />
    <p
      v-if="formError"
      class="pt-2 text-center text-sm text-red-600 font-medium"
    >Une erreur est survenue lors de l'envoi du formulaire.</p>
    <ActionList
      v-if="!currentAction"
      v-model="currentAction"
      class="flex h-[150px] justify-center"
    />
    <div v-else class="flex flex-1 gap-2 items-center justify-center">
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
import {computed, onMounted, ref} from "vue";
import { useFetch } from "@app/composables/useFetch";
import Routing from 'fos-router'
import { useTaskStore } from "@app/stores/task";
import { useWishStore } from "@app/stores/wish";
import {storeToRefs} from "pinia";

const props = defineProps<{ memberId: number, wishCount: number }>()

const currentAction = ref()
const formError = ref<boolean>(false)

const componentForm = computed(
  () => {
    formError.value = false

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

const { add } = useTaskStore()

const { fill, increment } = useWishStore()
const { total } = storeToRefs(useWishStore())

onMounted(() => {
  fill(props.wishCount)
})

async function handleSubmit(event: Event) {
  const { data, error } = await useFetch(
      Routing.generate('newWish', { id: props.memberId }),
      {
        method: 'POST',
        body: JSON.stringify(event)
      }
  )
  currentAction.value = null

  if (error.value) {
    formError.value = true
    return
  }

  add(data.value)
  increment()
}
</script>