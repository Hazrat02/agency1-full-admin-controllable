<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import MediaUploadInput from '../../../Components/Admin/MediaUploadInput.vue';

const props = defineProps({
    mode: {
        type: String,
        required: true,
    },
    testimonial: {
        type: Object,
        required: true,
    },
    originalId: {
        type: String,
        default: null,
    },
});

const form = useForm({
    id: props.testimonial.id ?? '',
    rating: props.testimonial.rating ?? 5,
    quote: props.testimonial.quote ?? props.testimonial.description ?? '',
    author_image_url: props.testimonial.author_image_url ?? props.testimonial.image_url ?? '',
    author_name: props.testimonial.author_name ?? '',
    author_role: props.testimonial.author_role ?? '',
    status: props.testimonial.status ?? 'Active',
    sort_order: props.testimonial.sort_order ?? 1,
});

const submit = () => {
    const url = props.mode === 'create'
        ? '/admin/content/testimonial'
        : `/admin/content/testimonial/${props.originalId}`;

    if (props.mode === 'create') {
        form.post(url);
        return;
    }

    form.put(url);
};
</script>

<template>
    <Head :title="mode === 'create' ? 'Create Testimonial' : 'Edit Testimonial'" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">{{ mode === 'create' ? 'Create Testimonial' : 'Edit Testimonial' }}</h1>
            <Link href="/admin/content/testimonial" class="btn btn-outline-secondary">Back</Link>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Testimonial Form</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="submit">
                    <MediaUploadInput
                        v-model="form.author_image_url"
                        label="Author Image URL"
                        placeholder="https://..."
                        folder="testimonial"
                        accept="image/*"
                    />

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <select v-model="form.rating" class="form-select">
                                    <option :value="5">5 Stars</option>
                                    <option :value="4">4 Stars</option>
                                    <option :value="3">3 Stars</option>
                                    <option :value="2">2 Stars</option>
                                    <option :value="1">1 Star</option>
                                </select>
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
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input v-model="form.sort_order" class="form-control" type="number" min="0" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quote</label>
                        <textarea v-model="form.quote" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Author Name</label>
                                <input v-model="form.author_name" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Author Role</label>
                                <input v-model="form.author_role" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.author_name || !form.quote">
                        {{ mode === 'create' ? 'Create Testimonial' : 'Update Testimonial' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
