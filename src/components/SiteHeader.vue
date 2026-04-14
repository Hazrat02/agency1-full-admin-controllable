<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useSiteStore } from '../stores/site'

const route = useRoute()
const siteStore = useSiteStore()
const socialLinks = computed(() => siteStore.activeSocialLinks.slice(0, 4))
const logoUrl = computed(() => siteStore.logoUrl || 'https://html.awaikenthemes.com/artistic/images/logo.svg')
const navigationLinks = computed(() => siteStore.activeSidebarLinks)

function isInternalLink(url) {
  return typeof url === 'string' && /^\/(?!\/)/.test(url)
}

function isLinkActive(url) {
  if (!isInternalLink(url)) {
    return false
  }

  const normalizedUrl = url.replace(/\/+$/, '') || '/'
  const normalizedPath = route.path.replace(/\/+$/, '') || '/'

  return normalizedPath === normalizedUrl
}
</script>

<template>
  <header class="main-header">
    <div class="header-sticky">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <RouterLink class="navbar-brand" to="/">
            <img style="width: 200px;" :src="logoUrl" :alt="siteStore.siteName">
          </RouterLink>

          <div class="collapse navbar-collapse main-menu">
            <div class="nav-menu-wrapper">
              <ul class="navbar-nav mr-auto" id="menu">
                <li v-for="link in navigationLinks" :key="`header-link-${link.label}-${link.sort_order}`" class="nav-item">
                  <RouterLink
                    v-if="isInternalLink(link.url)"
                    class="nav-link"
                    :class="{ active: isLinkActive(link.url) }"
                    :to="link.url"
                  >
                    {{ link.label }}
                  </RouterLink>
                  <a
                    v-else
                    class="nav-link"
                    :href="link.url"
                    :target="/^https?:\/\//i.test(link.url) ? '_blank' : null"
                    :rel="/^https?:\/\//i.test(link.url) ? 'noopener noreferrer' : null"
                  >
                    {{ link.label }}
                  </a>
                </li>
              </ul>
            </div>

            <div class="header-social-box d-inline-flex">
              <div v-if="socialLinks.length" class="header-social-links">
                <ul>
                  <li v-for="link in socialLinks" :key="`header-social-${link.name}-${link.sort_order}`">
                    <a :href="link.url" target="_blank" rel="noopener noreferrer" :aria-label="link.name">
                      <i :class="link.icon"></i>
                    </a>
                  </li>
                </ul>
              </div>

              <div class="header-btn">
                <button class="btn btn-popup" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                  <img src="https://html.awaikenthemes.com/artistic/images/header-btn-dot.svg" alt="Open menu">
                </button>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight">
                  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>

                  <div class="offcanvas-body">
                    <div class="header-contact-box">
                      <div class="icon-box">
                        <img src="https://html.awaikenthemes.com/artistic/images/icon-phone.svg" alt="Phone icon">
                      </div>
                      <div class="header-contact-box-content">
                        <h3>phone</h3>
                        <p>{{ siteStore.phone || '-' }}</p>
                      </div>
                    </div>

                    <div class="header-contact-box">
                      <div class="icon-box">
                        <img src="https://html.awaikenthemes.com/artistic/images/icon-mail.svg" alt="Mail icon">
                      </div>
                      <div class="header-contact-box-content">
                        <h3>email</h3>
                        <p>{{ siteStore.email || '-' }}</p>
                      </div>
                    </div>

                    <div class="header-contact-box">
                      <div class="icon-box">
                        <img src="https://html.awaikenthemes.com/artistic/images/icon-location.svg" alt="Location icon">
                      </div>
                      <div class="header-contact-box-content">
                        <h3>address</h3>
                        <p>{{ siteStore.address || '-' }}</p>
                      </div>
                    </div>

                    <div v-if="socialLinks.length" class="header-social-links sidebar-social-links">
                      <h3>stay connected</h3>
                      <ul>
                        <li v-for="link in socialLinks" :key="`sidebar-social-${link.name}-${link.sort_order}`">
                          <a :href="link.url" target="_blank" rel="noopener noreferrer" :aria-label="link.name">
                            <i :class="link.icon"></i>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="navbar-toggle"></div>
        </div>
      </nav>
      <div class="responsive-menu"></div>
    </div>
  </header>
</template>
