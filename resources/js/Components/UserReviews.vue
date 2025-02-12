<template>
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
                            <div class="flex items-center justify-between">
                                <div class="font-medium">{{ review.user?.name }}</div>
                                <QRating
                                    :model-value="review.rating"
                                    :max="5"
                                    size="1em"
                                    readonly
                                    color=""
                                />
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                {{ formatDate(review.created_at) }}
                            </div>
                            <div class="mt-2">{{ review.comment }}</div>
                        </QCardSection>
                    </QCard>
                </div>
            </div>
            <p v-else class="mt-2 text-gray-600 italic">No reviews yet</p>
        </QCardSection>
    </QCard>
</template>

<script setup>
import { QCard, QCardSection, QRating } from 'quasar'

defineProps({
    reviews: {
        type: Array,
        default: () => []
    }
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}
</script>
