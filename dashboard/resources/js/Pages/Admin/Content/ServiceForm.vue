<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import MediaUploadInput from '../../../Components/Admin/MediaUploadInput.vue';

const props = defineProps({
    mode: { type: String, required: true },
    service: { type: Object, required: true },
    originalSlug: { type: String, default: null },
});

const defaultSteps = () => ([
    { icon_url: '', image_url: '', title: '', description: '' },
    { icon_url: '', image_url: '', title: '', description: '' },
    { icon_url: '', image_url: '', title: '', description: '' },
]);

const defaultFeatures = () => ['', '', '', '', '', ''];

const form = useForm({
    name: props.service.name ?? '',
    slug: props.service.slug ?? '',
    card_icon_url: props.service.card_icon_url ?? '',
    short_description: props.service.short_description ?? '',
    detail_feature_image_url: props.service.detail_feature_image_url ?? '',
    intro_paragraph_1: props.service.intro_paragraph_1 ?? '',
    intro_paragraph_2: props.service.intro_paragraph_2 ?? '',
    key_features_title_before: props.service.key_features_title_before ?? 'Key',
    key_features_title_highlight: props.service.key_features_title_highlight ?? 'feature',
    key_features_title_after: props.service.key_features_title_after ?? 'of digital marketing',
    key_features_paragraph_1: props.service.key_features_paragraph_1 ?? '',
    key_features_paragraph_2: props.service.key_features_paragraph_2 ?? '',
    features_list: Array.isArray(props.service.features_list) && props.service.features_list.length ? props.service.features_list : defaultFeatures(),
    feature_side_image_url: props.service.feature_side_image_url ?? '',
    process_title_before: props.service.process_title_before ?? 'Our',
    process_title_highlight: props.service.process_title_highlight ?? 'process',
    process_title_after: props.service.process_title_after ?? 'of digital marketing',
    process_description: props.service.process_description ?? '',
    process_steps: Array.isArray(props.service.process_steps) && props.service.process_steps.length ? props.service.process_steps : defaultSteps(),
    show_on_home: props.service.show_on_home ?? false,
    status: props.service.status ?? 'Active',
    sort_order: props.service.sort_order ?? 1,
});

const submit = () => {
    const url = props.mode === 'create'
        ? '/admin/content/services'
        : `/admin/content/services/${props.originalSlug}`;

    if (props.mode === 'create') {
        form.post(url);
        return;
    }

    form.put(url);
};
</script>

<template>
    <Head :title="mode === 'create' ? 'Create Service' : 'Edit Service'" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">{{ mode === 'create' ? 'Create Service' : 'Edit Service' }}</h1>
            <Link href="/admin/content/services" class="btn btn-outline-secondary">Back</Link>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Service Form</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Service Name</label>
                                <input v-model="form.name" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input v-model="form.slug" class="form-control" type="text" placeholder="leave blank to auto-create" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <MediaUploadInput v-model="form.card_icon_url" label="Card Icon URL" placeholder="https://..." folder="services" accept="image/*" />
                        </div>
                        <div class="col-lg-4">
                            <MediaUploadInput v-model="form.detail_feature_image_url" label="Detail Feature Image URL" placeholder="https://..." folder="services" accept="image/*" />
                        </div>
                        <div class="col-lg-4">
                            <MediaUploadInput v-model="form.feature_side_image_url" label="Feature Side Image URL" placeholder="https://..." folder="services" accept="image/*" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Card Short Description</label>
                        <textarea v-model="form.short_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detail Intro Paragraph 1</label>
                        <textarea v-model="form.intro_paragraph_1" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Detail Intro Paragraph 2</label>
                        <textarea v-model="form.intro_paragraph_2" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Key Features Title Before</label>
                                <input v-model="form.key_features_title_before" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Key Features Highlight</label>
                                <input v-model="form.key_features_title_highlight" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Key Features Title After</label>
                                <input v-model="form.key_features_title_after" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Key Features Paragraph 1</label>
                        <textarea v-model="form.key_features_paragraph_1" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Key Features Paragraph 2</label>
                        <textarea v-model="form.key_features_paragraph_2" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div v-for="(feature, index) in form.features_list" :key="`feature-${index}`" class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Feature List Item {{ index + 1 }}</label>
                                <input v-model="form.features_list[index]" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Process Title Before</label>
                                <input v-model="form.process_title_before" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Process Highlight</label>
                                <input v-model="form.process_title_highlight" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Process Title After</label>
                                <input v-model="form.process_title_after" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Process Description</label>
                        <textarea v-model="form.process_description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div v-for="(step, index) in form.process_steps" :key="`step-${index}`" class="col-lg-4">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">Process Step {{ index + 1 }}</h6>
                                <MediaUploadInput v-model="step.icon_url" label="Step Icon URL" placeholder="https://..." folder="services" accept="image/*" />
                                <MediaUploadInput v-model="step.image_url" label="Step Image URL" placeholder="https://..." folder="services" accept="image/*" />
                                <div class="mb-3">
                                    <label class="form-label">Step Title</label>
                                    <input v-model="step.title" class="form-control" type="text" />
                                </div>
                                <div>
                                    <label class="form-label">Step Description</label>
                                    <textarea v-model="step.description" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input v-model="form.sort_order" class="form-control" type="number" min="0" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select v-model="form.status" class="form-select">
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input id="service_show_on_home" v-model="form.show_on_home" class="form-check-input" type="checkbox" />
                        <label class="form-check-label" for="service_show_on_home">Show on home page services section</label>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.name">
                        {{ mode === 'create' ? 'Create Service' : 'Update Service' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
