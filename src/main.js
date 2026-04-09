import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import AboutPage from './pages/AboutPage.vue'
import ContactPage from './pages/ContactPage.vue'
import HomePage from './pages/HomePage.vue'
import ServicesPage from './pages/ServicesPage.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: HomePage, meta: { page: 'home' } },
    { path: '/about', name: 'about', component: AboutPage, meta: { page: 'about' } },
    { path: '/services', name: 'services', component: ServicesPage, meta: { page: 'services' } },
    { path: '/contact', name: 'contact', component: ContactPage, meta: { page: 'contact' } },
  ],
})

createApp(App).use(router).mount('#app')
