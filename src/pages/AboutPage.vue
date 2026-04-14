<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { resolveAssetUrl } from '../config/api'
import { useSiteStore } from '../stores/site'

function getCounterMeta(value) {
  const normalized = String(value ?? '').trim()
  const numeric = normalized.replace(/[^0-9.]/g, '')
  const hasDecimal = numeric.includes('.')
  const decimals = hasDecimal ? Math.max((numeric.split('.')[1] || '').length, 0) : 0
  const target = Number.parseFloat(numeric || '0')

  return {
    target: Number.isFinite(target) ? target : 0,
    decimals,
  }
}

function formatCounterValue(value, decimals) {
  if (!Number.isFinite(value)) {
    return '0'
  }

  if (decimals > 0) {
    return value.toFixed(decimals)
  }

  return String(Math.round(value))
}

const siteStore = useSiteStore()
const aboutContent = computed(() => siteStore.about ?? {})
const contactInfo = computed(() => siteStore.contactInfo ?? {})
const faqContent = computed(() => siteStore.faq ?? [])
const homeFeaturesContent = computed(() => siteStore.homeFeatures ?? {})
const testimonialContent = computed(() => siteStore.testimonial ?? {})
const whoWeAreContent = computed(() => siteStore.whoWeAre ?? {})
const whyChooseUsContent = computed(() => siteStore.whyChooseUs ?? {})
const whoWeAreSection = ref(null)
const animatedWhoWeAreValues = ref([])
let whoWeAreObserver = null
let whoWeAreAnimationFrame = null
let whoWeAreAnimated = false

function splitTitleParts(value, fallback) {
  const parts = String(value || fallback).split('|')
  return {
    before: parts[0] || '',
    highlight: parts[1] || '',
    after: parts[2] || '',
  }
}

