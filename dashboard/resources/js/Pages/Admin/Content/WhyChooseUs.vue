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
        title: 'Data-driven Approach',
        description: 'We leverage data and insights to make informed decisions that lead to more effective and efficient solutions.',
    },
    {
        title: 'Competitive Pricing',
        description: 'We offer our top-quality services at competitive prices, providing you with great value for your investment.',
    },
    {
        title: 'Ethical Business Practices',
        description: 'We maintain the highest level of professionalism and ethical standards professionalism in all our business dealings.',
    },
];

const items = Array.isArray(props.content.items) && props.content.items.length === 3
    ? props.content.items
    : defaultItems;

const form = useForm({
    subtitle: props.content.subtitle ?? 'why choose',
    title_before: props.content.title_before ?? 'Expertise for',
    title_highlight: props.content.title_highlight ?? 'your digital',
    title_after: props.content.title_after ?? 'growth journey',
    description: props.content.description ?? 'Our dedicated team is committed to understanding your unique needs, ensuring that we provide innovative strategies that drive results. With a focus on quality and integrity.',
    items: items.map((item) => ({
        title: item.title ?? '',
        description: item.description ?? '',
    })),
    image_url: props.content.image_url ?? '',
});

const submit = () => {
    form.put('/admin/content/why-choose-us');
};
</script>

<template>
    <Head title="Content - Why Choose Us" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Content Manage / Why Choose Us</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Why Choose Us Form</h5>
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
                        <div v-for="(item, index) in form.items" :key="`why-item-${index}`" class="col-lg-4">
                            <div class="border rounded p-3 mb-3 h-100">
                                <h6 class="mb-3">Why Choose Item {{ index + 1 }}</h6>
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

                    <MediaUploadInput
                        v-model="form.image_url"
                        label="Section Image URL"
                        placeholder="https://..."
                        folder="why-choose-us"
                        accept="image/*"
                    />

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.subtitle || !form.title_before || !form.title_highlight">
                        Save Why Choose Us
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
