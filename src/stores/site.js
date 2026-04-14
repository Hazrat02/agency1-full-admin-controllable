import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { buildApiUrl, resolveAssetUrl } from '../config/api'

async function fetchJson(path) {
  const response = await fetch(buildApiUrl(path), {
    headers: {
      Accept: 'application/json',
    },
  })

  if (!response.ok) {
    throw new Error(`Request failed for ${path}: ${response.status}`)
  }

  return response.json()
}

export const useSiteStore = defineStore('site', () => {
  const content = ref({})
  const initialized = ref(false)
  const loading = ref(false)
  const error = ref('')
  let requestPromise = null

  function sortByOrder(items = []) {
    return [...items].sort((first, second) => (first?.sort_order ?? 0) - (second?.sort_order ?? 0))
  }

  function filterActive(items = []) {
    return items.filter((item) => (item?.status ?? 'Active') === 'Active')
  }

  function getContent(key, fallback = {}) {
    return content.value?.[key] ?? fallback
  }

  function getItems(key, options = {}) {
    const {
      path = 'items',
      requireActive = true,
      flag = null,
      limit = null,
      predicate = null,
    } = options

    const section = getContent(key, path === null ? [] : {})
    const source = path === null
      ? (Array.isArray(section) ? section : [])
      : (Array.isArray(section?.[path]) ? section[path] : [])
    let items = [...source]

    if (requireActive) {
      items = filterActive(items)
    }

    if (flag) {
      items = items.filter((item) => item?.[flag] ?? true)
    }

    if (typeof predicate === 'function') {
      items = items.filter(predicate)
    }

    items = sortByOrder(items)

    return typeof limit === 'number' ? items.slice(0, limit) : items
  }

  function findItemBySlug(key, slug, options = {}) {
    return getItems(key, options).find((item) => item?.slug === slug) ?? null
  }

  const generalSettings = computed(() => getContent('general_settings'))
  const socialLinks = computed(() => getContent('social_links', []))
  const contactInfo = computed(() => getContent('contact_info'))
  const sidebarLinks = computed(() => getContent('sidebar', []))
  const banners = computed(() => getContent('banner', []))
  const faq = computed(() => getContent('faq', []))
  const about = computed(() => getContent('about'))
  const mission = computed(() => getContent('mission'))
  const workingProcess = computed(() => getContent('working_process'))
  const whoWeAre = computed(() => getContent('who_we_are'))
  const homeFeatures = computed(() => getContent('home_features'))
  const whyChooseUs = computed(() => getContent('why_choose_us'))
  const testimonial = computed(() => getContent('testimonial'))
  const homeServicesContent = computed(() => getContent('home_services'))
  const servicesPageContent = computed(() => getContent('services_page'))
  const team = computed(() => getContent('team'))
  const projects = computed(() => getContent('projects'))

  const activeSocialLinks = computed(() => getItems('social_links', { path: null }).filter((item) => item?.url))

  const activeSidebarLinks = computed(() => getItems('sidebar', { path: null }).filter((item) => item?.url))

  const activeHomeBanners = computed(() =>
    getItems('banner', {
      path: null,
      predicate: (item) => (item?.position ?? 'Home Top') === 'Home Top',
    }),
  )

  const activeTestimonials = computed(() => getItems('testimonial'))
  const homeTeamMembers = computed(() => getItems('team', { flag: 'show_on_home', limit: 3 }))
  const teamPageItems = computed(() => getItems('team', { flag: 'show_on_team_page' }))
  const homeProjects = computed(() => getItems('projects', { flag: 'show_on_home', limit: 3 }))
  const projectsPageItems = computed(() => getItems('projects', { flag: 'show_on_projects_page' }))
  const primaryHomeBanner = computed(() => activeHomeBanners.value[0] ?? null)
  const logoUrl = computed(() => resolveAssetUrl(generalSettings.value?.logo_url))
  const siteName = computed(() => generalSettings.value?.site_name || 'Site')
  const phone = computed(() => contactInfo.value?.phone || '')
  const email = computed(() => contactInfo.value?.email || generalSettings.value?.support_email || '')
  const address = computed(() => contactInfo.value?.address || '')
  const mapEmbedUrl = computed(() => contactInfo.value?.map_embed_url || '')

  async function initialize() {
    if (initialized.value) {
      return
    }

    if (requestPromise) {
      return requestPromise
    }

    loading.value = true
    error.value = ''

    requestPromise = fetchJson('content')
      .then((response) => {
        content.value = response?.data ?? {}
        initialized.value = true
      })
      .catch((requestError) => {
        error.value = requestError instanceof Error ? requestError.message : 'Failed to load site settings.'
        throw requestError
      })
      .finally(() => {
        loading.value = false
        requestPromise = null
      })

    return requestPromise
  }

  return {
    about,
    activeHomeBanners,
    activeSidebarLinks,
    activeSocialLinks,
    activeTestimonials,
    address,
    banners,
    contactInfo,
    content,
    email,
    error,
    faq,
    findItemBySlug,
    generalSettings,
    getContent,
    getItems,
    homeFeatures,
    homeProjects,
    homeTeamMembers,
    initialize,
    initialized,
    loading,
    logoUrl,
    mapEmbedUrl,
    mission,
    phone,
    primaryHomeBanner,
    projects,
    projectsPageItems,
    homeServicesContent,
    sidebarLinks,
    siteName,
    servicesPageContent,
    socialLinks,
    team,
    teamPageItems,
    testimonial,
    whoWeAre,
    whyChooseUs,
    workingProcess,
  }
})
