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

const defaultItems = [
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
];

const items = Array.isArray(props.content.items) && props.content.items.length === 2
    ? props.content.items
    : defaultItems;

const form = useForm({
    subtitle: props.content.subtitle ?? 'features',
    title_before: props.content.title_before ?? 'Innovative',
    title_highlight: props.content.title_highlight ?? 'features',
    title_after: props.content.title_after ?? 'for your digital success',
    description: props.content.description ?? 'Our digital services empower brands with innovative strategies and solutions for sustainable growth and engagement.',
    cta_text: props.content.cta_text ?? 'learn more',
    cta_url: props.content.cta_url ?? '/contact',
    items: items.map((item) => ({
        image_url: item.image_url ?? '',
        title: item.title ?? '',
        description: item.description ?? '',
    })),
});

const submit = () => {
    form.put('/admin/content/home-features');
};
</script>

<template>
    <Head title="Content - Home Features" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Home Features</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Home Features Form</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Subtitle</label>
                                <input v-model="form.subtitle" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Title Before Highlight</label>
                                <input v-model="form.title_before" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Title Highlight</label>
                                <input v-model="form.title_highlight" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Title After Highlight</label>
                        <input v-model="form.title_after" class="form-control" type="text" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Section Description</label>
                        <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Button Text</label>
                                <input v-model="form.cta_text" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Button URL</label>
                                <input v-model="form.cta_url" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div v-for="(item, index) in form.items" :key="`feature-item-${index}`" class="col-lg-6">
                            <div class="border rounded p-3 mb-3 h-100">
                                <h6 class="mb-3">Feature Card {{ index + 1 }}</h6>
                                <MediaUploadInput
                                    v-model="item.image_url"
                                    label="Image URL"
                                    placeholder="https://..."
                                    folder="home-features"
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

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.subtitle || !form.title_before || !form.title_highlight">
                        Save Home Features
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
