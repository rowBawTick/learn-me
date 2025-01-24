<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated class="bg-white text-black">
      <q-toolbar>
        <q-toolbar-title class="text-primary">
          <Link href="/" class="text-inherit no-decoration">LearnMe</Link>
        </q-toolbar-title>

        <q-space />

        <!-- Navigation Items -->
        <div class="gt-xs">
          <Link href="/search" class="q-btn q-btn-item non-selectable no-outline q-btn--flat no-wrap text-black q-btn--rectangle q-focusable q-hoverable">
            Search Lessons
          </Link>
          <Link v-if="user?.is_tutor" href="/my-adverts" class="q-btn q-btn-item non-selectable no-outline q-btn--flat no-wrap text-black q-btn--rectangle q-focusable q-hoverable">
            My Adverts
          </Link>
          <Link href="/create-advert" class="q-btn q-btn-item non-selectable no-outline q-btn--flat no-wrap text-black q-btn--rectangle q-focusable q-hoverable">
            Create Advert
          </Link>
          <Link href="/messages" class="q-btn q-btn-item non-selectable no-outline q-btn--flat no-wrap text-black q-btn--rectangle q-focusable q-hoverable">
            Messages
          </Link>
          <Link href="/reviews" class="q-btn q-btn-item non-selectable no-outline q-btn--flat no-wrap text-black q-btn--rectangle q-focusable q-hoverable">
            Reviews
          </Link>
        </div>

        <!-- Mobile Menu -->
        <q-btn flat round icon="menu" class="lt-sm">
          <q-menu>
            <q-list style="min-width: 200px">
              <q-item>
                <Link href="/search" class="text-inherit no-decoration full-width">Search Lessons</Link>
              </q-item>
              <q-item v-if="user?.is_tutor">
                <Link href="/my-adverts" class="text-inherit no-decoration full-width">My Adverts</Link>
              </q-item>
              <q-item>
                <Link href="/create-advert" class="text-inherit no-decoration full-width">Create Advert</Link>
              </q-item>
              <q-item>
                <Link href="/messages" class="text-inherit no-decoration full-width">Messages</Link>
              </q-item>
              <q-item>
                <Link href="/reviews" class="text-inherit no-decoration full-width">Reviews</Link>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>

        <!-- User Menu -->
        <q-btn flat round>
          <q-avatar size="32px">
            <img :src="user?.avatar || 'https://www.gravatar.com/avatar/?d=mp'" />
          </q-avatar>

          <q-menu>
            <q-list style="min-width: 150px">
              <q-item>
                <Link href="/profile" class="text-inherit no-decoration full-width">Profile</Link>
              </q-item>
              <q-item>
                <Link href="/settings" class="text-inherit no-decoration full-width">Settings</Link>
              </q-item>
              <q-separator />
              <q-item clickable @click="logout">
                <q-item-section>Logout</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <slot />
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
.no-decoration {
  text-decoration: none;
}

.text-inherit {
  color: inherit;
}

.full-width {
  width: 100%;
  display: block;
  padding: 8px 16px;
}
</style>
