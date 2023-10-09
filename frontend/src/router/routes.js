
const routes = [
  {
    path: '/',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      { path:
        '/',
        redirect: 'auth/login'
      },
      {
        path: 'auth/login',
        name: 'auth.login',
        meta: {
          requiresAuth: false,
        },
        component: () => import('pages/auth/LoginPage.vue')
      },
      {
        path: 'auth/register',
        name: 'auth.registration',
        meta: {
          requiresAuth: false,
        },
        component: () => import('pages/auth/RegistrationPage.vue')
      },
    ]
  },
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    children: [
      {
        path: 'tasks',
        name: 'admin.tasks.index',
        meta: {
          requiresAuth: true,
        },
        component: () => import('pages/admin/TaskPage.vue')
      },
    ]
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
