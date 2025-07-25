<template>
  <Modal>
    <form>
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div>
          <div class="mb-6">
            <input type="text" v-model="form.label" required placeholder="Titre de votre secret santa." class="border border-gray-300 bg-gray-50 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
          </div>
          <div class="flex flex-row-reverse">
            <div class="inline-flex items-center">
              <input type="checkbox" id="secret_santa_registerMe" v-model="form.registerMe" class="mr-2 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
              <label class="block text-gray-800" for="secret_santa_registerMe">S'inscrire</label>
            </div>
          </div>
        </div>
        {{ form }}
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
  </Modal>
</template>

<script setup lang="ts">
import { reactive } from "vue"
import Modal from "@app/components/global/Modal.vue";
import Routing from "fos-router";
import { Options, useFetch } from "@app/composables/useFetch";
import { SecretSanta } from "@app/types";

const form = reactive({
  label: 'test',
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