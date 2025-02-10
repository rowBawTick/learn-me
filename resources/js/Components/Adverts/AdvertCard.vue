<script setup>
import { Link } from '@inertiajs/vue3'
import { QCard, QCardSection, QRating } from 'quasar'
import { computed } from 'vue'

const props = defineProps({
    advert: {
        type: Object,
        required: true
    }
})

const formatPrice = (price, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency.code
    }).format(price)
}

const getNextAvailableSlot = (times) => {
    if (!times || times.length === 0) return null

    const today = new Date()
    const dayMap = {
        'Sunday': 0,
        'Monday': 1,
        'Tuesday': 2,
        'Wednesday': 3,
        'Thursday': 4,
        'Friday': 5,
        'Saturday': 6
    }

    return times
        .sort((a, b) => {
            const dayDiff = dayMap[a.day_of_week] - dayMap[b.day_of_week]
            if (dayDiff !== 0) return dayDiff
            return a.start_time.localeCompare(b.start_time)
        })
        .find(time => {
            const timeDay = dayMap[time.day_of_week]
            const currentDay = today.getDay()
            return timeDay >= currentDay
        })
}

const rating = computed(() => {
    return props.advert.avg_rating ? Number(props.advert.avg_rating) : 0
})
</script>

<template>
    <QCard class="h-full hover:shadow-lg transition-shadow duration-200">
        <QCardSection>
            <Link :href="`/adverts/${advert.id}`" class="block">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ advert.title }}</h3>
                        <div class="mt-2 flex items-center">
                            <QRating
                                :model-value="rating"
                                :max="5"
                                size="1em"
                                readonly
                            />
                            <span class="ml-2 text-sm text-gray-600">
                                {{ rating ? rating.toFixed(1) : 'No' }}
                                ({{ advert.review_count || 0 }} reviews)
                            </span>
                        </div>
                    </div>
                    <div class="text-lg font-medium text-primary-600">
                        {{ advert.currency.symbol }}{{ advert.price_per_hour }}/hour
                    </div>
                </div>
                
                <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ advert.description }}</p>
                
                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <span>{{ advert.subject.name }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ advert.user.name }}</span>
                </div>
            </Link>
        </QCardSection>
    </QCard>
</template>
