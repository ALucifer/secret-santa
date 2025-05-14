<template>
  <div>
    <h2 class="text-3xl font-bold text-center text-gray-900 mb-6">Ma liste d'envie</h2>
    <div class="grid grid-cols-2 gap-4">
      <template v-for="item in wishItems" :key="item.id">
        <component
          :is="WishEvent"
          v-if="item.type === Type.EVENT"
          v-bind="item.data"
          @remove="handleRemove(item.id)"
        />
        <component
          :is="WishGift"
          v-else-if="item.type === Type.GIFT"
          v-bind="item.data"
          @remove="handleRemove(item.id)"
        />
        <component
          :is="WishMoney"
          v-else
          v-bind="item.data"
          @remove="handleRemove(item.id)"
        />
      </template>
      <template v-for="(item, key) in taskStore.items" :key="key">
        <WishLoader
          :type="item.data.type"
          :taskId="item.id"
          @loaded="addNewItem"
        />
      </template>
    </div>
  </div>
</template>
<script setup lang="ts">
import WishGift from "@app/components/Wish/WishGift.vue";
import WishMoney from "@app/components/Wish/WishMoney.vue";
import WishEvent from "@app/components/Wish/WishEvent.vue";
import { useTaskStore } from "@app/stores/task"
import WishLoader from "@app/components/Wish/WishLoader.vue";
import { ref } from "vue";
import { TaskResponse } from "@app/types";
import {useFetch} from "@app/composables/useFetch";
import Routing from "fos-router";
import {useWishStore} from "@app/stores/wish";

enum Type {
  MONEY = 'MONEY',
  GIFT = 'GIFT',
  EVENT = 'EVENT'
}

interface Money {
  price: number
}
interface Gift {
  url: string
}
interface Event {
  date: Date,
  name: string
}

interface Item {
  id: number,
  type: 'MONEY' | 'GIFT' | 'EVENT',
  data: Money | Gift | Event
}

const props = defineProps<{ items: Item[] }>()

const wishItems = ref<Item[]>(props.items)

const taskStore = useTaskStore()

function addNewItem(item: TaskResponse) {
  wishItems.value.push({ type: item.data.type, id: item.data.id, data: item.data.data })
}

const { decrease } = useWishStore()

async function handleRemove(itemId: number) {
  try {
    await useFetch(
        Routing.generate('delete_wish', { id: itemId }),
        {
          method: 'DELETE'
        }
    )
    wishItems.value = wishItems.value.filter((item: Item) => item.id !== itemId)
    taskStore.remove(itemId)
    decrease()
  } catch {
  }
}
</script>