const pageTitleParts = computed(() => splitTitleParts(aboutContent.value?.page_title, 'About|us'))
const agencyTitleParts = computed(() =>
  splitTitleParts(aboutContent.value?.agency_title, 'Crafting|unique digital|experiences that elevate your brand'),
)
const aboutAgencyItems = computed(() => {
  const items = aboutContent.value?.agency_items

  if (Array.isArray(items) && items.length) {
    return items
  }

  return [
    {
      icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-about-agency-1.svg',
      title: 'your success, our mission',
      description: 'We measure our success by the success of our clients. With a focus on results and a dedication to quality, our mission is to deliver digital solutions that make a real impact.',
    },
    {
      icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-about-agency-2.svg',
      title: 'creators of digital excellence',
      description: 'At the core of our agency is a commitment to excellence and creativity. We specialize in crafting digital solutions that not only meet your needs but also elevate your brand.',
    },
    {
      icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-about-agency-3.svg',
      title: 'innovating the digital landscape',
      description: 'Founded on a passion for creativity and technology, we are a team of dedicated digital experts committed to transforming the way brands connect with audiences.',
    },
    {
      icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-about-agency-4.svg',
      title: 'helping brands thrive online',
      description: "Our purpose is simple: to help brands succeed in the digital age. We're passionate about building strong relationships with our clients and crafting custom strategies that drive results.",
    },
  ]
})
const pageHeaderStyle = computed(() =>
  aboutContent.value?.page_title_bg_url
    ? { backgroundImage: `url(${resolveAssetUrl(aboutContent.value.page_title_bg_url)})` }
    : undefined,
)
const testimonialItems = computed(() => {
  const items = testimonialContent.value?.items

  if (Array.isArray(items) && items.length) {
    return [...items]
      .filter((item) => (item?.status ?? 'Active') === 'Active')
      .sort((first, second) => (first?.sort_order ?? 0) - (second?.sort_order ?? 0))
  }

  return []
})
const testimonialReviewImages = computed(() => {
  const images = testimonialContent.value?.review_images

  if (Array.isArray(images) && images.length) {
    return images.filter(Boolean)
  }

  return []
})
const testimonialBenefits = computed(() => {
  const items = testimonialContent.value?.benefits

  if (Array.isArray(items) && items.length) {
    return items
  }

  return []
})
const faqItems = computed(() => {
  const items = faqContent.value

  if (Array.isArray(items) && items.length) {
    return items
      .filter((item) => (item?.status ?? 'Published') === 'Published')
      .slice(0, 4)
  }

  return []
})
const homeFeatureItems = computed(() => {
  const items = homeFeaturesContent.value?.items

  if (Array.isArray(items) && items.length) {
    return items.slice(0, 2)
  }

  return [
    {
      image_url: 'https://html.awaikenthemes.com/artistic/images/digital-features-img-1.jpg',
      title: 'custom branding solutions',
      description: 'Unique brand identity development, including logos, color palettes.',
    },
    {
      image_url: 'https://html.awaikenthemes.com/artistic/images/digital-features-img-2.jpg',
      title: 'data-driven digital marketing',
      description: 'Strategies combining SEO, PPC, content marketing',
    },
  ]
})
const whoWeAreCounters = computed(() => {
  const items = whoWeAreContent.value?.items

  if (Array.isArray(items) && items.length) {
    return items.slice(0, 4)
  }

  return [
    {
      icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-who-we-are-counter-1.svg',
      value: '35',
      suffix: 'k+',
      label: 'Happy Customer Around the Word',
    },
    {
      icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-who-we-are-counter-3.svg',
      value: '250',
      suffix: '+',
      label: 'trusted best partners and sponsers',
    },
    {
      icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-who-we-are-counter-2.svg',
      value: '120',
      suffix: '+',
      label: 'best client support award achieved',
    },
    {
      icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-who-we-are-counter-4.svg',
      value: '5',
      suffix: 'k+',
      label: 'active users using our best services',
    },
  ]
})
const whoWeAreTopCounters = computed(() => whoWeAreCounters.value.slice(0, 2))
const whoWeAreBottomCounters = computed(() => whoWeAreCounters.value.slice(2, 4))
const whoWeAreReviewImages = computed(() => {
  const images = whoWeAreContent.value?.review_images

  if (Array.isArray(images) && images.length) {
    return images.filter(Boolean).slice(0, 5)
  }

  return testimonialReviewImages.value.slice(0, 5)
})
const displayWhoWeAreCounters = computed(() =>
  whoWeAreCounters.value.map((item, index) => {
    const meta = getCounterMeta(item?.value)
    const currentValue = animatedWhoWeAreValues.value[index] ?? 0

    return {
      ...item,
      displayValue: formatCounterValue(currentValue, meta.decimals),
    }
  }),
)
const displayWhoWeAreTopCounters = computed(() => displayWhoWeAreCounters.value.slice(0, 2))
const displayWhoWeAreBottomCounters = computed(() => displayWhoWeAreCounters.value.slice(2, 4))
const whyChooseItems = computed(() => {
  const items = whyChooseUsContent.value?.items

  if (Array.isArray(items) && items.length) {
    return items
  }

  return []
})

function cancelWhoWeAreAnimation() {
  if (whoWeAreAnimationFrame) {
    window.cancelAnimationFrame(whoWeAreAnimationFrame)
    whoWeAreAnimationFrame = null
  }
}

function animateWhoWeAreCounters() {
  cancelWhoWeAreAnimation()

  const counters = whoWeAreCounters.value.map((item) => getCounterMeta(item?.value))
  const duration = 1800
  const start = performance.now()

  animatedWhoWeAreValues.value = counters.map(() => 0)

  const step = (timestamp) => {
    const progress = Math.min((timestamp - start) / duration, 1)
    const eased = 1 - Math.pow(1 - progress, 3)

    animatedWhoWeAreValues.value = counters.map((counter) => counter.target * eased)

    if (progress < 1) {
      whoWeAreAnimationFrame = window.requestAnimationFrame(step)
      return
    }

    animatedWhoWeAreValues.value = counters.map((counter) => counter.target)
    whoWeAreAnimationFrame = null
  }

  whoWeAreAnimationFrame = window.requestAnimationFrame(step)
}

