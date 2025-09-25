<template>
  <AppModal>
    <form name="secret_santa">
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div>
          <div class="mb-6">
            <AppInput :invalid="false" :showActions="false" v-model="form.label" required placeholder="Titre de votre secret santa." />
          </div>
          <div class="flex flex-row-reverse">
            <div class="inline-flex items-center">
              <AppInput :invalid="false" :showActions="false" v-model="form.registerMe" id="secret_santa_registerMe" type="checkbox" />
              <label class="px-2.5 block text-gray-800" for="secret_santa_registerMe">S'inscrire</label>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
        <button type="submit" @click="handleSubmit" class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-xs bg-teal-600 hover:bg-teal-500 sm:ml-3 sm:w-auto cursor-pointer">
          Ajouter
        </button>
        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-200 sm:mt-0 sm:w-auto cursor-pointer" data-modal-cancel>
          Cancel
        </button>
      </div>
    </form>
  </AppModal>
</template>

<script setup lang="ts">
import { reactive } from "vue"
import AppModal from "@app/components/global/AppModal.vue";
import { Options, useFetch } from "@app/composables/useFetch";
import { SecretSanta } from "@app/types";
import AppInput from "@app/components/global/form/AppInput.vue";
import Routing from "@js/routing";

const form = reactive({
  label: '',
  registerMe: false,
})

async function handleSubmit()
{
  if (form.label === '') {
    return
  }

  const { hasError, data } = await useFetch<SecretSanta>(
    Routing.generate('newSecret'),
    {
      method: 'POST',
      body: JSON.stringify(form),
    } as Options
  )


  if (!hasError.value) {
    window.location.href = Routing.generate('secret_santa_view', { id: data.value?.id })
  }
}
</script>