<template>
  <Head title="Create New Advert" />

  <MainLayout>
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold mb-8 text-darkestGrey">Create New Advert</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label for="title" class="block text-sm font-medium text-darkerGrey">Title</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            class="mt-1 block w-full rounded-md border-lighterGrey shadow-sm focus:border-primary focus:ring-primary"
            required
          />
          <div v-if="form.errors.title" class="mt-1 text-error text-sm">{{ form.errors.title }}</div>
        </div>

        <div>
          <label for="subject" class="block text-sm font-medium text-darkerGrey">Subject</label>
          <q-select
            id="subject"
            v-model="form.subject_id"
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
          <div v-if="form.errors.subject_id" class="mt-1 text-error text-sm">{{ form.errors.subject_id }}</div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="currency" class="block text-sm font-medium text-darkerGrey">Currency</label>
            <q-select
              id="currency"
              v-model="form.currency_id"
              :options="currencyOptions"
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
                    No currencies found
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
            <div v-if="form.errors.currency_id" class="mt-1 text-error text-sm">{{ form.errors.currency_id }}</div>
          </div>

          <div>
            <label for="price" class="block text-sm font-medium text-darkerGrey">Price per hour</label>
            <q-input
              id="price"
              v-model.number="form.price_per_hour"
              type="number"
              min="0"
              step="any"
              filled
              dense
              borderless
              input-class="no-border"
              :prefix="selectedCurrencySymbol"
              class="w-full"
              @keydown="allowDecimalInput"
              @keydown.up="incrementPrice"
              @keydown.down="decrementPrice"
              required
            />
            <div v-if="form.errors.price_per_hour" class="mt-1 text-error text-sm">{{ form.errors.price_per_hour }}</div>
          </div>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-darkerGrey">Description</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            class="mt-1 block w-full rounded-md border-lighterGrey shadow-sm focus:border-primary focus:ring-primary"
            required
          ></textarea>
          <div v-if="form.errors.description" class="mt-1 text-error text-sm">{{ form.errors.description }}</div>
        </div>

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
            label="Create Advert"
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
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  subjects: {
    type: Array,
    required: true
  },
  currencies: {
    type: Array,
    required: true
  }
})

const subjectOptions = computed(() => {
  return props.subjects.map(subject => ({
    label: subject.name,
    value: subject.id
  }))
})

const currencyOptions = computed(() => {
  return props.currencies.map(currency => ({
    label: `${currency.code} (${currency.symbol})`,
    value: currency.id,
    symbol: currency.symbol
  }))
})

const selectedCurrencySymbol = computed(() => {
  const currency = props.currencies.find(c => c.id === form.currency_id)
  return currency?.symbol || '£'
})

const form = useForm({
  title: '',
  subject_id: '',
  currency_id: '',
  price_per_hour: '',
  description: '',
  available_times: []
})

// Set default currency to GBP if available
if (props.currencies?.length) {
  const gbp = props.currencies.find(c => c.code === 'GBP')
  if (gbp) {
    form.currency_id = gbp.id
  }
}

const submit = () => {
  form.post(route('adverts.store'))
}

const allowDecimalInput = (event) => {
  // Allow: backspace, delete, tab, escape, enter, decimal point
  const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Enter', 'Escape', '.', 'ArrowLeft', 'ArrowRight']
  if (allowedKeys.includes(event.key)) {
    return
  }

  // Allow numbers
  if (/\d/.test(event.key)) {
    return
  }

  // Block any other input
  event.preventDefault()
}

const incrementPrice = (event) => {
  event.preventDefault()
  const currentPrice = parseFloat(form.price_per_hour) || 0
  form.price_per_hour = Math.max(0, Math.floor(currentPrice) + 1)
}

const decrementPrice = (event) => {
  event.preventDefault()
  const currentPrice = parseFloat(form.price_per_hour) || 0
  form.price_per_hour = Math.max(0, Math.floor(currentPrice) - 1)
}
</script>

<style>
.no-border {
  border: none !important;
  box-shadow: none !important;
}

.no-border:focus {
  border: none !important;
  box-shadow: none !important;
}
</style>
