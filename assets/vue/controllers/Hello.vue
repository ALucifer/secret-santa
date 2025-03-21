<template>
  <div class="flex flex-wrap gap-2" v-if="data">
    <MemberItem v-for="member in data.members" :key="member.id" :member="member" />
    <NewMember @member:new="(e) => submit(e.member)"  />
  </div>
</template>

<script setup lang="ts">
import NewMember from "@app/components/NewMember.vue";
import { useCookie } from "@app/composables/useCookie";
import { useFetch } from "@app/composables/useFetch";
import MemberItem from "@app/components/MemberItem.vue";
import { Member } from "@app/types";

const props = defineProps<{ santaId: number }>()

const { readCookie } = useCookie()
const { data } = useFetch<{ members: Member[] }>(`/api/secret-santa/${props.santaId}/members`)

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
    console.log(e)
  }
}
</script>
