<script setup>
import { ref, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { QSelect, QRange, QCard, QCardSection, QPagination, QBtn, QSpace } from 'quasar'
import MainLayout from '@/Layouts/MainLayout.vue'
import AdvertCard from '@/Components/Adverts/AdvertCard.vue'

const props = defineProps({
    adverts: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    subjects: {
        type: Array,
        required: true
    },
    maxPrice: {
        type: Number,
        required: true
    }
})

const priceRange = ref({
    min: props.filters.min_price || 0,
    max: props.filters.max_price || props.maxPrice
})

const selectedSubject = ref(props.filters.subject || null)

// Format price for display
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP',
        maximumFractionDigits: 0
    }).format(price)
}

const priceLabel = computed(() => {
    return `${formatPrice(priceRange.value.min)} - ${formatPrice(priceRange.value.max)}`
})

// Generate markers for the price range slider
const priceMarkers = computed(() => {
    const markers = {}
    const steps = [0, 10, 25, 50, 100, 250, 500]
    
    steps.forEach(value => {
        if (value <= props.maxPrice) {
            markers[value] = {
                label: formatPrice(value),
                value
            }
        }
    })
    
    // Always add the max price
    markers[props.maxPrice] = {
        label: formatPrice(props.maxPrice),
        value: props.maxPrice
    }
    
    return markers
})

const clearFilters = () => {
    selectedSubject.value = null
    priceRange.value = {
        min: 0,
        max: props.maxPrice
    }
    updateSearch()
}

const updateSearch = () => {
    const searchParams = {
        subject: selectedSubject.value,
        min_price: priceRange.value.min,
        max_price: priceRange.value.max
    }

    router.get('/adverts/search', searchParams, {
        preserveState: true,
        preserveScroll: true,
        only: ['adverts']
    })
}
</script>

<template>
    <MainLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Search Filters -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-1">
                    <QCard flat bordered>
                        <QCardSection>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium">Filters</h3>
                                <QBtn
                                    flat
                                    dense
                                    color="primary"
                                    label="Clear"
                                    @click="clearFilters"
                                    :disable="!selectedSubject && priceRange.min === 0 && priceRange.max === maxPrice"
                                />
                            </div>
                            
                            <div class="space-y-6">
                                <!-- Subject -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                    <QSelect
                                        v-model="selectedSubject"
                                        :options="subjects"
                                        option-label="name"
                                        option-value="name"
                                        emit-value
                                        map-options
                                        dense
                                        outlined
                                        class="w-full"
                                        placeholder="Any subject"
                                        clearable
                                    >
                                        <template v-slot:selected>
                                            <div class="text-primary">
                                                {{ selectedSubject || 'Any subject' }}
                                            </div>
                                        </template>
                                    </QSelect>
                                </div>

                                <!-- Price Range -->
                                <div class="pt-2">
                                    <div class="flex justify-between items-center mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Price Range</label>
                                        <span class="text-sm text-gray-600">{{ priceLabel }}</span>
                                    </div>
                                    <QRange
                                        v-model="priceRange"
                                        :min="0"
                                        :max="maxPrice"
                                        :step="5"
                                        :markers="true"
                                        label
                                        class="mt-1"
                                    />
                                </div>

                                <div class="pt-4">
                                    <QBtn
                                        color="primary"
                                        class="w-full"
                                        label="Apply Filters"
                                        @click="updateSearch"
                                    />
                                </div>
                            </div>
                        </QCardSection>
                    </QCard>
                </div>

                <!-- Results -->
                <div class="lg:col-span-3">
                    <div v-if="adverts.data.length === 0" class="text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900">No adverts found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search filters</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <AdvertCard
                            v-for="advert in adverts.data"
                            :key="advert.id"
                            :advert="advert"
                        />
                    </div>

                    <!-- Pagination -->
                    <div v-if="adverts.data.length > 0" class="mt-8 flex justify-center">
                        <QPagination
                            v-model="adverts.current_page"
                            :max="adverts.last_page"
                            :max-pages="5"
                            boundary-numbers
                            direction-links
                            @update:model-value="page => router.get(
                                '/adverts/search',
                                { 
                                    subject: selectedSubject,
                                    min_price: priceRange.min,
                                    max_price: priceRange.max,
                                    page 
                                },
                                { preserveState: true, preserveScroll: true }
                            )"
                        />
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
