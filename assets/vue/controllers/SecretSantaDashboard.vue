<template>
  <div class="flex flex-col gap-2">
    <transition>
      <div class="p-4 rounded bg-red-200 text-red-800" v-if="error">
        Une erreur est survenu
      </div>
    </transition>
    <div class="flex flex-wrap gap-2" v-if="data">
      <MemberItem
        v-for="member in data.members"
        :key="member.id"
        :member="member"
        @member:delete="deleteMember"
      />
      <NewMember @member:new="(e) => submit(e.member)"  />
    </div>
  </div>

</template>

<script setup lang="ts">
import NewMember from "@app/components/NewMember.vue";
import { useCookie } from "@app/composables/useCookie";
import { useFetch } from "@app/composables/useFetch";
import MemberItem from "@app/components/MemberItem.vue";
import { Member } from "@app/types";
import { ref } from "vue";

const props = defineProps<{ santaId: number }>()

const error = ref(false)

const { readCookie } = useCookie()
const { data } = useFetch<{ members: Member[] }>(`/api/secret-santa/${props.santaId}/members`)

async function deleteMember(id: number) {
  try {
    await fetch(
      `/api/secret-santa/${props.santaId}/delete/member/${id}`,
      {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + readCookie('AUTH_TOKEN')
        },
      }
    )

    data.value!.members = data.value!.members.filter((m) => m.id !== id)
  } catch (e: any) {
    error.value = true

    setTimeout(() => error.value = false, 10000)
  }
}

async function submit(member: string) {
  try {
    const response = await fetch(
      `/api/secret-santa/${props.santaId}/register/member`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + readCookie('AUTH_TOKEN')
          },
          body: JSON.stringify({ email: member }),
        }
    )

    const newMember = await response.json()
    data.value!.members.push(newMember)
  } catch (e: any) {
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