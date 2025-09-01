<template>
  <div>
    <h2 class="text-3xl font-bold text-center text-gray-900 mb-6">Ma liste d'envie</h2>
    <div class="grid grid-cols-2 gap-4">
      <template v-for="item in wishItems" :key="item.id">
        <component
          :is="WishEvent"
          v-if="item.type === WishItemType.EVENT"
          v-bind="item.data"
          @remove="handleRemove(item.id)"
        />
        <component
          :is="WishGift"
          v-else-if="item.type === WishItemType.GIFT"
          v-bind="item.data"
          @remove="handleRemove(item.id)"
        />
        <component
          :is="WishMoney"
          v-else-if="item.type === WishItemType.MONEY"
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
import { ref } from "vue";
import Routing from "fos-router";
import WishGift from "@app/components/Wish/WishGift.vue";
import WishMoney from "@app/components/Wish/WishMoney.vue";
import WishEvent from "@app/components/Wish/WishEvent.vue";
import WishLoader from "@app/components/Wish/WishLoader.vue";
import { useTaskStore } from "@app/stores/task"
import { useWishStore } from "@app/stores/wish";
import { TaskResponse, Item, WishItem, WishItemType } from "@app/types";
import { Options, useFetch } from "@app/composables/useFetch";

const props = defineProps<{ items: WishItem[] }>()

const wishItems = ref<WishItem[]>(props.items)

const taskStore = useTaskStore()

function addNewItem(item: TaskResponse) {
  wishItems.value.push({ type: item.data.type, id: item.data.id, data: item.data.data })
}

const { decrease } = useWishStore()

async function handleRemove(itemId: number) {
  try {
    await useFetch(
      Routing.generate('delete_wish', { id: itemId }),
      { method: 'DELETE' } as Options
    )
    wishItems.value = wishItems.value.filter((item: WishItem) => item.id !== itemId)
    taskStore.remove(itemId)
    decrease()
  } catch {
  }
}
</script>