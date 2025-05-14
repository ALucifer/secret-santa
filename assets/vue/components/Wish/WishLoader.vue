<template>
  <WishContainer :content-container-css-classes="containerClass" :useAction="false">
    <template #icon>
      <Spinner />
    </template>
    <template #content>
      <template v-if="type === 'GIFT'">
        <div class="flex items-center w-full">
          <div class="h-2.5 bg-neutral-400 rounded-full dark:bg-neutral-600 w-32"></div>
          <div class="h-2.5 ms-2 bg-neutral-500 rounded-full dark:bg-neutral-500 w-24"></div>
          <div class="h-2.5 ms-2 bg-neutral-500 rounded-full dark:bg-neutral-500 w-full"></div>
        </div>
        <div class="flex justify-end">
          <div class="h-2.5 bg-neutral-400 rounded-full dark:bg-neutral-600 w-8"></div>
        </div>
      </template>
      <template v-else-if="type === 'MONEY'">
        <p>De l'argent :</p>
        <div class="flex-1 flex items-center justify-center font-semibold font-stretch-expanded text-3xl">
          <div class="h-2.5 bg-neutral-400 rounded-full dark:bg-neutral-600 w-8"></div>
        </div>
      </template>
      <template v-else-if="type ==='EVENT'">
        <div class="flex items-center w-full">
          <div class="h-2.5 bg-neutral-400 rounded-full dark:bg-neutral-600 w-32"></div>
        </div>
        <p class="flex-1 flex">
          le :<span class="flex-1 flex items-center justify-center"><p class="h-2.5 bg-neutral-400 rounded-full dark:bg-neutral-600 w-32"></p></span>
        </p>
      </template>
    </template>
  </WishContainer>
</template>

<script setup lang="ts">
import WishContainer from "@app/components/Wish/WishContainer.vue";
import Spinner from "@app/icons/Spinner.vue";
import {TaskResponse, WishType} from "@app/types";
import { onMounted, ref, watch } from "vue";
import { useFetch } from "@app/composables/useFetch";
import Routing from "fos-router";
import {useTaskStore} from "@app/stores/task";

const props = defineProps<{ type: WishType, taskId: number }>()

const emits = defineEmits<{
  (e: 'loaded', data: TaskResponse),
}>()

const containerClass = ref(
    props.type == WishType.GIFT
        ? 'flex-1 flex flex-col justify-between overflow-hidden animate-pulse'
        : 'flex-1 flex flex-col justify-between max-w-sm animate-pulse'
)

const intervalNeeded = ref<boolean>(true)
const dataFromInterval = ref<TaskResponse>()

const { remove } = useTaskStore()

const retryCount = ref<number>(0)

onMounted(() => {
  const intervalId = setInterval(
    async () => {
      const response = await useFetch<TaskResponse>(
          Routing.generate('task', { id: props.taskId })
      )

      if (!response.data.value) {
        return
      }

      if (response.data.value.state !== 'PENDING') {
        intervalNeeded.value = false
        dataFromInterval.value = response.data.value
      }

      if (retryCount.value > 10) {
        intervalNeeded.value = false
      }

      retryCount.value++
    }, 5000
  )

  watch(intervalNeeded, () => {
    clearInterval(intervalId)
    remove(props.taskId)
    emits('loaded', dataFromInterval.value)
  })
})
</script>