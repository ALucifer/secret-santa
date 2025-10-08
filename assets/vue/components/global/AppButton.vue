<template>
  <button @click.stop.prevent="$emit('press')">
    <slot></slot>
  </button>
</template>