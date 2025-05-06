<template>
  <div>
    <h2>Ma liste d'envie</h2>
    <ul class="flex flex-col gap-2">
      <li v-for="item in items" :key="item.id">
        <component
          :is="WishEvent"
          v-if="item.type === Type.EVENT"
          v-bind="item.data"
        />
        <component
          :is="WishGift"
          v-else-if="item.type === Type.GIFT"
          v-bind="item.data"
        />
        <component
          :is="WishMoney"
          v-else
          v-bind="item.data"
        />
      </li>
    </ul>
  </div>
</template>
<script setup lang="ts">
import WishGift from "@app/components/WishGift.vue";
import WishMoney from "@app/components/WishMoney.vue";
import WishEvent from "@app/components/WishEvent.vue";

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

defineProps<{ items: Item[] }>()
</script>