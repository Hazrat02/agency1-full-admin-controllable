<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { resolveAssetUrl } from '../config/api'
import { useSiteStore } from '../stores/site'

function normalizeCategory(value) {
  return String(value || '')
    .trim()
    .toLowerCase()
    .replace(/&/g, 'and')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

const siteStore = useSiteStore()
const projectItems = computed(() => siteStore.projectsPageItems ?? [])
const tickerLabels = computed(() => {
  const labels = projectItems.value.map((item) => item?.name).filter(Boolean)

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

const categories = computed(() => {
  const mapped = projectItems.value
    .map((item) => item?.category)
    .filter(Boolean)
    .map((label) => ({
      label,
      value: normalizeCategory(label),
    }))

  return mapped.filter((item, index, list) => list.findIndex((entry) => entry.value === item.value) === index)
})
</script>

<template>
  <div>
    <div class="page-header">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-12">
            <div class="page-header-box">
              <h1 class="text-anime-style-2" data-cursor="-opaque">Our <span>Portfolio</span></h1>
              <nav class="wow fadeInUp">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><RouterLink to="/">home</RouterLink></li>
                  <li class="breadcrumb-item active" aria-current="page">portfolio</li>
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
          <span v-for="(label, index) in tickerLabels" :key="`projects-ticker-primary-${index}`">
            <img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}
          </span>
          <span v-for="(label, index) in tickerLabels" :key="`projects-ticker-primary-repeat-${index}`">
            <img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}
          </span>
        </div>

        <div class="scrolling-content">
          <span v-for="(label, index) in tickerLabels" :key="`projects-ticker-secondary-${index}`">
            <img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}
          </span>
          <span v-for="(label, index) in tickerLabels" :key="`projects-ticker-secondary-repeat-${index}`">
            <img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">{{ label }}
          </span>
        </div>
      </div>
    </div>

    <div class="page-project">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12">
            <div class="our-Project-nav wow fadeInUp" data-wow-delay="0.25s">
              <ul>
                <li><a href="#" class="active-btn" data-filter="*">all</a></li>
                <li v-for="category in categories" :key="category.value">
                  <a href="#" :data-filter="`.${category.value}`">{{ category.label }}</a>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-lg-12">
            <div class="row project-item-boxes align-items-center">
              <div
                v-for="(item, index) in projectItems"
                :key="item.slug || `project-${index}`"
                class="col-lg-4 col-md-6 project-item-box"
                :class="normalizeCategory(item.category)"
              >
                <div class="project-item wow fadeInUp" :data-wow-delay="index ? `${(index % 3) * 0.2}s` : null">
                  <div class="project-image">
                    <RouterLink :to="`/projects/${item.slug}`" class="image-anime">
                      <figure>
                        <img :src="resolveAssetUrl(item.list_image_url || item.home_image_url)" :alt="item.name">
                      </figure>
                    </RouterLink>

                    <div class="project-tag">
                      <RouterLink :to="`/projects/${item.slug}`">{{ item.category }}</RouterLink>
                    </div>

                    <div class="project-btn">
                      <RouterLink :to="`/projects/${item.slug}`"><img src="https://html.awaikenthemes.com/artistic/images/arrow-white.svg" alt=""></RouterLink>
                    </div>
                  </div>

                  <div class="project-content">
                    <h3>{{ item.name }}</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
