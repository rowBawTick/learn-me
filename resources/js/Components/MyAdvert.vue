<script setup>
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const $q = useQuasar();

const props = defineProps({
  advert: {
    type: Object,
    required: true
  }
});

const isActive = ref(!!props.advert.is_active);

const toggleActive = async (newValue) => {
  try {
    await axios.patch(`/api/adverts/${props.advert.id}/toggle-active`, {
      is_active: newValue
    });

    isActive.value = newValue;

    $q.notify({
      type: 'positive',
      message: 'Advert status updated successfully',
      position: 'top'
    });
  } catch (error) {
    // Revert the toggle if the API call fails
    isActive.value = !newValue;

    $q.notify({
      type: 'negative',
      message: 'Failed to update advert status',
      position: 'top'
    });
  }
};
</script>

<template>
  <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
    <div class="flex justify-between items-start mb-4">
      <div>
        <h2 class="text-xl font-semibold mb-2 text-darkestGrey">{{ advert.title }}</h2>
        <p class="text-sm text-darkGrey mb-2">{{ advert.subject.name }}</p>
      </div>
      <q-btn
        flat
        color="primary"
        icon="edit"
        label="Edit"
        class="font-medium"
        @click="router.visit(`/adverts/${advert.id}/edit`)"
      />
    </div>
    <p class="text-lg font-medium text-primary mb-4">£{{ advert.price_per_hour }}/hour</p>
    <p class="text-darkerGrey text-sm line-clamp-3 mb-4">{{ advert.description }}</p>
    <div class="flex justify-between items-center">
      <span class="text-sm text-grey">
        Created {{ new Date(advert.created_at).toLocaleDateString() }}
      </span>
      <div class="flex flex-col items-end gap-1">
        <span class="text-sm text-grey">Active</span>
        <q-toggle
          v-model="isActive"
          color="positive"
          @update:model-value="toggleActive"
        />
      </div>
    </div>
  </div>
</template>
