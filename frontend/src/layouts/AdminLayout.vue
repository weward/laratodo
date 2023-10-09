<template>
  <q-layout view="hHh lpR lFf">
    <q-header elevated>
      <q-toolbar class="tw-bg-neutral-700 tw-border-b tw-border-amber-500">
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer"/>

        <q-toolbar-title>
          {{ APP_NAME }}
        </q-toolbar-title>

        <div>
          <q-btn-dropdown color="amber-6" :label="authStore.getUser?.name"  flat>
            <q-list>

              <q-item clickable v-close-popup @click="() => logout()">
                <q-item-section>
                  <q-item-label>Logout</q-item-label>
                </q-item-section>
              </q-item>

            </q-list>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>



    <q-page-container>
      <q-drawer v-model="leftDrawerOpen" show-if-above bordered side="left" class="tw-border-r tw-border-amber-100 tw-bg-neutral-200">
        <q-list>
          <q-item-label header>
            Menu
          </q-item-label>

          <EssentialLink v-for="link in navMenuList" :key="link.title" v-bind="link" />
        </q-list>
      </q-drawer>

      <router-view />

    </q-page-container>

  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores/authStore'
import EssentialLink from 'components/EssentialLink.vue'
import { api } from 'src/boot/axios'
import { navMenuList } from 'src/config/navMenu'

const APP_NAME = import.meta.env.VITE_APP_NAME

const leftDrawerOpen = ref(false)

const router = useRouter()

const authStore = useAuthStore()

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

const logout = () => {
  api.post('auth/logout', {}, {
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${localStorage.getItem('authToken')}`
    }
  }).then((res) => {
    localStorage.removeItem('authToken')
    localStorage.removeItem('authUser')
    router.push({ name: 'auth.login' })
  }).catch((err) => {
    if (err.response.status == 404) {
      $q.notify({
        color: 'negative',
        position: 'top',
        message: 'Failed to logout.',
        icon: 'report_problem'
      })
    }
  })
}

</script>
