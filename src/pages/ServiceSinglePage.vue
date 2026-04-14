<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { resolveAssetUrl } from '../config/api'
import { useSiteStore } from '../stores/site'

const route = useRoute()
const siteStore = useSiteStore()
const pageRoot = ref(null)

const servicesPageContent = computed(() => siteStore.servicesPageContent ?? {})
const pageHeader = computed(() => servicesPageContent.value?.page_header ?? {})
const globalFaqItems = computed(() => {
  const items = siteStore.faq

  if (!Array.isArray(items)) {
    return []
  }

  return items.filter((item) => (item?.status ?? 'Published') === 'Published')
})
const sidebarCta = computed(() => servicesPageContent.value?.sidebar_cta ?? {})
const contactPhone = computed(() => siteStore.phone || sidebarCta.value?.phone_text || '')
const contactPhoneUrl = computed(() => {
  if (siteStore.contactInfo?.phone) {
    const normalizedPhone = String(siteStore.contactInfo.phone).replace(/[^\d+]/g, '')
    return normalizedPhone ? `tel:${normalizedPhone}` : '#'
  }

  return sidebarCta.value?.phone_url || '#'
})
const allServices = computed(() => {
  const items = servicesPageContent.value?.items

  if (!Array.isArray(items)) {
    return []
  }

  return [...items]
    .filter((item) => (item?.status ?? 'Active') === 'Active')
    .sort((first, second) => (first?.sort_order ?? 0) - (second?.sort_order ?? 0))
})

const service = computed(() => {
  const slug = route.params.slug
  return allServices.value.find((item) => item?.slug === slug) ?? allServices.value[0] ?? null
})

const tickerLabels = computed(() => {
  const labels = allServices.value.map((item) => item?.name).filter(Boolean)

  if (labels.length) {
    return labels
  }

  return [
    'Custom Branding',
    'Website Design',
    'Digital Marketing',
    'Strategy Consulting',
    'Analytics & Reporting',
  ]
})

const serviceProcessSteps = computed(() => Array.isArray(service.value?.process_steps) ? service.value.process_steps : [])
const serviceFeatures = computed(() => Array.isArray(service.value?.features_list) ? service.value.features_list.filter(Boolean) : [])

async function reinitializePageScripts() {
  await nextTick()
  const images = Array.from(pageRoot.value?.querySelectorAll('img') ?? [])

  await Promise.all(
    images.map((image) => {
      if (image.complete) {
        return Promise.resolve()
      }

      return new Promise((resolve) => {
        image.addEventListener('load', resolve, { once: true })
        image.addEventListener('error', resolve, { once: true })
      })
    }),
  )

  if (window.ScrollTrigger?.refresh) {
    window.ScrollTrigger.refresh()
  }

  if (window.WOW) {
    new window.WOW().init()
  }

  const revealElements = pageRoot.value?.querySelectorAll('.service-feature-image .reveal, .service-entry-image .reveal, .process-step-image .reveal')
  revealElements?.forEach((element) => {
    element.style.removeProperty('transform')
    element.style.removeProperty('opacity')
    element.style.removeProperty('visibility')

    const image = element.querySelector('img')
    image?.style.removeProperty('transform')
    image?.style.removeProperty('opacity')
    image?.style.removeProperty('visibility')
  })
}

onMounted(async () => {
  await reinitializePageScripts()
})

watch(
  () => service.value?.slug,
  async () => {
    await reinitializePageScripts()
  },
)
</script>

