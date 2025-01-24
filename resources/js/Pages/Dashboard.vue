<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const search = ref('')
const myAdverts = ref([]) // This will be populated from the backend
const featuredAdverts = ref([]) // This will be populated from the backend

const deleteAdvert = (id) => {
  // Implementation will come later
}
</script>

<template>
    <Head title="Dashboard" />

    <MainLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <!-- Search Section -->
          <div class="mb-8">
            <h1 class="text-3xl font-bold mb-4">Find Your Perfect Tutor</h1>
            <q-input
              outlined
              v-model="search"
              label="Search for lessons"
              class="max-w-xl"
            >
              <template v-slot:append>
                <q-btn round flat icon="search" />
              </template>
            </q-input>
          </div>

          <!-- My Adverts Section (if user is tutor) -->
          <div v-if="user?.value?.is_tutor" class="mb-8">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-2xl font-semibold">My Adverts</h2>
              <Link
                href="/create-advert"
                class="q-btn q-btn-item non-selectable no-outline q-btn--standard q-btn--rectangle bg-primary text-white q-btn--actionable q-focusable q-hoverable"
              >
                Create New Advert
              </Link>
            </div>
            <div v-if="myAdverts.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <q-card v-for="advert in myAdverts" :key="advert.id" class="cursor-pointer hover:shadow-lg transition-shadow">
                <q-card-section>
                  <div class="text-h6">{{ advert.title }}</div>
                  <div class="text-subtitle2">{{ advert.subject.name }}</div>
                </q-card-section>
                <q-card-section>
                  <p class="line-clamp-2">{{ advert.description }}</p>
                </q-card-section>
                <q-card-actions align="right">
                  <Link :href="`/adverts/${advert.id}/edit`" class="q-btn q-btn-item non-selectable no-outline q-btn--flat no-wrap text-primary q-btn--rectangle q-focusable q-hoverable">
                    Edit
                  </Link>
                  <button @click="deleteAdvert(advert.id)" class="q-btn q-btn-item non-selectable no-outline q-btn--flat no-wrap text-negative q-btn--rectangle q-focusable q-hoverable">
                    Delete
                  </button>
                </q-card-actions>
              </q-card>
            </div>
            <div v-else class="text-center py-8">
              <p class="text-gray-500 mb-4">You haven't created any adverts yet</p>
              <Link
                href="/create-advert"
                class="q-btn q-btn-item non-selectable no-outline q-btn--standard q-btn--rectangle bg-primary text-white q-btn--actionable q-focusable q-hoverable"
              >
                Create Your First Advert
              </Link>
            </div>
          </div>

          <!-- Featured Tutors Section -->
          <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">Featured Tutors</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <q-card v-for="advert in featuredAdverts" :key="advert.id" class="cursor-pointer hover:shadow-lg transition-shadow">
                <q-card-section>
                  <div class="flex items-center mb-2">
                    <q-avatar size="40px">
                      <img :src="advert.user.avatar || 'https://www.gravatar.com/avatar/?d=mp'" />
                    </q-avatar>
                    <div class="ml-3">
                      <div class="text-h6">{{ advert.user.name }}</div>
                      <div class="text-subtitle2">{{ advert.subject.name }}</div>
                    </div>
                  </div>
                  <p class="line-clamp-2">{{ advert.description }}</p>
                </q-card-section>
                <q-card-actions align="right">
                  <Link :href="`/tutors/${advert.user.id}`" class="q-btn q-btn-item non-selectable no-outline q-btn--flat no-wrap text-primary q-btn--rectangle q-focusable q-hoverable">
                    View Profile
                  </Link>
                  <Link :href="`/adverts/${advert.id}`" class="q-btn q-btn-item non-selectable no-outline q-btn--standard q-btn--rectangle bg-primary text-white q-btn--actionable q-focusable q-hoverable">
                    Book Lesson
                  </Link>
                </q-card-actions>
              </q-card>
            </div>
          </div>
        </div>
    </MainLayout>
</template>
