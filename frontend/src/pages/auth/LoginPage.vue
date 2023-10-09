<template>
  <div class="">
    <q-page-container style="padding-top: 0 !important;">
      <q-page class="flex h-screen items-center justify-center">
        <q-card class="tw-mx-auto tw-mx-2 tw-w-full md:tw-w-2/6 lg:tw-w-1/6">
          <q-card-section>
            <div class="tw-text-center tw-text-2xl tw-uppercase tw-py-6">Login</div>
            <q-form @submit="login" class="">
              <q-input
              v-model="form.email"
              label="Email"
              type="email"
              :disable="data.loading"
              :error="errors.form.hasOwnProperty('email') ?? false"
              :error-message="errors.form.email?.[0] ?? ''"
              :rules="[
                val => val && val.length > 4 || 'The email must be a valid email address.',
              ]"
              class="tw-mb-3"
              lazy-rules
              outlined
              required>
              </q-input>
              <q-input
                v-model="form.password"
                label="Password"
                type="password"
                :disable="data.loading"
                :error="errors.form.hasOwnProperty('password') ?? false"
                :error-message="errors.form.password?.[0] ?? ''"
                :rules="[
                  val => val && val.length > 7 || 'Password must have at least 8 characters.',
                ]"
                class="tw-mb-3"
                lazy-rules
                outlined
                required>
              </q-input>

              <q-btn
                :loading="data.loading"
                color="amber-6"
                label="Login"
                type="submit"
                class="q-mt-md tw-float-right tw-mb-6 tw-w-full md:tw-w-auto">
              </q-btn>
            </q-form>
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>
  </div>
</template>

<script setup>
  import { reactive } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuthStore } from 'src/stores/authStore';

  const router = useRouter()
  const authStore = useAuthStore()

  const data = reactive({
    loading: false
  })

  const form = reactive({
    email: '',
    password: ''
  })

  const errors = reactive({
    form: {},
  })

  const login = () => {
    authStore.login(form)
    .then((res) => {
      router.push({ name: 'admin.tasks.index', query: { status: 'todo' } })
    }).catch((err) => {
      errors.form = err ?? {}
    }).finally(() => {
      data.loading = false
    })
  }

</script>

<style scoped>

</style>
