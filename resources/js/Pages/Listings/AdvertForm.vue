<template>
  <Head :title="advert ? 'Edit Advert' : 'Create New Advert'" />

  <MainLayout>
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold mb-8 text-darkestGrey">{{ advert ? 'Edit Advert' : 'Create New Advert' }}</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <AdvertBasicInfo
          v-model:title="form.title"
          v-model:subject_id="form.subject_id"
          :subjects="subjects"
          :errors="form.errors"
        />

        <AdvertPricing
          v-model:currency_id="form.currency_id"
          v-model:price_per_hour="form.price_per_hour"
          :currencies="currencies"
          :errors="form.errors"
        />

        <AdvertDescription
          v-model="form.description"
          :error="form.errors.description"
        />

        <TimeSlotManager
          v-model="form.available_times"
          :error="form.errors['available_times']"
        />

        <div class="flex justify-end">
          <q-btn
            type="submit"
            :loading="form.processing"
            color="primary"
            class="px-4 py-2"
            label="Save Advert"
          />
        </div>
      </form>
    </div>
  </MainLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import TimeSlotManager from '@/Components/TimeSlots/TimeSlotManager.vue'
import AdvertBasicInfo from '@/Components/Advert/AdvertBasicInfo.vue'
import AdvertPricing from '@/Components/Advert/AdvertPricing.vue'
import AdvertDescription from '@/Components/Advert/AdvertDescription.vue'
import { Head } from '@inertiajs/vue3'
import { useQuasar } from 'quasar'

const $q = useQuasar()

const props = defineProps({
  subjects: {
    type: Array,
    required: true
  },
  currencies: {
    type: Array,
    required: true
  },
  advert: {
    type: Object,
    required: false,
    default: null
  }
})

const form = useForm({
  title: props.advert?.title || '',
  subject_id: props.advert?.subject_id || '',
  currency_id: props.advert?.currency_id || '',
  price_per_hour: props.advert?.price_per_hour || '',
  description: props.advert?.description || '',
  available_times: props.advert?.available_times?.map(({day_of_week, local_start_time, local_end_time }) => ({
    day_of_week, start_time: local_start_time, end_time: local_end_time }))
    || []
})

function submit() {
  if (props.advert) {
    form.put(route('adverts.update', props.advert.id), {
      onSuccess: () => {
        $q.notify({
          type: 'positive',
          message: 'Advert updated successfully',
          position: 'top'
        })
      },
      onError: () => {
        $q.notify({
          type: 'negative',
          message: 'Failed to update advert',
          position: 'top'
        })
      }
    })
  } else {
    form.post(route('adverts.store'), {
      onSuccess: () => {
        $q.notify({
          type: 'positive',
          message: 'Advert created successfully',
          position: 'top'
        })
      },
      onError: () => {
        $q.notify({
          type: 'negative',
          message: 'Failed to create advert',
          position: 'top'
        })
      }
    })
  }
}
</script>
