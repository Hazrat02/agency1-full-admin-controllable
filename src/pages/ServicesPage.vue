<script setup>
import { computed } from 'vue'
import { resolveAssetUrl } from '../config/api'
import { useSiteStore } from '../stores/site'

const siteStore = useSiteStore()
const servicesPageContent = computed(() => siteStore.servicesPageContent ?? {})
const workingProcessContent = computed(() => siteStore.workingProcess ?? {})
const pageHeader = computed(() => servicesPageContent.value?.page_header ?? {})
const servicesSection = computed(() => servicesPageContent.value?.services_section ?? {})
const serviceItems = computed(() => {
  const items = servicesPageContent.value?.items

  if (Array.isArray(items) && items.length) {
    return [...items]
      .filter((item) => (item?.status ?? 'Active') === 'Active')
      .sort((first, second) => (first?.sort_order ?? 0) - (second?.sort_order ?? 0))
  }

  return []
})
const workingProcessItems = computed(() => {
  const items = workingProcessContent.value?.items

  if (Array.isArray(items) && items.length) {
    return items
  }

  return []
})
</script>

<template>
<div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box">
                    <h1 class="text-anime-style-2" data-cursor="-opaque">{{ pageHeader.title_before || 'Our' }} <span>{{ pageHeader.title_highlight || 'Services' }}</span></h1>
                    <ol class="breadcrumb wow fadeInUp">
                        <li class="breadcrumb-item"><a :href="pageHeader.breadcrumb_home_url || '/'">{{ pageHeader.breadcrumb_home_label || 'home' }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ pageHeader.breadcrumb_current_label || 'services' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Our Services Section Start -->
    <div class="our-services">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-7">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">{{ servicesSection.subtitle || 'Our services' }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{{ servicesSection.title_before || 'Our' }} <span>{{ servicesSection.title_highlight || 'digital services' }}</span> {{ servicesSection.title_after || 'to grow your brand' }}</h2>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-5">
                    <!-- Section Content Button Start -->
                    <div class="section-content-btn">
                        <!-- Section Title Content Start -->
                        <div class="section-title-content wow fadeInUp" data-wow-delay="0.2s">
                            <p>{{ servicesSection.description || 'Our digital services empower brands with innovative strategies and solutions for sustainable growth and engagement.' }}</p>
                        </div>
                        <!-- Section Title Content End -->

                        <!-- Section Button Start -->
                        <div class="section-btn wow fadeInUp" data-wow-delay="0.4s">
                            <a :href="servicesSection.cta_url || '/services'" class="btn-default">{{ servicesSection.cta_text || 'all services' }}</a>
                        </div>
                        <!-- Section Button End -->
                    </div>   
                    <!-- Section Content Button End -->                 
                </div>
            </div>

            <div class="row">
                <div
                    v-for="(item, index) in serviceItems"
                    :key="`services-page-item-${index}`"
                    class="col-lg-4 col-md-6"
                >
                    <!-- Service Item Start -->
                    <div class="service-item wow fadeInUp" :data-wow-delay="index ? `${index * 0.2}s` : null">
                        <!-- Service Item Header Start -->
                        <div class="service-item-header">
                            <div class="icon-box">
                                <img :src="resolveAssetUrl(item.card_icon_url)" :alt="item.name">
                            </div>

                            <div class="service-arrow">
                                <a :href="`/services/${item.slug}`"><img src="https://html.awaikenthemes.com/artistic/images/arrow-accent.svg" alt=""></a>
                            </div>
                        </div>
                        <!-- Service Item Header End -->

                        <!-- Service Item Body Start -->
                        <div class="service-item-body">
                            <h3>{{ item.name }}</h3>
                            <p>{{ item.short_description }}</p>
                        </div>
                        <!-- Service Item Body End -->
                    </div>
                    <!-- Service Item End -->
                </div>

                <div class="col-lg-12">
                    <div class="service-footer wow fadeInUp" data-wow-delay="0.25s">
                        <p>{{ servicesSection.footer_text || "Let's make something great work together." }} <a :href="servicesSection.footer_link_url || '/contact'">{{ servicesSection.footer_link_text || 'get free quote' }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Our Services Section End -->

    <!-- Digital Success Section Start -->
    <!-- How It Work Section Start -->
    <div class="how-it-work">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-7">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">{{ workingProcessContent.subtitle || 'how it work' }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{{ workingProcessContent.title_before || 'Our proven' }} <span>{{ workingProcessContent.title_highlight || 'process' }}</span> {{ workingProcessContent.title_after || 'for achieving success' }}</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
                <div class="col-lg-5">
                    <!-- Section Title Content Start -->
                    <div class="section-title-content wow fadeInUp" data-wow-delay="0.25s">
                        <p>{{ workingProcessContent.description || 'Our proven process combines research, strategy, and creativity to deliver tailored solutions that drive measurable results.' }}</p>
                    </div>
                    <!-- Section Title Content End -->
                </div>
            </div>

            <div class="row">
                <div
                    v-for="(item, index) in workingProcessItems"
                    :key="`working-process-${index}`"
                    class="col-lg-4 col-md-6"
                >
                    <!-- Work Process Item Start -->
                    <div class="work-process-item wow fadeInUp" :data-wow-delay="index ? `${index * 0.25}s` : null">
                        <!-- Work Process Header Start -->
                        <div class="work-process-header">
                            <!-- Work Process Title Start -->
                            <div class="work-process-title">
                                <h3>{{ item.title }}</h3>
                            </div>
                            <!-- Work Process Title End -->

                            <!-- Work Process Button Start -->
                            <div class="work-process-btn">
                                <a :href="item.link_url || '/contact'" class="readmore-btn"><img src="https://html.awaikenthemes.com/artistic/images/arrow-white.svg" alt=""></a>
                            </div>
                            <!-- Work Process Button End -->
                        </div>
                        <!-- Work Process Header End -->

                        <!-- Work Process Content Start -->
                        <div class="work-process-content">
                            <p>{{ item.description }}</p>
                        </div>
                        <!-- Work Process Content End -->

                        <!-- Work Process Body Start -->
                        <div class="work-process-body">
                            <!-- Work Process Number Start -->
                            <div class="work-process-no">
                                <h3>step</h3>
                                <h2>{{ `0${index + 1}`.slice(-2) }}</h2>
                            </div>
                            <!-- Work Process Number End -->

                            <!-- Work Process Icon Box Start -->
                            <div class="work-process-icon-box">
                                <img :src="resolveAssetUrl(item.icon_url)" :alt="item.title">
                            </div>
                            <!-- Work Process Icon Box End -->
                        </div>
                        <!-- Work Process Body End -->
                    </div>
                    <!-- Work Process Item End -->
                </div>
            </div>
        </div>
    </div>
    <!-- How It Work Section End -->

    <!-- Our Features Start -->
    <div class="our-features">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-6">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">features</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">Innovative <span>features</span> for your digital success</h2>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-6">
                    <!-- Section Content Button Start -->
                    <div class="section-content-btn">
                        <!-- Section Title Content Start -->
                        <div class="section-title-content wow fadeInUp" data-wow-delay="0.2s">
                            <p>Our digital services empower brands with innovative strategies and solutions for sustainable growth and engagement.</p>
                        </div>
                        <!-- Section Title Content End -->

                        <!-- Section Button Start -->
                        <div class="section-btn wow fadeInUp" data-wow-delay="0.4s">
                            <a href="/contact" class="btn-default">leran more</a>
                        </div>
                        <!-- Section Button End -->
                    </div>   
                    <!-- Section Content Button End -->
                </div>
            </div>

            <div class="col-lg-12">
                <!-- Digital Features Box Start -->
                <div class="digital-features-box">
                    <!-- Digital Features Item Start -->
                    <div class="digital-features-item features-item-1 wow fadeInUp">
                        <!-- Digital Features Image Start -->
                        <div class="digital-features-image">
                            <figure class="image-anime">
                                <img src="https://html.awaikenthemes.com/artistic/images/digital-features-img-1.jpg" alt="">
                            </figure>
                        </div>
                        <!-- Digital Features Image End -->

                        <!-- Digital Features Content Start -->
                        <div class="digital-features-content">
                            <h3>custom branding solutions</h3>
                            <p>Unique brand identity development, including logos, color palettes.</p>
                        </div>
                        <!-- Digital Features Content End -->
                    </div>
                    <!-- Digital Features Item End -->

                    <!-- Digital Features Item Start -->
                    <div class="digital-features-item features-item-2 wow fadeInUp" data-wow-delay="0.25s">
                        <!-- Digital Features Image Start -->
                        <div class="digital-features-image">
                            <figure class="image-anime">
                                <img src="https://html.awaikenthemes.com/artistic/images/digital-features-img-2.jpg" alt="">
                            </figure>
                        </div>
                        <!-- Digital Features Image End -->

                        <!-- Digital Features Content Start -->
                        <div class="digital-features-content">
                            <h3>data-driven digital marketing</h3>
                            <p>Strategies combining SEO, PPC, content marketing</p>
                        </div>
                        <!-- Digital Features Content End -->
                    </div>
                    <!-- Digital Features Item End -->  
                    
                </div>
                <!-- Digital Features Box End -->
            </div>
        </div>
    </div>
    <!-- Our Features End -->
</template>