function observeWhoWeAreCounters() {
  if (typeof window === 'undefined' || !whoWeAreSection.value) {
    return
  }

  if (whoWeAreObserver) {
    whoWeAreObserver.disconnect()
  }

  whoWeAreObserver = new window.IntersectionObserver(
    (entries) => {
      const [entry] = entries

      if (!entry?.isIntersecting || whoWeAreAnimated) {
        return
      }

      whoWeAreAnimated = true
      animateWhoWeAreCounters()
      whoWeAreObserver?.disconnect()
      whoWeAreObserver = null
    },
    { threshold: 0.25 },
  )

  whoWeAreObserver.observe(whoWeAreSection.value)
}

watch(
  whoWeAreCounters,
  async (items) => {
    animatedWhoWeAreValues.value = items.map(() => 0)
    whoWeAreAnimated = false
    await nextTick()
    observeWhoWeAreCounters()
  },
  { immediate: true },
)

onMounted(() => {
  observeWhoWeAreCounters()
})

onBeforeUnmount(() => {
  cancelWhoWeAreAnimation()
  whoWeAreObserver?.disconnect()
  whoWeAreObserver = null
})
</script>

<template>
<div >
 <!-- Page Header Start -->
	<div class="page-header" :style="pageHeaderStyle">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-12">
					<!-- Page Header Box Start -->
					<div class="page-header-box">
						<h1 class="text-anime-style-2" data-cursor="-opaque">{{ pageTitleParts.before }} <span>{{ pageTitleParts.highlight }}</span>{{ pageTitleParts.after ? ` ${pageTitleParts.after}` : '' }}</h1>
						<nav class="wow fadeInUp">
                            <ol class="breadcrumb">
								<li class="breadcrumb-item"><RouterLink to="/">home</RouterLink></li>
								<li class="breadcrumb-item active" aria-current="page">{{ aboutContent.breadcrumb_label || 'About Us' }}</li>
							</ol>
						</nav>
					</div>
					<!-- Page Header Box End -->
				</div>
			</div>
		</div>
	</div>
	<!-- Page Header End -->

    <!-- Scrolling Ticker Section Start -->
    <div class="our-scrolling-ticker subpages-scrolling-ticker">
        <!-- Scrolling Ticker Start -->
        <div class="scrolling-ticker-box">
            <div class="scrolling-content">
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Custom Branding</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Website Design</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Digital Marketing</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Strategy Consulting</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Analytics & Reporting</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Custom Branding</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Website Design</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Digital Marketing</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Strategy Consulting</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Analytics & Reporting</span>
            </div>

            <div class="scrolling-content">
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Custom Branding</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Website Design</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Digital Marketing</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Strategy Consulting</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Analytics & Reporting</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Custom Branding</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Website Design</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Digital Marketing</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Strategy Consulting</span>
                <span><img src="https://html.awaikenthemes.com/artistic/images/asterisk-icon.svg" alt="">Analytics & Reporting</span>
            </div>
        </div>
    </div>
    <!-- Scrolling Ticker Section End -->

    <!-- About Agency Section Start -->
    <div class="about-agency">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <!-- About Agency Content Start -->
                    <div class="about-agency-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ aboutContent.agency_subtitle || 'about agency' }}</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{{ agencyTitleParts.before }} <span>{{ agencyTitleParts.highlight }}</span> {{ agencyTitleParts.after }}</h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- Section btn Start -->
                        <div class="section-btn wow fadeInUp" data-wow-delay="0.25s">
                            <a :href="aboutContent.agency_cta_url || '/about'" class="btn-default">{{ aboutContent.agency_cta_text || 'more about' }}</a>
                        </div>
                        <!-- Section btn End -->
                    </div>
                    <!-- About Agency Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- About Agency List Start -->
                    <div class="about-agency-list">
                        <div v-for="(item, index) in aboutAgencyItems" :key="`about-agency-item-${index}`" class="about-agency-item wow fadeInUp" :data-wow-delay="index ? `${index * 0.2}s` : null">
                            <div class="icon-box">
                                <img :src="resolveAssetUrl(item.icon_url)" :alt="item.title">
                            </div>
                            <div class="agency-item-content">
                                <h3>{{ item.title }}</h3>
                                <p>{{ item.description }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- About Agency List End -->
                </div>
            </div>
        </div>
    </div>
    <!-- About Us Section End -->

    <!-- Our Approach Start -->
    <div class="our-approach">
        <div class="container">
            <div class="row align-items-center section-row">
                <div class="col-lg-6">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">our approach</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">Crafting <span>impactful</span> digital experiences</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
                <div class="col-lg-6">
                    <!-- Section Content Start -->
                    <div class="section-title-content wow fadeInUp" data-wow-delay="0.25s">
                        <p>We blend creativity, strategy, and technology to design digital experiences that connect, engage, and inspire. From concept to completion, we deliver tailored solutions that elevate brands and drive results.</p>
                    </div>
                    <!-- Section Content End -->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <!-- Mission Vision Item Start -->
                    <div class="mission-vision-item wow fadeInUp">
                        <!-- Mission Vision Image Start -->
                        <div class="mission-vision-image">
                            <div class="mission-vision-img">
                                <figure class="image-anime">
                                    <img src="https://html.awaikenthemes.com/artistic/images/our-mission-img.jpg" alt="">
                                </figure>
                            </div>

                            <div class="icon-box">
                                <img src="https://html.awaikenthemes.com/artistic/images/icon-our-mission.svg" alt="">
                            </div>
                        </div>
                        <!-- Mission Vision Image End -->

                        <!-- Mission Vision Content Start -->
                        <div class="mission-vision-content">
                            <h3>our mission</h3>
                            <p>Delivering transformative digital solutions that elevate brands  connections.</p>
                        </div>
                        <!-- Mission Vision Content End -->
                    </div>
                    <!-- Mission Vision Item End -->
                </div>

                <div class="col-lg-4 col-md-6">
                    <!-- Mission Vision Item Start -->
                    <div class="mission-vision-item wow fadeInUp" data-wow-delay="0.2s">
                        <!-- Mission Vision Image Start -->
                        <div class="mission-vision-image">
                            <div class="mission-vision-img">
                                <figure class="image-anime">
                                    <img src="https://html.awaikenthemes.com/artistic/images/our-vision-img.jpg" alt="">
                                </figure>
                            </div>

                            <div class="icon-box">
                                <img src="https://html.awaikenthemes.com/artistic/images/icon-our-vision.svg" alt="">
                            </div>
                        </div>
                        <!-- Mission Vision Image End -->

                        <!-- Mission Vision Content Start -->
                        <div class="mission-vision-content">
                            <h3>our vision</h3>
                            <p>Shaping the future through innovative and impactful digital experiences.</p>
                        </div>
                        <!-- Mission Vision Content End -->
                    </div>
                    <!-- Mission Vision Item End -->
                </div>

                <div class="col-lg-4 col-md-6">
                    <!-- Mission Vision Item Start -->
                    <div class="mission-vision-item wow fadeInUp" data-wow-delay="0.4s">
                        <!-- Mission Vision Image Start -->
                        <div class="mission-vision-image">
                            <div class="mission-vision-img">
                                <figure class="image-anime">
                                    <img src="https://html.awaikenthemes.com/artistic/images/our-value-img.jpg" alt="">
                                </figure>
                            </div>

                            <div class="icon-box">
                                <img src="https://html.awaikenthemes.com/artistic/images/icon-our-value.svg" alt="">
                            </div>
                        </div>
                        <!-- Mission Vision Image End -->

                        <!-- Mission Vision Content Start -->
                        <div class="mission-vision-content">
                            <h3>our value</h3>
                            <p>Integrity, creativity, innovation, collaboration, excellence, results.</p>
                        </div>
                        <!-- Mission Vision Content End -->
                    </div>
                    <!-- Mission Vision Item End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Approach End -->

    <!-- Who We Are Start -->
    <div ref="whoWeAreSection" class="who-we-are">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Who We Are Content Start -->
                    <div class="who-we-are-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ whoWeAreContent.subtitle || 'who we are' }}</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{{ whoWeAreContent.title_before || 'Experts in' }} <span>{{ whoWeAreContent.title_highlight || 'digital' }}</span> {{ whoWeAreContent.title_after || 'brand innovation' }}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.25s">{{ whoWeAreContent.description || 'We specialize in transforming brands through cutting-edge digital strategies, blending creativity with technology to drive growth, enhance engagement, and deliver memorable experiences.' }}</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- Experts Rating Video Start -->
                        <div class="experts-rating-video">
                            <!-- Experts Rating Video Image Start -->
                            <div class="experts-rating-video-image">
                                <!-- Video Image Start -->
                                <div class="video-image">
                                    <a :href="whoWeAreContent.video_url || 'https://www.youtube.com/watch?v=Y-x0efG1seA'" class="popup-video" data-cursor-text="Play">
                                        <figure class="image-anime">
                                            <img :src="resolveAssetUrl(whoWeAreContent.video_image_url || 'https://html.awaikenthemes.com/artistic/images/experts-rating-video-bg.jpg')" alt="">
                                        </figure>
                                    </a>
                                </div>
                                <!-- Video Image End -->

                                <!-- Video Play Button Start -->
                                <div class="video-play-button">
                                    <a :href="whoWeAreContent.video_url || 'https://www.youtube.com/watch?v=Y-x0efG1seA'" class="popup-video" data-cursor-text="Play">
                                        <i class="fa-solid fa-play"></i>
                                    </a>
                                </div>
                                <!-- Video Play Button End -->           
                            </div>
                            <!-- Experts Rating Video Image End -->

                            <!-- Who We Are Company Client Start -->
                            <div class="who-we-are-client">
                                <div class="comapny-client-rating wow fadeInUp">
                                    <ul>
                                        <li>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                        </li>
                                    </ul>
                                    <p>{{ whoWeAreContent.review_label || testimonialContent.review_label || '(40+ Reviews)' }}</p>
                                </div>
                                
                                <!-- Company Client Images Start -->                          
                                <div class="company-client-images">
                                    <div v-for="(image, index) in whoWeAreReviewImages" :key="`about-who-we-are-client-${index}`" class="client-image">
                                        <figure class="image-anime reveal">
                                            <img :src="resolveAssetUrl(image)" :alt="`client review ${index + 1}`">
                                        </figure>
                                    </div>
                                </div>
                                <!-- Company Client Images End -->

                                <!-- Contact Now Button Start -->
                                <div class="contact-now-btn wow fadeInUp" data-wow-delay="0.2s">
                                    <a :href="whoWeAreContent.cta_url || '/contact'" class="contact-btn">{{ whoWeAreContent.cta_text || 'contact now' }}</a>
                                </div>
                                <!-- Contact Now Button End -->
                            </div>
                            <!-- Who We Are Company Client End -->
                        </div>
                        <!-- Experts Rating Video End -->
                    </div>
                    <!-- Who We Are Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- Experts Counter List Start -->
                    <div class="experts-counters-list">
                        <!-- Experts Counter Box Start -->
                        <div class="experts-counter-box expert-box-1">
                            <!-- Experts Counter Item Start -->
                            <div v-for="(item, index) in displayWhoWeAreTopCounters" :key="`about-who-we-are-top-${index}`" class="experts-counter-item">
                                <div class="icon-box">
                                    <img :src="resolveAssetUrl(item.icon_url)" :alt="item.label">
                                </div>
                                <div class="experts-counter-content">
                                    <h2><span class="who-we-are-counter-value">{{ item.displayValue }}</span>{{ item.suffix }}</h2>
                                    <p>{{ item.label }}</p>
                                </div>
                            </div>
                        </div> 
                        <!-- Experts Counter Box End -->
                        
                        <!-- Experts Counter Box Start -->
                        <div class="experts-counter-box expert-box-2">
                            <!-- Experts Counter Item Start -->
                            <div v-for="(item, index) in displayWhoWeAreBottomCounters" :key="`about-who-we-are-bottom-${index}`" class="experts-counter-item">
                                <div class="icon-box">
                                    <img :src="resolveAssetUrl(item.icon_url)" :alt="item.label">
                                </div>
                                <div class="experts-counter-content">
                                    <h2><span class="who-we-are-counter-value">{{ item.displayValue }}</span>{{ item.suffix }}</h2>
                                    <p>{{ item.label }}</p>
                                </div>
                            </div>
                        </div> 
                        <!-- Experts Counter Box End -->                     
                    </div>
                    <!-- Experts Counter List End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Who We Are End -->

    <!-- Our Features Start -->
    <div class="our-features">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-6">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">{{ homeFeaturesContent.subtitle || 'features' }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{{ homeFeaturesContent.title_before || 'Innovative' }} <span>{{ homeFeaturesContent.title_highlight || 'features' }}</span> {{ homeFeaturesContent.title_after || 'for your digital success' }}</h2>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-6">
                    <!-- Section Content Button Start -->
                    <div class="section-content-btn">
                        <!-- Section Title Content Start -->
                        <div class="section-title-content wow fadeInUp" data-wow-delay="0.2s">
                            <p>{{ homeFeaturesContent.description || 'Our digital services empower brands with innovative strategies and solutions for sustainable growth and engagement.' }}</p>
                        </div>
                        <!-- Section Title Content End -->

                        <!-- Section Button Start -->
                        <div class="section-btn wow fadeInUp" data-wow-delay="0.4s">
                            <a :href="homeFeaturesContent.cta_url || '/contact'" class="btn-default">{{ homeFeaturesContent.cta_text || 'learn more' }}</a>
                        </div>
                        <!-- Section Button End -->
                    </div>   
                    <!-- Section Content Button End -->
                </div>
            </div>

            <div class="col-lg-12">
                <!-- Digital Features Box Start -->
                <div class="digital-features-box">
                    <div v-for="(item, index) in homeFeatureItems" :key="`about-feature-${index}`" class="digital-features-item wow fadeInUp" :class="`features-item-${index + 1}`" :data-wow-delay="index ? `${index * 0.25}s` : null">
                        <!-- Digital Features Image Start -->
                        <div class="digital-features-image">
                            <figure class="image-anime">
                                <img :src="resolveAssetUrl(item.image_url)" :alt="item.title">
                            </figure>
                        </div>
                        <!-- Digital Features Image End -->

                        <!-- Digital Features Content Start -->
                        <div class="digital-features-content">
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.description }}</p>
                        </div>
                        <!-- Digital Features Content End -->
                    </div>
                </div>
                <!-- Digital Features Box End -->
            </div>
        </div>
    </div>
    <!-- Our Features End -->


    <!-- Why Choose us Section Start -->
    <div class="why-choose-us">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-7">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">{{ whyChooseUsContent.subtitle || 'why choose' }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{{ whyChooseUsContent.title_before || 'Expertise for' }} <span>{{ whyChooseUsContent.title_highlight || 'your digital' }}</span> {{ whyChooseUsContent.title_after || 'growth journey' }}</h2>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-5">
                    <!-- Section Title Content Start -->
                    <div class="section-title-content wow fadeInUp" data-wow-delay="0.25s">
                        <p>{{ whyChooseUsContent.description || 'Our dedicated team is committed to understanding your unique needs, ensuring that we provide innovative strategies that drive results. With a focus on quality and integrity.' }}</p>
                    </div>
                    <!-- Section Title Content End -->          
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Why Choose Content Start -->
                    <div class="why-choose-content">
                        <div
                            v-for="(item, index) in whyChooseItems"
                            :key="`about-why-choose-${index}`"
                            class="why-choose-item wow fadeInUp"
                            :class="{ active: index === 0 }"
                            :data-wow-delay="index ? `${index * 0.25}s` : null"
                        >
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.description }}</p>
                        </div>
                    </div>
                    <!-- Why Choose Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- Why Choose Image Start -->
                    <div class="why-choose-image">
                        <figure class="image-anime">
                            <img :src="resolveAssetUrl(whyChooseUsContent.image_url || 'https://html.awaikenthemes.com/artistic/images/why-choose-image.jpg')" alt="">
                        </figure>
                    </div>
                    <!-- Why Choose Image End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Why Choose us Section End -->

    <!-- Our Testimonial Section Start -->
    <div class="our-testimonial">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-7">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">{{ testimonialContent.subtitle || 'testimonials' }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{{ testimonialContent.title_before || 'Read what they have to say about' }} <span>{{ testimonialContent.title_highlight || 'working with us' }}</span>{{ testimonialContent.title_after ? ` ${testimonialContent.title_after}` : '' }}</h2>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-5">
                    <div class="section-title-content wow fadeInUp" data-wow-delay="0.2s">
                        <p>{{ testimonialContent.description || 'Discover how our clients have achieved success through our innovative solutions and dedicated support.' }}</p>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-4">
                    <!-- Testimonial Review Box Start -->
                    <div class="testimonial-review-box">
                        <!-- Testimonial Review Header Start -->
                        <div class="testimonial-review-header">
                            <h2><span>{{ testimonialContent.review_score || '4.9' }}</span></h2>
                            <div class="testimonial-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <p>{{ testimonialContent.review_label || '(40+ Reviews)' }}</p>
                        </div>
                        <!-- Testimonial Review Header End -->

                        <!-- Testimonial Review Content Start -->
                        <div class="testimonial-review-content wow fadeInUp">
                            <h3>{{ testimonialContent.review_heading || 'Customer experiences that speak for themselves' }}</h3>
                        </div>
                        <!-- Testimonial Review Content End -->

                        <!-- Testimonial Review Image Start -->
                        <div class="testimonial-review-image">
                            <div v-for="(image, index) in testimonialReviewImages" :key="`about-testimonial-review-${index}`" class="satisfy-client-image">
                                <figure class="image-anime">
                                    <img :src="resolveAssetUrl(image)" :alt="`testimonial review ${index + 1}`">
                                </figure>
                            </div>
                        </div>
                        <!-- Testimonial Review Image End -->
                    </div>
                    <!-- Testimonial Review Box End -->                   
                </div>

                <div class="col-lg-8">
                    <!-- Testimonial Slider Start -->
                    <div class="testimonial-slider">
                            <div class="swiper">
                                <div class="swiper-wrapper" data-cursor-text="Drag">
                                <div v-for="item in testimonialItems" :key="item.id || item.author_name" class="swiper-slide">
                                    <div class="testimonial-item">
                                        <div class="testimonial-rating">
                                            <i v-for="star in Number(item.rating || 5)" :key="`about-star-${item.id || item.author_name}-${star}`" class="fa-solid fa-star"></i>
                                        </div>                                        
                                        <div class="testimonial-content">
                                            <p>{{ item.quote || item.description }}</p>
                                        </div>
                                        <div class="testimonial-body">
                                            <div class="author-image">
                                                <figure class="image-anime">
                                                    <img :src="resolveAssetUrl(item.author_image_url || item.image_url)" :alt="item.author_name">
                                                </figure>
                                            </div>            
                                            <div class="author-content">
                                                <h3>{{ item.author_name }}</h3>
                                                <p>{{ item.author_role }}</p>
                                            </div>
                                        </div>                                    
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-btn">
                                <div class="testimonial-button-prev"></div>
                                <div class="testimonial-button-next"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial Slider End -->
                </div>

                <div class="col-lg-12">
                    <!-- Testimonial Benefits Box Start -->
                    <div class="testimonial-benefits-box">
                        <div v-for="(item, index) in testimonialBenefits" :key="`about-testimonial-benefit-${index}`" class="testimonial-benefits-item wow fadeInUp" :data-wow-delay="index ? `${index * 0.2}s` : null">
                            <div class="icon-box">
                                <img :src="resolveAssetUrl(item.icon_url)" :alt="item.title">
                            </div>
                            <div class="testimonial-benefits-content">
                                <h3>{{ item.title }}</h3>
                                <ul>
                                    <li v-for="(point, pointIndex) in item.points" :key="`about-testimonial-benefit-point-${index}-${pointIndex}`">{{ point }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial Benefits Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Testimonial Section End -->

    <!-- Our FAQs Start -->
    <div class="our-faqs">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- FAQ Images Start -->
                    <div class="faq-images">
                        <!-- FAQ Img 1 Start -->
                        <div class="faq-img-1">
                            <figure class="image-anime ">
                                <img src="https://html.awaikenthemes.com/artistic/images/faq-img-1.jpg" alt="">
                            </figure>
                        </div>
                        <!-- FAQ Img 1 End -->

                        <!-- FAQ Img 2 Start -->
                        <div class="faq-img-2">
                            <figure class="image-anime ">
                                <img src="https://html.awaikenthemes.com/artistic/images/faq-img-2.jpg" alt="">
                            </figure>
                        </div>
                        <!-- FAQ Img 2 End -->

                        <!-- FAQ CTA Box Start -->
                        <div class="faq-cta-box">
                            <a :href="contactInfo.phone ? `tel:${String(contactInfo.phone).replace(/[^+\d]/g, '')}` : 'tel:308855314'">
                                <img src="https://html.awaikenthemes.com/artistic/images/faq-cta-phone.svg" alt="">{{ contactInfo.phone || '(30) 8855-314' }}
                            </a>
                        </div>
                        <!-- FAQ CTA Box End -->
                    </div>
                    <!-- FAQ Images End -->
                </div>

                <div class="col-lg-6">
                    <!-- Our FAQ Section Start -->
                    <div class="our-faq-section">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">have any questions?</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">Let us address your <span>questions</span> today!</h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- FAQ Accordion Start -->
                        <div class="faq-accordion" id="aboutfaqaccordion">
                            <div v-for="(item, index) in faqItems" :key="`about-faq-${index}`" class="accordion-item wow fadeInUp" :data-wow-delay="index ? `${index * 0.2}s` : null">
                                <h2 class="accordion-header" :id="`about-heading-${index}`">
                                    <button
                                        class="accordion-button"
                                        :class="{ collapsed: index !== 0 }"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        :data-bs-target="`#about-collapse-${index}`"
                                        :aria-expanded="index === 0 ? 'true' : 'false'"
                                        :aria-controls="`about-collapse-${index}`"
                                    >
                                        {{ item.question }}
                                    </button>
                                </h2>
                                <div
                                    :id="`about-collapse-${index}`"
                                    class="accordion-collapse collapse"
                                    :class="{ show: index === 0 }"
                                    :aria-labelledby="`about-heading-${index}`"
                                    data-bs-parent="#aboutfaqaccordion"
                                >
                                    <div class="accordion-body">
                                        <p>{{ item.answer }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Accordion End -->
                    </div>
                    <!-- Our FAQ Section End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our FAQs End -->

</div>



</template>


