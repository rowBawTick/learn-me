<script setup>
import { ref, computed } from 'vue'
import { QCard, QCardSection, QBtn, QRating } from 'quasar'
import MainLayout from '@/Layouts/MainLayout.vue'

const props = defineProps({
    advert: {
        type: Object,
        required: true
    }
})

const isFavorite = ref(false)

const toggleFavorite = () => {
    isFavorite.value = !isFavorite.value
}

const rating = computed(() => {
    return props.advert.average_rating || 0
})

const availableTimes = computed(() => {
    return props.advert.available_times || []
})

const reviews = computed(() => {
    return props.advert.reviews || []
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}
</script>

<template>
    <MainLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <QCard class="w-full">
                <QCardSection>
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ advert.title }}</h1>
                            <div class="mt-2 flex items-center">
                                <QRating
                                    :model-value="rating"
                                    :max="5"
                                    size="1em"
                                    readonly
                                />
                                <span class="ml-2 text-sm text-gray-600">
                                    {{ rating ? rating.toFixed(1) : 'No' }}
                                    ({{ reviews.length }} reviews)
                                </span>
                            </div>
                            <div class="mt-4 text-xl font-medium text-primary-600">
                                {{ advert.formatted_price }} per hour
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <QBtn
                                flat
                                round
                                :icon="isFavorite ? 'favorite' : 'favorite_border'"
                                :color="isFavorite ? 'red' : 'grey'"
                                @click="toggleFavorite"
                            />
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-lg font-medium text-gray-900">About this tutor</h2>
                        <p class="mt-2 text-gray-700 whitespace-pre-line">{{ advert.description }}</p>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-lg font-medium text-gray-900">Available Times</h2>
                        <div v-if="availableTimes.length" class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div
                                v-for="time in availableTimes"
                                :key="`${time.day_of_week}-${time.local_start_time}`"
                                class="p-3 bg-gray-50 rounded-lg"
                            >
                                <div class="font-medium">{{ time.day_of_week }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ time.local_start_time }} - {{ time.local_end_time }}
                                </div>
                            </div>
                        </div>
                        <p v-else class="mt-2 text-gray-600 italic">No available times provided</p>
                    </div>

                    <div class="mt-8 flex justify-center">
                        <QBtn
                            color="primary"
                            icon="mail"
                            label="Message Tutor"
                            class="w-full sm:w-auto"
                        />
                    </div>
                </QCardSection>
            </QCard>

            <!-- Reviews Section -->
            <QCard class="w-full mt-8">
                <QCardSection>
                    <h2 class="text-xl font-bold text-gray-900">Reviews</h2>
                    <div v-if="reviews.length" class="mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            <QCard
                                v-for="review in reviews"
                                :key="review.id"
                                flat
                                bordered
                                class="bg-gray-50/50"
                            >
                                <QCardSection>
                                    <div class="space-y-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ review.reviewer.name }}
                                            </h3>
                                            <div class="mt-2 flex items-center gap-4 text-sm text-gray-600">
                                                <div class="flex items-center">
                                                    <QRating
                                                        :model-value="review.rating"
                                                        :max="5"
                                                        size="1em"
                                                        readonly
                                                    />
                                                    <span class="ml-1">{{ review.rating }}/5</span>
                                                </div>
                                                <span class="text-gray-400">•</span>
                                                <span>{{ formatDate(review.created_at) }}</span>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 whitespace-pre-line">{{ review.description }}</p>
                                    </div>
                                </QCardSection>
                            </QCard>
                        </div>
                    </div>
                    <p v-else class="mt-4 text-gray-600 italic text-center">
                        No reviews yet
                    </p>
                </QCardSection>
            </QCard>
        </div>
    </MainLayout>
</template>
