<script setup>
import { defineProps } from 'vue';

defineProps({
  rating: {
    type: Number,
    required: true,
    validator: (value) => value >= 1 && value <= 5
  },
  size: {
    type: String,
    default: 'sm'
  },
  readonly: {
    type: Boolean,
    default: true
  }
});
</script>

<template>
  <div class="flex">
    <template v-for="i in 5" :key="i">
      <q-icon 
        :name="i <= rating ? 'star' : 'star_border'" 
        :size="size === 'sm' ? 'xs' : size === 'md' ? 'sm' : 'md'" 
        :class="i <= rating ? 'text-amber-400' : 'text-gray-300'"
      />
    </template>
  </div>
</template>
