<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import StarRating from '@/Components/StarRating.vue';
import { formatDistanceToNow } from 'date-fns';

defineProps({
  reviews: {
    type: Array,
    required: true
  },
  error: {
    type: String,
    default: null
  }
});

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return formatDistanceToNow(date, { addSuffix: true });
};
</script>

<template>
  <Head title="My Reviews" />

  <MainLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold mb-8 text-darkestGrey">My Reviews</h1>

        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
          {{ error }}
        </div>

        <div v-if="reviews.length === 0 && !error">
          <EmptyState 
            title="No Reviews Yet" 
            message="You haven't received any reviews for your adverts yet."
          />
        </div>

        <div v-else class="space-y-6">
          <div 
            v-for="review in reviews" 
            :key="review.id" 
            class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200"
          >
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4">
              <div>
                <div class="flex items-center mb-2">
                  <StarRating :rating="review.rating" size="md" />
                  <span class="ml-2 text-sm text-gray-500">{{ review.rating }}/5</span>
                </div>
                <h3 class="font-semibold text-lg">{{ review.advert.subject.name }}</h3>
                <p class="text-sm text-gray-500">
                  Reviewed by {{ review.reviewer.name }} · {{ formatDate(review.created_at) }}
                </p>
              </div>
              <div class="mt-2 sm:mt-0">
                <q-badge color="primary" class="text-xs">
                  {{ review.advert.subject.name }}
                </q-badge>
              </div>
            </div>
            
            <p class="text-gray-700 whitespace-pre-line">{{ review.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
