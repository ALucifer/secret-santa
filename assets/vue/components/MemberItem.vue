<template>
  <CardItem
    @mouseleave="showAction = false"
    @mouseover="showAction = true"
  >
    {{ member.email }}
    <transition mode="out-in" name="slide-left">
      <span
        v-if="showAction && isOwner"
        @click="deleteMember"
        class="absolute flex items-center justify-center bg-red-600 inset-y-0 right-0 left-[80%] z-10 text-white"
      >X</span>
    </transition>
  </CardItem>
</template>

<script setup lang="ts">
import CardItem from "@app/components/CardItem.vue";
import {inject, ref} from "vue";

const props = defineProps<{ member: { id: number, email: string } }>()

const isOwner = inject('isOwner')

const emits = defineEmits<{
  (e: 'member:delete', id: number)
}>()

const showAction = ref<boolean>(false)

function deleteMember() {
  emits('member:delete', props.member.id)
}
</script>

<style scoped>
.slide-left-enter-from {
  transform: translateX(100%);
}
.slide-left-enter-active {
  transition: transform 0.4s ease;
}
.slide-left-enter-to {
  transform: translateX(0);
}

.slide-left-leave-from {
  transform: translateX(0);
}
.slide-left-leave-active {
  transition: transform 0.4s ease;
}
.slide-left-leave-to {
  transform: translateX(100%);
}
</style>