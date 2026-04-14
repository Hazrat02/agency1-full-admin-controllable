<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import MediaUploadInput from '../../../Components/Admin/MediaUploadInput.vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    agency_subtitle: props.content.agency_subtitle ?? 'about agency',
    agency_title: props.content.agency_title ?? 'Crafting|unique digital|experiences that elevate your brand',
    agency_cta_text: props.content.agency_cta_text ?? 'more about',
    agency_cta_url: props.content.agency_cta_url ?? '/about',
    agency_items: Array.isArray(props.content.agency_items) && props.content.agency_items.length === 4
        ? props.content.agency_items
        : [
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
                description: 'Our purpose is simple: to help brands succeed in the digital age. We are passionate about building strong relationships with our clients and crafting custom strategies that drive results.',
            },
        ],
});

const submit = () => {
    form.put('/admin/content/about');
};
</script>

<template>
    <Head title="Content - About Us" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / About Us</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Homepage + About Page Agency Section</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Agency Subtitle</label>
                                <input v-model="form.agency_subtitle" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Agency Title</label>
                                <textarea v-model="form.agency_title" class="form-control" rows="3" placeholder="Crafting|unique digital|experiences that elevate your brand"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Agency CTA Text</label>
                                <input v-model="form.agency_cta_text" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Agency CTA URL</label>
                                <input v-model="form.agency_cta_url" class="form-control" type="text" placeholder="/about or /contact" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div v-for="(item, index) in form.agency_items" :key="`agency-item-${index}`" class="col-lg-6">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">Agency Card {{ index + 1 }}</h6>
                                <MediaUploadInput
                                    v-model="item.icon_url"
                                    label="Icon URL"
                                    placeholder="https://..."
                                    folder="about"
                                    accept="image/*"
                                />
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input v-model="item.title" class="form-control" type="text" />
                                </div>
                                <div>
                                    <label class="form-label">Description</label>
                                    <textarea v-model="item.description" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.agency_subtitle || !form.agency_title">
                        Save About Us
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
