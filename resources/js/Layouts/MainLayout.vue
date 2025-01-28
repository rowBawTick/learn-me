<template>
  <q-layout view="hHh lpR fFf" class="bg-gray-50">
    <q-header elevated class="bg-white text-black">
      <q-toolbar class="q-px-lg">
        <q-toolbar-title class="text-primary">
          <Link href="/" class="logo-link">
            <span class="text-2xl font-bold">learn.me</span>
          </Link>
        </q-toolbar-title>

        <q-space />

        <!-- Navigation Items -->
        <div class="gt-xs">
          <Link href="/search" class="nav-link">
            Search Lessons
          </Link>
          <Link v-if="user?.is_tutor" href="/my-adverts" class="nav-link">
            My Adverts
          </Link>
          <Link href="/adverts/create" class="nav-link">
            Create Advert
          </Link>
          <Link href="/messages" class="nav-link">
            Messages
          </Link>
          <Link href="/reviews" class="nav-link">
            Reviews
          </Link>
        </div>

        <!-- Mobile Menu -->
        <q-btn flat round icon="menu" class="lt-sm">
          <q-menu>
            <q-list style="min-width: 200px">
              <q-item>
                <Link href="/search" class="mobile-nav-link">Search Lessons</Link>
              </q-item>
              <q-item v-if="user?.is_tutor">
                <Link href="/my-adverts" class="mobile-nav-link">My Adverts</Link>
              </q-item>
              <q-item>
                <Link href="/adverts/create" class="mobile-nav-link">Create Advert</Link>
              </q-item>
              <q-item>
                <Link href="/messages" class="mobile-nav-link">Messages</Link>
              </q-item>
              <q-item>
                <Link href="/reviews" class="mobile-nav-link">Reviews</Link>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>

        <!-- User Menu -->
        <q-btn flat round class="ml-4">
          <q-avatar size="32px" class="shadow-sm">
            <img :src="user?.avatar || 'https://www.gravatar.com/avatar/?d=mp'" />
          </q-avatar>

          <q-menu>
            <q-list style="min-width: 150px">
              <q-item>
                <Link href="/profile" class="mobile-nav-link">Profile</Link>
              </q-item>
              <q-item>
                <Link href="/settings" class="mobile-nav-link">Settings</Link>
              </q-item>
              <q-separator />
              <q-item clickable @click="logout" class="text-red-500">
                <q-item-section>Logout</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-page-container class="q-pa-md">
      <div class="max-w-7xl mx-auto">
        <slot />
      </div>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'

const user = usePage().props.auth.user
const form = useForm()

const logout = () => {
  form.post('/logout')
}
</script>

<style scoped>
.logo-link {
  text-decoration: none;
  color: inherit;
  transition: opacity 0.2s;
}

.logo-link:hover {
  opacity: 0.9;
}

.nav-link {
  @apply px-4 py-2 rounded-full text-gray-700 hover:text-primary hover:bg-sky-50 transition-colors duration-200 font-medium text-sm;
  text-decoration: none;
  cursor: pointer;
}

.mobile-nav-link {
  @apply block px-4 py-2 text-gray-700 hover:text-primary hover:bg-sky-50 transition-colors duration-200 rounded-lg;
  text-decoration: none;
  width: 100%;
  cursor: pointer;
}
</style>
