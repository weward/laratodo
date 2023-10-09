import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'
import { notify } from 'src/config/elements'

export const useAuthStore = defineStore('authStore', {
  state: () => ({
    user: {
      name: JSON.parse(localStorage.getItem('authUser'))?.name ?? '',
      email: JSON.parse(localStorage.getItem('authUser'))?.email ?? '',
    },
    authToken: null,
  }),
  getters: {
    doubleCount: (state) => state.counter * 2,
    getAuthToken: (state) => state.authToken,
    getUser: (state) => state.user,
  },
  actions: {
    login(form) {
      return new Promise((resolve, reject) => {
        api.post('auth/login', JSON.stringify(form), {
          headers: {
            'Content-Type': 'application/json',
          }
        }).then((res) => {
          localStorage.setItem('authToken', res.data.token)
          localStorage.setItem('authUser', JSON.stringify(res.data.user))

          this.authToken = res.data.token
          resolve()
        }).catch((err) => {
          if ([404, 500].includes(err.response.status)) {
            notify(err.response?.data)
          }
          if (err.response.status == 422) {
            reject(err.response.data.errors)
          }

          reject()
        })
      })
    },
    register(form) {
      return new Promise((resolve, reject) => {
        api.post('auth/register', JSON.stringify(form), {
          headers: {
            'Content-Type': 'application/json',
          }
        }).then((res) => {
          localStorage.setItem('authToken', res.data.token)
          localStorage.setItem('authUser', JSON.stringify(res.data.user))

          this.authToken = res.data.token
          resolve()
        }).catch((err) => {
          if ([404, 500].includes(err.response.status)) {
            notify(err.response?.data)
          }

          if (err.response.status == 422) {
            reject(err.response.data.errors)
          }

          reject()
        })
      })
    },

  },
});
