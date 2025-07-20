<template>
  <div>
    <h3 class="application__title">{{ title }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 gap-4">
      <AppLink
        route-name="secret_santa_view"
        :parameters="{ id: item.id }"
        v-for="item in items"
        :key="item.id + item.label"
        class="secret-card"
      >
        <h4 class="card__title">{{ item.label }}</h4>
        <span class="flex gap-1" :class="{'text-green-500': item.state === 'started'}">
          <ArrowUpCircle v-if="item.state === 'started'" />
          <UserIcon v-else />
          {{ item.totalMembers }}
        </span>
      </AppLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import UserIcon from "@app/icons/UserIcon.vue";
import AppLink from "@app/components/global/AppLink.vue";
import ArrowUpCircle from "@app/icons/ArrowUpCircle.vue";

defineProps<{ title: string, items: array<{ id: number, label: string, totalMembers: number, state: string }> }>()
</script>