<template>
  <div>
    <label for="description" class="block text-sm font-medium text-darkerGrey">Description</label>
    <textarea
      id="description"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      rows="4"
      class="mt-1 block w-full rounded-md border-lighterGrey shadow-sm focus:border-primary focus:ring-primary"
      required
    ></textarea>
    <div v-if="error" class="mt-1 text-error text-sm">{{ error }}</div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    required: true
  },
  error: {
    type: String,
    default: ''
  }
});

defineEmits(['update:modelValue']);
</script>
