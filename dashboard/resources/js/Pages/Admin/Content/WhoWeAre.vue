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

const defaultReviewImages = [
    'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-1.jpg',
    'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-2.jpg',
    'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-3.jpg',
    'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-4.jpg',
    'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-5.jpg',
];

const defaultItems = [
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
];

const form = useForm({
    subtitle: props.content.subtitle ?? 'who we are',
    title_before: props.content.title_before ?? 'Experts in',
    title_highlight: props.content.title_highlight ?? 'digital',
    title_after: props.content.title_after ?? 'brand innovation',
    description: props.content.description ?? 'We specialize in transforming brands through cutting-edge digital strategies, blending creativity with technology to drive growth, enhance engagement, and deliver memorable experiences.',
    video_url: props.content.video_url ?? 'https://www.youtube.com/watch?v=Y-x0efG1seA',
    video_image_url: props.content.video_image_url ?? 'https://html.awaikenthemes.com/artistic/images/experts-rating-video-bg.jpg',
    cta_text: props.content.cta_text ?? 'contact now',
    cta_url: props.content.cta_url ?? '/contact',
    review_label: props.content.review_label ?? '(40+ Reviews)',
    review_images: (Array.isArray(props.content.review_images) && props.content.review_images.length === 5
        ? props.content.review_images
        : defaultReviewImages
    ).map((image) => image ?? ''),
    items: (Array.isArray(props.content.items) && props.content.items.length === 4
        ? props.content.items
        : defaultItems
    ).map((item) => ({
        icon_url: item.icon_url ?? '',
        value: item.value ?? '',
        suffix: item.suffix ?? '',
        label: item.label ?? '',
    })),
});

const submit = () => {
    form.put('/admin/content/who-we-are');
};
</script>

<template>
    <Head title="Content - Who We Are" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Who We Are</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Who We Are Form</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Subtitle</label>
                                <input v-model="form.subtitle" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Title Before Highlight</label>
                                <input v-model="form.title_before" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Title Highlight</label>
                                <input v-model="form.title_highlight" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Title After Highlight</label>
                                <input v-model="form.title_after" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <MediaUploadInput
                                v-model="form.video_image_url"
                                label="Video Preview Image"
                                placeholder="https://..."
                                folder="who-we-are"
                                accept="image/*"
                            />
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Video URL</label>
                                <input v-model="form.video_url" class="form-control" type="text" />
                            </div>
                        </div>
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

                    <div class="mb-3">
                        <label class="form-label">Review Label</label>
                        <input v-model="form.review_label" class="form-control" type="text" />
                    </div>

                    <div class="row">
                        <div v-for="(image, index) in form.review_images" :key="`review-image-${index}`" class="col-lg-4">
                            <MediaUploadInput
                                v-model="form.review_images[index]"
                                :label="`Review Image ${index + 1}`"
                                placeholder="https://..."
                                folder="who-we-are"
                                accept="image/*"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div v-for="(item, index) in form.items" :key="`who-we-are-item-${index}`" class="col-lg-6">
                            <div class="border rounded p-3 mb-3 h-100">
                                <h6 class="mb-3">Counter {{ index + 1 }}</h6>
                                <MediaUploadInput
                                    v-model="item.icon_url"
                                    label="Icon URL"
                                    placeholder="https://..."
                                    folder="who-we-are"
                                    accept="image/*"
                                />
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Value</label>
                                            <input v-model="item.value" class="form-control" type="text" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Suffix</label>
                                            <input v-model="item.suffix" class="form-control" type="text" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Label</label>
                                    <textarea v-model="item.label" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="form.processing || !form.subtitle || !form.title_before || !form.title_highlight"
                    >
                        Save Who We Are
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
