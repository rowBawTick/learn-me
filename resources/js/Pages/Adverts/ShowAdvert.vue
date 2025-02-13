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
                            @click="isChatOpen = true"
                        />
                    </div>
                </QCardSection>
            </QCard>

            <ChatBox
                v-model:is-open="isChatOpen"
                :recipient-id="advert.user_id"
                :recipient-name="advert.user?.name"
            />

            <UserReviews :reviews="reviews" />
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { QCard, QCardSection, QBtn, QIcon, QRating } from 'quasar'
import MainLayout from '@/Layouts/MainLayout.vue'
import ChatBox from '@/Components/ChatBox.vue'
import UserReviews from '@/Components/UserReviews.vue'

const props = defineProps({
    advert: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
})

const isFavorite = ref(false)
const isChatOpen = ref(false)

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
</script>
