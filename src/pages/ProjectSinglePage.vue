<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { resolveAssetUrl } from '../config/api'
import { useSiteStore } from '../stores/site'

function splitTitle(value, fallback) {
  const parts = String(value || fallback).split('|')
  return {
    before: parts[0] || '',
    highlight: parts[1] || '',
    after: parts[2] || '',
  }
}

const route = useRoute()
const siteStore = useSiteStore()
const pageRoot = ref(null)

const servicesPageContent = computed(() => siteStore.servicesPageContent ?? {})
const sidebarCta = computed(() => servicesPageContent.value?.sidebar_cta ?? {})
const contactPhone = computed(() => siteStore.phone || sidebarCta.value?.phone_text || '')
const contactPhoneUrl = computed(() => {
  if (siteStore.contactInfo?.phone) {
    const normalizedPhone = String(siteStore.contactInfo.phone).replace(/[^\d+]/g, '')
    return normalizedPhone ? `tel:${normalizedPhone}` : '#'
  }

  return sidebarCta.value?.phone_url || '#'
})
const globalFaqItems = computed(() => {
  const items = siteStore.faq

  if (!Array.isArray(items)) {
    return []
  }

  return items.filter((item) => (item?.status ?? 'Published') === 'Published')
})
const allProjects = computed(() => siteStore.projectsPageItems ?? [])
const project = computed(() => siteStore.findItemBySlug('projects', route.params.slug, { flag: 'show_on_projects_page' }) ?? allProjects.value[0] ?? null)
const tickerLabels = computed(() => {
  const labels = allProjects.value.map((item) => item?.name).filter(Boolean)

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
const projectImage = computed(() => resolveAssetUrl(project.value?.list_image_url || project.value?.home_image_url))
const overviewParagraphs = computed(() => {
  const values = [project.value?.about_text, project.value?.short_description].filter(Boolean)

  return values.length
    ? values
    : [
        'Innovative identity design goes beyond visual decoration. It builds a coherent system that expresses positioning, trust, and differentiation across every brand touchpoint.',
        'A stronger identity improves recognition, supports consistency, and gives the business a clearer foundation for long-term growth.',
      ]
})
const factsTitle = computed(() => splitTitle(project.value?.facts_title, 'Challenges &|constraints'))
const resultTitle = computed(() => splitTitle(project.value?.results_title, 'Projects|solution'))
const projectFacts = computed(() => Array.isArray(project.value?.facts_list) ? project.value.facts_list.filter(Boolean) : [])
const resultParagraphs = computed(() => {
  const values = [
    project.value?.results_text,
    ...((Array.isArray(project.value?.results_items) ? project.value.results_items : [])
      .map((item) => item?.description)
      .filter(Boolean)
      .slice(0, 2)),
  ].filter(Boolean)

  return values.length
    ? values.slice(0, 3)
    : [
        'Our project solution aligned identity, message, and usability into one cohesive execution that improved recognition and delivery confidence.',
        'The output supported stronger presentation, clearer stakeholder alignment, and a more focused direction for launch and growth.',
      ]
})
const successRate = computed(() => {
  const resultCount = Array.isArray(project.value?.results_items) ? project.value.results_items.length : 0
  const factCount = projectFacts.value.length
  return Math.min(99, 80 + resultCount * 5 + factCount * 2)
})
const projectMeta = computed(() => [
  { label: 'project name', value: project.value?.name || 'innovative identity design' },
  { label: 'Category', value: project.value?.category || 'digital marketing' },
  { label: 'clients', value: project.value?.client_name || 'josefin H. smith' },
  { label: 'date', value: project.value?.project_date || '25 January, 2023' },
  { label: 'duration', value: project.value?.project_type || '6 month' },
])

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

  const  Elements = pageRoot.value?.querySelectorAll('.project-single-image . ')
   Elements?.forEach((element) => {
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
  () => project.value?.slug,
  async () => {
    await reinitializePageScripts()
  },
)
</script>

<template>
  <div v-if="project" ref="pageRoot">
    <div class="page-header">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-12">
            <div class="page-header-box">
              <h1 class="text-anime-style-2" data-cursor="-opaque">
                {{ project.name }} <span>Project</span>
              </h1>
              <nav class="wow fadeInUp">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><RouterLink to="/">home</RouterLink></li>
                  <li class="breadcrumb-item"><RouterLink to="/projects">portfolio</RouterLink></li>
                  <li class="breadcrumb-item active" aria-current="page">{{ project.name }}</li>
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
          <span v-for="(label, index) in tickerLabels" :key="`project-ticker-primary-${index}`"><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}</span>
          <span v-for="(label, index) in tickerLabels" :key="`project-ticker-primary-repeat-${index}`"><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}</span>
        </div>

        <div class="scrolling-content">
          <span v-for="(label, index) in tickerLabels" :key="`project-ticker-secondary-${index}`"><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}</span>
          <span v-for="(label, index) in tickerLabels" :key="`project-ticker-secondary-repeat-${index}`"><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}</span>
        </div>
      </div>
    </div>

    <div class="page-project-single">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <div class="project-single-content">
              <div class="project-single-image">
                <figure class="image-anime  ">
                  <img :src="projectImage" :alt="project.name">
                </figure>
              </div>

              <div class="project-entry">
                <div class="project-info">
                  <h2 class="text-anime-style-2">Project <span>overview</span></h2>
                  <p
                    v-for="(paragraph, index) in overviewParagraphs"
                    :key="`overview-${index}`"
                    class="wow fadeInUp"
                    :data-wow-delay="index ? `${(index + 1) * 0.2}s` : '0.2s'"
                  >
                    {{ paragraph }}
                  </p>
                </div>

                <div class="project-challenges">
                  <h2 class="text-anime-style-2">{{ factsTitle.before }} <span>{{ factsTitle.highlight }}</span>{{ factsTitle.after ? ` ${factsTitle.after}` : '' }}</h2>
                  <p class="wow fadeInUp" data-wow-delay="0.2s">{{ project.facts_text }}</p>

                  <ul class="wow fadeInUp" data-wow-delay="0.4s">
                    <li v-for="(item, index) in projectFacts" :key="`fact-${index}`">{{ item }}</li>
                  </ul>
                </div>
                
                <div class="project-solution">
                  <h2 class="text-anime-style-2">{{ resultTitle.before }} <span>{{ resultTitle.highlight }}</span>{{ resultTitle.after ? ` ${resultTitle.after}` : '' }}</h2>
                  <p class="wow fadeInUp" data-wow-delay="0.2s">{{ resultParagraphs[0] }}</p>

                  <div class="project-solution-rating">
                    <div class="project-rating-content">
                      <p
                        v-for="(paragraph, index) in resultParagraphs.slice(1)"
                        :key="`result-paragraph-${index}`"
                        class="wow fadeInUp"
                        :data-wow-delay="index ? '0.6s' : '0.4s'"
                      >
                        {{ paragraph }}
                      </p>
                    </div>

                    <div class="project-rating-counter">
                      <div class="icon-box">
                        <i class="fa-solid fa-star"></i>
                      </div>

                      <div class="project-counter-content">
                        <h3><span class="counter">{{ successRate }}</span>%</h3>
                        <p>Success Rate</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="our-faq-section">
                <div class="section-title">
                  <h2 class="text-anime-style-2" data-cursor="-opaque">Lets address your <span>questions</span> today!</h2>
                </div>

                <div class="faq-accordion" id="faqaccordion">
                  <div v-for="(faq, index) in globalFaqItems" :key="`project-faq-${index}`" class="accordion-item wow fadeInUp" :data-wow-delay="index ? `${index * 0.2}s` : null">
                    <h2 class="accordion-header" :id="`project-heading${index + 1}`">
                      <button
                        class="accordion-button"
                        :class="{ collapsed: index !== 1 }"
                        type="button"
                        data-bs-toggle="collapse"
                        :data-bs-target="`#project-collapse${index + 1}`"
                        :aria-expanded="index === 1 ? 'true' : 'false'"
                        :aria-controls="`project-collapse${index + 1}`"
                      >
                        {{ faq.question }}
                      </button>
                    </h2>
                    <div
                      :id="`project-collapse${index + 1}`"
                      class="accordion-collapse collapse"
                      :class="{ show: index === 1 }"
                      :aria-labelledby="`project-heading${index + 1}`"
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
            <div class="project-sidebar">
              <div class="project-catagery-list wow fadeInUp">
                <div class="category-item-list">
                  <div v-for="item in projectMeta" :key="item.label" class="category-list-item">
                    <h3>{{ item.label }}</h3>
                    <p>{{ item.value }}</p>
                  </div>
                </div>
                
                <div class="category-social-link">
                  <span>Share:</span>
                  <ul>
                    <li v-for="(item, index) in siteStore.activeSocialLinks.slice(0, 4)" :key="`project-social-${index}`">
                      <a :href="item.url" target="_blank" rel="noreferrer"><i :class="item.icon"></i></a>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="sidebar-cta-box wow fadeInUp" data-wow-delay="0.25s">
                <div class="icon-box">
                  <img :src="resolveAssetUrl(sidebarCta.icon_url)" alt="">
                </div>

                <div class="cta-contact-content">
                  <h3>{{ sidebarCta.title || 'You have different questions?' }}</h3>
                  <p>{{ sidebarCta.description || 'Our team will answer all your questions. we ensure a quick response.' }}</p>
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
