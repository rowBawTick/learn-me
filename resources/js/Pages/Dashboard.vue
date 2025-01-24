<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import FeaturedReviews from '@/Components/FeaturedReviews.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const search = ref('')
const myAdverts = ref([])
const featuredAdverts = ref([])

const deleteAdvert = (id) => {
  // Implementation will come later
}
</script>

<template>
    <Head title="Dashboard" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Search Section -->
                <div class="mb-8">
                    <div class="relative">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search for tutors or subjects..."
                            class="w-full px-4 py-2 text-gray-900 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Featured Reviews Section -->
                <div class="mb-8">
                    <FeaturedReviews />
                </div>
            </div>
        </div>
    </MainLayout>
</template>
