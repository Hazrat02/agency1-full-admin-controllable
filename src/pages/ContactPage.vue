<script setup>
import { computed, reactive, ref } from 'vue'
import { buildApiUrl } from '../config/api'
import { useSiteStore } from '../stores/site'

const siteStore = useSiteStore()
const mapEmbedUrl = computed(() => siteStore.mapEmbedUrl || 'https://www.google.com/maps?q=London&output=embed')

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
})

const formErrors = ref({})
const submitError = ref('')
const submitSuccess = ref('')
const isSubmitting = ref(false)

const fullName = computed(() => `${form.firstName} ${form.lastName}`.trim())

function resetForm() {
  form.firstName = ''
  form.lastName = ''
  form.email = ''
  form.phone = ''
  form.subject = ''
  form.message = ''
}

async function submitContactForm() {
  formErrors.value = {}
  submitError.value = ''
  submitSuccess.value = ''
  isSubmitting.value = true

  try {
    const response = await fetch(buildApiUrl('contact.store'), {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        name: fullName.value,
        email: form.email,
        phone: form.phone,
        subject: form.subject,
        message: form.message,
      }),
    })

    const payload = await response.json().catch(() => ({}))

    if (!response.ok) {
      formErrors.value = payload?.errors ?? {}
      submitError.value = payload?.message || 'Unable to send your message right now.'
      return
    }

    submitSuccess.value = payload?.message || 'Message sent successfully.'
    resetForm()
  } catch {
    submitError.value = 'Unable to send your message right now.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
<div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box">
                    <h1 class="text-anime-style-2" data-cursor="-opaque">Contact <span>Us</span></h1>
                    <ol class="breadcrumb wow fadeInUp">
                        <li class="breadcrumb-item"><RouterLink to="/">home</RouterLink></li>
                        <li class="breadcrumb-item active" aria-current="page">contact us</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-contact-us">
    <div class="container">
        <div class="row section-row align-items-center">
            <div class="col-lg-6">
                <div class="section-title">
                    <h3 class="wow fadeInUp">contact us</h3>
                    <h2 class="text-anime-style-2" data-cursor="-opaque">Let's start your next <span>digital project</span></h2>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-title-content wow fadeInUp" data-wow-delay="0.2s">
                    <p>Tell us what you need and our team will get back to you with a practical plan, timeline, and next steps.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="contact-information">
                    <div class="contact-info-box">
                        <div class="info-box-1 wow fadeInUp">
                            <div class="contact-info-item">
                                <div class="icon-box">
                                    <img src="https://html.awaikenthemes.com/artistic/images/icon-phone.svg" alt="Phone icon">
                                </div>
                                <div class="contact-item-content">
                                    <h3>Call us</h3>
                                    <p>{{ siteStore.phone || '-' }}</p>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <div class="icon-box">
                                    <img src="https://html.awaikenthemes.com/artistic/images/icon-mail.svg" alt="Mail icon">
                                </div>
                                <div class="contact-item-content">
                                    <h3>Email us</h3>
                                    <p>{{ siteStore.email || '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="info-box-2 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="contact-info-item">
                                <div class="icon-box">
                                    <img src="https://html.awaikenthemes.com/artistic/images/icon-location.svg" alt="Location icon">
                                </div>
                                <div class="contact-item-content">
                                    <h3>Visit office</h3>
                                    <p>{{ siteStore.address || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="contact-us-form wow fadeInUp" data-wow-delay="0.4s">
                    <form novalidate @submit.prevent="submitContactForm">
                        <div class="row g-4">
                            <div class="form-group col-md-6">
                                <input v-model.trim="form.firstName" type="text" class="form-control" placeholder="First name" :disabled="isSubmitting" required>
                            </div>
                            <div class="form-group col-md-6">
                                <input v-model.trim="form.lastName" type="text" class="form-control" placeholder="Last name" :disabled="isSubmitting">
                            </div>
                            <div class="form-group col-md-6">
                                <input v-model.trim="form.email" type="email" class="form-control" placeholder="Email address" :disabled="isSubmitting" required>
                                <small v-if="formErrors.email?.length" class="text-danger">{{ formErrors.email[0] }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <input v-model.trim="form.phone" type="text" class="form-control" placeholder="Phone number" :disabled="isSubmitting">
                            </div>
                            <div class="form-group col-md-12">
                                <input v-model.trim="form.subject" type="text" class="form-control" placeholder="Subject" :disabled="isSubmitting" required>
                                <small v-if="formErrors.subject?.length" class="text-danger">{{ formErrors.subject[0] }}</small>
                            </div>
                            <div class="form-group col-md-12">
                                <textarea v-model.trim="form.message" class="form-control" rows="6" placeholder="Your message" :disabled="isSubmitting" required></textarea>
                                <small v-if="formErrors.message?.length" class="text-danger">{{ formErrors.message[0] }}</small>
                            </div>
                            <div class="col-md-12">
                                <div v-if="formErrors.name?.length" class="h6 text-danger">{{ formErrors.name[0] }}</div>
                                <div v-if="submitError" class="h6 text-danger">{{ submitError }}</div>
                                <div v-if="submitSuccess" class="h6 text-success">{{ submitSuccess }}</div>
                            </div>
                            <div class="col-md-12 contact-form-btn">
                                <button type="submit" class="btn-highlighted" :disabled="isSubmitting">
                                  {{ isSubmitting ? 'sending...' : 'send message' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="google-map">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="google-map-iframe wow fadeInUp">
                    <iframe :src="mapEmbedUrl" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
