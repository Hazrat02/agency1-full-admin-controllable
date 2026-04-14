<script setup>
import { computed, nextTick, onMounted } from 'vue'
import { RouterView, useRoute, useRouter } from 'vue-router'
import SiteFooter from './components/SiteFooter.vue'
import SiteHeader from './components/SiteHeader.vue'
import { useSiteStore } from './stores/site'
import { initTemplateScripts } from './utils/initTemplateScripts'

const router = useRouter()
const route = useRoute()
const siteStore = useSiteStore()
const siteReady = computed(() => siteStore.initialized || Boolean(siteStore.error))
const siteLoading = computed(() => !siteReady.value)
const siteBootstrapPromise = siteStore.initialize().catch(() => {})

function handleInternalLinks(event) {
  const link = event.target.closest('a[href]')
  if (!link) return

  const href = link.getAttribute('href')
  if (!href || href.startsWith('http') || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
    return
  }

  if (href.startsWith('/')) {
    event.preventDefault()
    router.push(href)
  }
}

onMounted(async () => {
  document.addEventListener('click', handleInternalLinks)
  await siteBootstrapPromise
  await nextTick()
  await initTemplateScripts()
})

router.afterEach(async () => {
  window.scrollTo(0, 0)
  await siteBootstrapPromise
  await nextTick()
  await initTemplateScripts()
})
</script>

<template>
  <div>
    <div v-if="siteLoading" class="preloader">
      <div class="loading-container">
        <div class="loading"></div>
        <div id="loading-icon"><img src="/images/loader.svg" alt="Loading icon"></div>
      </div>
    </div>

    <template v-if="siteReady">
    <SiteHeader :key="`header-${route.fullPath}`" />
    <main>
      <RouterView :key="route.fullPath" />
    </main>
    <SiteFooter :key="`footer-${route.fullPath}`" />
    </template>
  </div>
</template>
