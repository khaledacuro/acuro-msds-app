import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/', name: 'Home', component: () => import('../views/HomeView.vue') },
  { path: '/upload', name: 'Upload', component: () => import('../views/UploadView.vue') },
  { path: '/documents', name: 'Documents', component: () => import('../views/DocumentsView.vue') },
  {
    path: '/documents/:id',
    name: 'DocumentDetail',
    component: () => import('../views/DocumentDetailView.vue'),
    props: true // This enables the route param `id` to be passed as a prop to DocumentDetailView
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'PageNotFound',
    component: () => import('../views/PageNotFoundView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
