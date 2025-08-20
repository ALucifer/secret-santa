<template>
  <div class="flex flex-col gap-2">
    <transition>
      <div class="p-4 rounded bg-red-200 text-red-800" v-if="error">
        Une erreur est survenu
      </div>
    </transition>
    <div class="md:flex flex-wrap gap-2 grid grid-cols-2">
      <MemberItem
        v-for="member in members"
        :key="member.id"
        :member="member"
        @member:delete="deleteMember"
      />
      <NewMember v-if="isOwner" @member:new="submit" />
    </div>
    <Tooltip v-if="authenticatedUserMember" message="Ma liste de cadeau" class="absolute bottom-[25px] right-[25px] hover:cursor-pointer">
      <AppLink route-name="secret_santa_member_wishlist" :parameters="{ secretSanta: santaId, secretSantaMember: authenticatedUserMember.id }" >
        <DocumentTextIcon />
      </AppLink>
    </Tooltip>
  </div>
</template>

<script setup lang="ts">
import NewMember from "@app/components/NewMember.vue";
import { useCookie } from "@app/composables/useCookie";
import MemberItem from "@app/components/MemberItem.vue";
import { Member } from "@app/types";
import DocumentTextIcon from "@app/icons/DocumentTextIcon.vue";
import AppLink from "@app/components/global/AppLink.vue";
import Tooltip from "@app/components/Tooltip.vue";
import { computed, provide, ref } from "vue";
import Routing from "fos-router";

const props = defineProps<{
  santaId: number,
  isOwner: boolean,
  authenticatedUserEmail: string,
  data: Member[]
}>()

const members = ref<Member[]>(props.data)
const authenticatedUserMember = computed(() => members.value.find(m => m.email === props.authenticatedUserEmail))

provide('isOwner', props.isOwner)

const error = ref(false)

const { readCookie } = useCookie()

async function deleteMember(id: number) {
  try {
    await fetch(
      Routing.generate('deleteMember', { secretSanta: props.santaId, member: id }),
      {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + readCookie('AUTH_TOKEN')
        },
      }
    )

    members.value = members.value.filter((member) => member.id !== id)
  } catch {
    error.value = true

    setTimeout(() => error.value = false, 10000)
  }
}

async function submit(event: { member: string }) {
  try {
    const response = await fetch(
      Routing.generate('registerMember', { id: props.santaId }),
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + readCookie('AUTH_TOKEN')
        },
        body: JSON.stringify({ email: event.member }),
      }
    )

    const newMember = await response.json()
    members.value.push(newMember)
  } catch {
    error.value = true

    setTimeout(() => error.value = false, 10000)
  }
}
</script>

<style scoped>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.8s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>