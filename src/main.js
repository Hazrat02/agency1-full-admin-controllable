import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import AboutPage from './pages/AboutPage.vue'
import ContactPage from './pages/ContactPage.vue'
import HomePage from './pages/HomePage.vue'
import ProjectsPage from './pages/ProjectsPage.vue'
import ProjectSinglePage from './pages/ProjectSinglePage.vue'
import ServiceSinglePage from './pages/ServiceSinglePage.vue'
import ServicesPage from './pages/ServicesPage.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: HomePage, meta: { page: 'home' } },
    { path: '/about', name: 'about', component: AboutPage, meta: { page: 'about' } },
    { path: '/projects', name: 'projects', component: ProjectsPage, meta: { page: 'projects' } },
    { path: '/projects/:slug', name: 'project-single', component: ProjectSinglePage, meta: { page: 'projects' } },
    { path: '/services', name: 'services', component: ServicesPage, meta: { page: 'services' } },
    { path: '/services/:slug', name: 'service-single', component: ServiceSinglePage, meta: { page: 'services' } },
    { path: '/contact', name: 'contact', component: ContactPage, meta: { page: 'contact' } },
  ],
})

createApp(App).use(createPinia()).use(router).mount('#app')