<template>
    <div v-if="service" ref="pageRoot">
        <div class="page-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="page-header-box">
                            <h1 class="text-anime-style-2" data-cursor="-opaque">
                                {{ pageHeader.title_before || 'Our' }} <span>{{ service.name }}</span>
                            </h1>
                            <nav class="wow fadeInUp">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><RouterLink to="/">{{ pageHeader.breadcrumb_home_label || 'home' }}</RouterLink></li>
                                    <li class="breadcrumb-item"><RouterLink to="/services">{{ pageHeader.breadcrumb_current_label || 'services' }}</RouterLink></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ service.name }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="our-scrolling-ticker subpages-scrolling-ticker">
            <div class="scrolling-ticker-box">
                <div class="scrolling-content">
                    <span v-for="(label, index) in tickerLabels" :key="`ticker-primary-${index}`"><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}</span>
                    <span v-for="(label, index) in tickerLabels" :key="`ticker-primary-repeat-${index}`"><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}</span>
                </div>

                <div class="scrolling-content">
                    <span v-for="(label, index) in tickerLabels" :key="`ticker-secondary-${index}`"><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}</span>
                    <span v-for="(label, index) in tickerLabels" :key="`ticker-secondary-repeat-${index}`"><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}</span>
                </div>
            </div>
        </div>

        <div class="page-service-single">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="service-single-content">
                            <div class="service-feature-image">
                                <figure class="image-anime">
                                    <img :src="resolveAssetUrl(service.detail_feature_image_url)" :alt="service.name">
                                </figure>
                            </div>

                            <div class="service-entry">
                                <p class="wow fadeInUp">{{ service.intro_paragraph_1 }}</p>

                                <p class="wow fadeInUp" data-wow-delay="0.2s">{{ service.intro_paragraph_2 }}</p>

                                <h2 class="text-anime-style-2">{{ service.key_features_title_before }} <span>{{ service.key_features_title_highlight }}</span> {{ service.key_features_title_after }}</h2>

                                <p class="wow fadeInUp">{{ service.key_features_paragraph_1 }}</p>

                                <p class="wow fadeInUp" data-wow-delay="0.2s">{{ service.key_features_paragraph_2 }}</p>

                                <div class="service-entry-list-image">
                                    <div class="service-entry-list wow fadeInUp" data-wow-delay="0.4s">
                                        <ul>
                                            <li v-for="(feature, index) in serviceFeatures" :key="`feature-${index}`">{{ feature }}</li>
                                        </ul>
                                    </div>

                                    <div class="service-entry-image">
                                        <figure class="image-anime">
                                            <img :src="resolveAssetUrl(service.feature_side_image_url)" :alt="service.name">
                                        </figure>
                                    </div>
                                </div>

                                <h2 class="text-anime-style-2">{{ service.process_title_before }} <span>{{ service.process_title_highlight }}</span> {{ service.process_title_after }}</h2>

                                <p class="wow fadeInUp">{{ service.process_description }}</p>

                                <div class="service-process-steps">
                                    <div v-for="(step, index) in serviceProcessSteps" :key="`process-${index}`" class="process-step-item">
                                        <div class="process-step-content wow fadeInUp" :data-wow-delay="`${(index + 1) * 0.2}s`">
                                            <div class="process-step-header">
                                                <div class="icon-box">
                                                    <img :src="resolveAssetUrl(step.icon_url)" :alt="step.title">
                                                </div>

                                                <div class="process-step-no">
                                                    <h3>step <span>{{ `0${index + 1}`.slice(-2) }}</span></h3>
                                                </div>
                                            </div>

                                            <div class="process-step-body">
                                                <h3>{{ step.title }}</h3>
                                                <p>{{ step.description }}</p>
                                            </div>
                                        </div>

                                        <div class="process-step-image">
                                            <figure class="image-anime">
                                                <img :src="resolveAssetUrl(step.image_url)" :alt="step.title">
                                            </figure>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="our-faq-section">
                                <div class="section-title">
                                    <h2 class="text-anime-style-2" data-cursor="-opaque">Lets address your <span>questions</span> today!</h2>
                                </div>

                                <div class="faq-accordion" id="faqaccordion">
                                    <div v-for="(faq, index) in globalFaqItems" :key="`faq-${index}`" class="accordion-item wow fadeInUp" :data-wow-delay="index ? `${index * 0.2}s` : null">
                                        <h2 class="accordion-header" :id="`heading${index + 1}`">
                                            <button
                                                class="accordion-button"
                                                :class="{ collapsed: index !== 1 }"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                :data-bs-target="`#collapse${index + 1}`"
                                                :aria-expanded="index === 1 ? 'true' : 'false'"
                                                :aria-controls="`collapse${index + 1}`"
                                            >
                                                {{ faq.question }}
                                            </button>
                                        </h2>
                                        <div
                                            :id="`collapse${index + 1}`"
                                            class="accordion-collapse collapse"
                                            :class="{ show: index === 1 }"
                                            :aria-labelledby="`heading${index + 1}`"
                                            data-bs-parent="#faqaccordion"
                                        >
                                            <div class="accordion-body">
                                                <p>{{ faq.answer }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="service-sidebar">
                            <div class="service-catagery-list wow fadeInUp">
                                <h3>services category</h3>
                                <ul>
                                    <li v-for="item in allServices" :key="item.slug">
                                        <RouterLink :to="`/services/${item.slug}`">{{ item.name }}</RouterLink>
                                    </li>
                                </ul>
                            </div>

                            <div class="sidebar-cta-box wow fadeInUp" data-wow-delay="0.25s">
                                <div class="icon-box">
                                    <img :src="resolveAssetUrl(sidebarCta.icon_url)" alt="">
                                </div>

                                <div class="cta-contact-content">
                                    <h3>You have different questions?</h3>
                                    <p>Our team will answer all your questions. we ensure a quick response.</p>
                                </div>

                                <div class="cta-contact-btn">
                                    <a :href="contactPhoneUrl"><img :src="resolveAssetUrl(sidebarCta.phone_icon_url)" alt=""> {{ contactPhone }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
