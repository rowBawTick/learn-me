<template>
  <div class="space-y-6">
    <div>
      <label for="title" class="block text-sm font-medium text-darkerGrey">Title</label>
      <input
        id="title"
        :value="title"
        @input="$emit('update:title', $event.target.value)"
        type="text"
        class="mt-1 block w-full rounded-md border-lighterGrey shadow-sm focus:border-primary focus:ring-primary"
        required
      />
      <div v-if="errors?.title" class="mt-1 text-error text-sm">{{ errors.title }}</div>
    </div>

    <div>
      <label for="subject" class="block text-sm font-medium text-darkerGrey">Subject</label>
      <q-select
        id="subject"
        :model-value="subject_id"
        @update:model-value="$emit('update:subject_id', $event)"
        :options="subjectOptions"
        filled
        dense
        emit-value
        map-options
        class="w-full"
        required
      >
        <template v-slot:no-option>
          <q-item>
            <q-item-section class="text-grey">
              No subjects found
            </q-item-section>
          </q-item>
        </template>
      </q-select>
      <div v-if="errors?.subject_id" class="mt-1 text-error text-sm">{{ errors.subject_id }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subject_id: {
    type: [String, Number],
    required: true
  },
  subjects: {
    type: Array,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
});

defineEmits(['update:title', 'update:subject_id']);

const subjectOptions = computed(() => {
  return props.subjects.map(subject => ({
    label: subject.name,
    value: subject.id
  }));
});
</script>
