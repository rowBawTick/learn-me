<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import MyAdvert from '@/Components/MyAdvert.vue';
import EmptyState from '@/Components/EmptyState.vue';

defineProps({
  adverts: {
    type: Array,
    required: true
  }
});
</script>

<template>
  <Head title="My Adverts" />

  <MainLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold mb-8 text-darkestGrey">My Adverts</h1>

        <div v-if="adverts.length === 0">
          <EmptyState
            title="No Adverts Yet"
            message="You haven't created any adverts yet."
            actionLabel="Create Your First Advert"
            actionRoute="/adverts/create"
          />
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <MyAdvert
            v-for="advert in adverts"
            :key="advert.id"
            :advert="advert"
          />
        </div>
      </div>
    </div>
  </MainLayout>
</template>
