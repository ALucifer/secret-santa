<template>
  <div class="flex">
    <CardItem @[!showForm&&`click`]="showForm = true" class="h-[76px]">
      <AppInput
        v-if="showForm"
        v-model:email="emailModel"
        @send="submit"
        @keyup.enter="submit"
        placeholder="Email"
        type="email"
        :invalid="isInvalid"
      />
      <template v-else>
        Nouveau membre<PlusIcon/>
      </template>
    </CardItem>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import CardItem from "@app/components/CardItem.vue";
import PlusIcon from "@app/icons/PlusIcon.vue";
import AppInput from "@app/components/AppInput.vue";

const emits = defineEmits(['member:new'])

const showForm = ref(false)
const isInvalid = ref(false)

const emailModel = ref<string>('')

function submit() {
  if (!emailModel.value.toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) {
    isInvalid.value = true
    return
  }

  emits('member:new', { member: emailModel.value })

  isInvalid.value = false
  emailModel.value = ''
  showForm.value = false
}
</script>