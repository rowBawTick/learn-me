<script setup>
import { defineProps } from 'vue';

defineProps({
  title: {
    type: String,
    default: 'No data available'
  },
  message: {
    type: String,
    default: 'There are no items to display at this time.'
  },
  actionLabel: {
    type: String,
    default: ''
  },
  actionRoute: {
    type: String,
    default: ''
  }
});
</script>

<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="rounded-full bg-gray-100 p-6 mb-4">
      <q-icon name="sentiment_dissatisfied" size="3rem" color="grey" />
    </div>
    <h3 class="text-xl font-semibold text-darkestGrey mb-2">{{ title }}</h3>
    <p class="text-grey mb-6 max-w-md">{{ message }}</p>
    <q-btn 
      v-if="actionLabel && actionRoute"
      color="primary"
      :label="actionLabel"
      class="font-medium"
      :to="actionRoute"
    />
  </div>
</template>
