<template>
  <CardItem
    @mouseleave="showAction = false"
    @mouseover="showAction = true"
  >
    <Tooltip v-if="!member.invitationAccepted && isOwner" message="Le membre n'a pas encore accepté l'invitation">
      <WarningIcon stroke="red" />
    </Tooltip>
    {{ member.email }}
    <transition mode="out-in" name="slide-fade">
      <span
        v-if="showAction && isOwner"
        @click="deleteMember"
        class="absolute flex items-center justify-center bg-red-600 inset-y-0 right-0 left-[80%] text-white"
      >X</span>
    </transition>
  </CardItem>
</template>

<script setup lang="ts">
import CardItem from "@app/components/CardItem.vue";
import {inject, ref} from "vue";
import WarningIcon from "@app/icons/WarningIcon.vue";
import Tooltip from "@app/components/Tooltip.vue";
import { Member } from "@app/types";

const props = defineProps<{ member: Member }>()

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

/* transition hooks */
.expand-enter-active, .expand-leave-active {
  transition: all 0.3s ease;
}
.expand-enter-from, .expand-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
.expand-enter-to, .expand-leave-from {
  opacity: 1;
  transform: scale(1);
}

/* transition classes */
.slide-fade-enter-active {
  transition: all 0.5s ease;
}
.slide-fade-leave-active {
  transition: all 0.5s ease;
}
.slide-fade-enter-from {
  transform: translateX(100%);
  opacity: 0;
}
.slide-fade-enter-to {
  transform: translateX(0);
  opacity: 1;
}
.slide-fade-leave-from {
  transform: translateX(0);
  opacity: 1;
}
.slide-fade-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>