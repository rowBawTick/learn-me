<template>
  <MainLayout>
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold mb-8">Create New Advert</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          />
          <div v-if="form.errors.title" class="mt-1 text-red-600 text-sm">{{ form.errors.title }}</div>
        </div>

        <div>
          <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
          <select
            id="subject"
            v-model="form.subject_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          >
            <option value="">Select a subject</option>
            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
              {{ subject.name }}
            </option>
          </select>
          <div v-if="form.errors.subject_id" class="mt-1 text-red-600 text-sm">{{ form.errors.subject_id }}</div>
        </div>

        <div>
          <label for="price" class="block text-sm font-medium text-gray-700">Price per hour (£)</label>
          <input
            id="price"
            v-model="form.price_per_hour"
            type="number"
            min="0"
            step="0.01"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          />
          <div v-if="form.errors.price_per_hour" class="mt-1 text-red-600 text-sm">{{ form.errors.price_per_hour }}</div>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          ></textarea>
          <div v-if="form.errors.description" class="mt-1 text-red-600 text-sm">{{ form.errors.description }}</div>
        </div>

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

const props = defineProps({
  subjects: {
    type: Array,
    required: true
  }
})

const form = useForm({
  title: '',
  subject_id: '',
  price_per_hour: '',
  description: ''
})

const submit = () => {
  form.post(route('adverts.store'))
}
</script>
