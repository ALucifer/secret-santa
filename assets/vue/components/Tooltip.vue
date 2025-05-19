<template>
  <div>
    <div
      @mouseover="handleOver($event)"
      @mouseleave="showTooltip = false"
    >
      <slot></slot>
    </div>
    <teleport to="body">
      <span v-show="showTooltip" class="tooltip" :style="style">{{ message }}</span>
    </teleport>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

defineProps<{ message: string }>()

const showTooltip = ref(false)

const style = ref({
  top: '0px',
  left: '0px'
})

function handleOver(event: any) {
  showTooltip.value = true;

  const rect: DOMRect = event.currentTarget.getBoundingClientRect()

  style.value = {
    top: (rect.top - rect.height - 20) + 'px',
    left: (rect.left + (rect.width / 2)) + 'px',
  }
}
</script>

<style scoped>
.tooltip {
  background-color: #333;
  color: #fff;
  text-align: center;
  padding: 6px 10px;
  border-radius: 6px;
  position: fixed;
  z-index: 100;
  transform: translateX(-50%);
  transition: opacity 0.3s ease;
  white-space: nowrap;
}

.tooltip::after {
  content: '';
  position: absolute;
  top: 100%; /* flèche vers le bas */
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #333 transparent transparent transparent;
}
</style>