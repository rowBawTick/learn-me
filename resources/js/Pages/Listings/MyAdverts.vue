<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  adverts: {
    type: Array,
    required: true
  }
})
</script>

<template>
  <Head title="My Adverts" />

  <MainLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold mb-8 text-darkestGrey">My Adverts</h1>
        
        <div v-if="adverts.length === 0" class="text-center py-12">
          <p class="text-grey">You haven't created any adverts yet.</p>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="advert in adverts" :key="advert.id" 
               class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <h2 class="text-xl font-semibold mb-2 text-darkestGrey">{{ advert.title }}</h2>
            <p class="text-sm text-darkGrey mb-2">{{ advert.subject.name }}</p>
            <p class="text-lg font-medium text-primary mb-4">£{{ advert.price_per_hour }}/hour</p>
            <p class="text-darkerGrey text-sm line-clamp-3 mb-4">{{ advert.description }}</p>
            <div class="flex justify-between items-center">
              <span class="text-sm text-grey">
                Created {{ new Date(advert.created_at).toLocaleDateString() }}
              </span>
              <q-badge :color="advert.is_active ? 'green' : 'grey'" class="text-white">
                {{ advert.is_active ? 'Active' : 'Inactive' }}
              </q-badge>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
