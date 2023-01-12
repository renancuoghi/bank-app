import { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('pages/HomePage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'user',
        },
      },
    ],
  },
  {
    path: '/auth',
    component: () => import('layouts/LoginLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('pages/User/LoginPage.vue'),
      },
      {
        path: 'register',
        name: 'register',
        component: () => import('pages/User/RegisterPage.vue'),
      },
    ],
  },

  {
    path: '/expenses',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'expenses',
        component: () => import('pages/Balance/ExpensesPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'user',
        },
      },
      {
        path: 'purchase',
        name: 'purchase',
        component: () => import('pages/Balance/PurchasePage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'user',
        },
      },
    ],
  },

  {
    path: '/checks',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'checks',
        component: () => import('pages/Balance/ChecksPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'user',
        },
      },
      {
        path: 'deposit',
        name: 'deposit',
        component: () => import('pages/Balance/CheckdepositPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'user',
        },
      },
    ],
  },

  {
    path: '/admin',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: 'home',
        name: 'admin-root',
        component: () => import('pages/Balance/PendingPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'admin',
        },
      },
      {
        path: 'checkdetail/:id',
        name: 'checkdetail',
        component: () => import('pages/Balance/CheckdetailsPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'admin',
        },
      },
    ],
  },

  // PendingPage

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
