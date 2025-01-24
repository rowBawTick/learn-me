<template>
  <div>
    <div v-if="loading" class="flex justify-center">
      <q-spinner-dots color="primary" size="3em" />
    </div>
    <q-carousel
      v-else
      v-model="slide"
      swipeable
      animated
      arrows
      infinite
      autoplay
      :autoplay-timeout="5000"
      class="bg-transparent"
      height="auto"
    >
      <q-carousel-slide v-for="(chunk, index) in chunks" :key="index" :name="index">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <q-card v-for="review in chunk" :key="review.id" flat bordered class="bg-white">
            <q-card-section>
              <div class="flex items-center mb-2">
                <div class="flex-1">
                  <div class="font-semibold">{{ review.tutor_name }}</div>
                  <div class="text-sm text-gray-600">{{ review.subject }}</div>
                </div>
                <div class="flex items-center">
                  <q-rating
                    :model-value="review.rating"
                    size="1.5em"
                    color="primary"
                    readonly
                  />
                </div>
              </div>
              <p class="text-gray-700 mt-2">{{ review.description }}</p>
              <div class="text-sm text-gray-500 mt-4">
                - {{ review.reviewer_name }}
              </div>
            </q-card-section>
          </q-card>
        </div>
      </q-carousel-slide>
    </q-carousel>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const reviews = ref([])
const loading = ref(true)
const slide = ref(0)

// Split reviews into chunks of 3
const chunks = computed(() => {
  const result = []
  const reviewsArray = reviews.value || []

  for (let i = 0; i < reviewsArray.length; i += 3) {
    const chunk = reviewsArray.slice(i, i + 3)
    result.push(chunk)
  }

  return result
})

onMounted(async () => {
  try {
    const response = await axios.get('/reviews/featured')
    reviews.value = response.data
  } catch (error) {
    console.error('Error fetching featured reviews:', error)
  } finally {
    loading.value = false
  }
})
</script>

<style>
.q-carousel__arrow {
  color: var(--q-primary) !important;
}

.q-carousel__navigation-inner {
  gap: 4px;
}
</style>
