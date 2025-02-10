<script setup>
import { ref, computed } from 'vue'
import { QSelect, QRange, QCard, QCardSection, QBtn } from 'quasar'

const props = defineProps({
    subjects: {
        type: Array,
        required: true
    },
    maxPrice: {
        type: Number,
        required: true
    },
    initialFilters: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['update:filters', 'clear'])

const selectedSubject = ref(props.initialFilters.subject || null)
const priceRange = ref({
    min: props.initialFilters.min_price || 0,
    max: props.initialFilters.max_price || props.maxPrice
})

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

const clearFilters = () => {
    selectedSubject.value = null
    priceRange.value = {
        min: 0,
        max: props.maxPrice
    }
    emit('clear')
}

const updateFilters = () => {
    emit('update:filters', {
        subject: selectedSubject.value,
        min_price: priceRange.value.min,
        max_price: priceRange.value.max
    })
}
</script>

<template>
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
                        autocomplete="true">
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
                        label
                        class="mt-1"
                        color=""
                    />
                </div>

                <div class="pt-4">
                    <QBtn
                        color="primary"
                        class="w-full"
                        label="Apply Filters"
                        @click="updateFilters"
                    />
                </div>
            </div>
        </QCardSection>
    </QCard>
</template>
