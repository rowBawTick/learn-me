<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { QPagination } from 'quasar'
import MainLayout from '@/Layouts/MainLayout.vue'
import AdvertCard from '@/Components/Adverts/AdvertCard.vue'
import AdvertFilters from '@/Components/Adverts/AdvertFilters.vue'

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

const handleFiltersUpdate = (filters) => {
    router.get('/adverts/search', filters, {
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
                    <AdvertFilters
                        :subjects="subjects"
                        :max-price="maxPrice"
                        :initial-filters="filters"
                        @update:filters="handleFiltersUpdate"
                        @clear="handleFiltersUpdate({})"
                    />
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
                            @update:model-value="page => handleFiltersUpdate({ 
                                ...filters,
                                page 
                            })"
                        />
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
