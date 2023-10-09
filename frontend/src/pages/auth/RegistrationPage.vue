<template>
  <div class="">
    <q-page-container style="padding-top: 0 !important;">
      <q-page class="flex h-screen items-center justify-center">
        <q-card class="tw-mx-auto tw-mx-2 tw-w-full md:tw-w-2/6 lg:tw-w-2/6">
          <q-card-section>
            <div class="tw-text-center tw-text-2xl tw-uppercase tw-py-6">Register</div>
            <q-form @submit="register">
              <q-input
                v-model="form.name"
                label="Name"
                type="text"
                :error="errors.form.hasOwnProperty('name') ?? false"
                :error-message="errors.form.name?.[0] ?? ''"
                :rules="[
                  val => val && val.length > 1 || 'The name field is required.',
                ]"
                :disable="data.loading"
                class="tw-mb-3"
                lazy-rules
                outlined
                required>
              </q-input>
              <q-input
                v-model="form.email"
                label="Email"
                type="email"
                :error="errors.form.hasOwnProperty('email') ?? false"
                :error-message="errors.form.email?.[0] ?? ''"
                :rules="[
                  val => val && val.length > 1 || 'The email field is required.',
                  val => val && val.length > 4 || 'Invalid Email.',
                ]"
                :disable="data.loading"
                class="tw-mb-3"
                lazy-rules
                outlined
                required>
              </q-input>
              <q-input
                v-model="form.password"
                @change="checkPasswordRules"
                label="Password"
                type="password"
                :error="errors.form.hasOwnProperty('password') ?? false"
                :error-message="errors.form.password?.[0] ?? ''"
                :rules="[
                  val => val && val.length > 7 || 'Password field must have at least 8 characters.',
                ]"
                :disable="data.loading"
                class="tw-mb-3"
                lazy-rules
                outlined
                required>
              </q-input>
              <q-input
                v-model="form.password_confirmation"
                @change="checkPasswordRules"
                label="Confirm Password"
                type="password"
                :rules="[
                  val => val && val.length > 7 || 'Password field must have at least 8 characters.',
                ]"
                :disable="data.loading"
                class="tw-mb-3"
                lazy-rules
                outlined
                required>
              </q-input>

              <q-btn
                :loading="data.loading"
                color="amber-6"
                label="Register"
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
import { computed, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { api } from 'src/boot/axios'
import { useAuthStore } from 'src/stores/authStore';

const router = useRouter()
const authStore = useAuthStore()

const data = reactive({
  loading: false,
})

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errors = reactive({
  form: {},
})

const register = () => {
  data.loading = true

  authStore.register(form)
  .then((res) => {
    router.push({ name: 'admin.tasks.index' })
  }).catch((err) => {
    errors.form = err
  }).finally(() => {
    data.loading = false
  })

}

const checkPasswordRules = () => {
  let errorMsg = 'Password fields are not matching.'

  if (form.password.length && form.password_confirmation.length && (form.password != form.password_confirmation)) {
    errors.form.password = [errorMsg]
    return
  }

  delete errors.form.password
}

</script>

<style scoped></style>
