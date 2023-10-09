<template>
  <q-layout view="hHh lpR lFf">
    <q-header elevated>
      <q-toolbar class="tw-bg-neutral-700 tw-border-b tw-border-amber-500">

        <q-toolbar-title>
          {{ APP_NAME }}
        </q-toolbar-title>

        <div class="flex">
          <router-link
            :to="{name: 'auth.login'}"
            class="tw-px-3">
            Login
          </router-link>

          <q-separator vertical></q-separator>

          <router-link
            :to="{name: 'auth.registration'}"
            class="tw-px-3">
            Register
          </router-link>
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
  const APP_NAME = import.meta.env.VITE_APP_NAME
</script>
