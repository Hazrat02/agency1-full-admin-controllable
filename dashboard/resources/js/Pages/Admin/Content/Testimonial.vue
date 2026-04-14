<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import MediaUploadInput from '../../../Components/Admin/MediaUploadInput.vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
});

const fallbackBenefits = [
    {
        icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-testimonial-benefits-1.svg',
        title: 'Low Cost',
        points: ['Competitive fee', 'Flexible rates'],
    },
    {
        icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-testimonial-benefits-2.svg',
        title: 'Permission Less',
        points: ['Open for integration', 'Run your own nodes'],
    },
    {
        icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-testimonial-benefits-3.svg',
        title: 'Secure Data',
        points: ['Open source sheet', '360 Security'],
    },
    {
        icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-testimonial-benefits-4.svg',
        title: '24 X 7 Support',
        points: ['Toll free number', 'Ticket systems'],
    },
];

const sectionBenefits = Array.isArray(props.content.benefits) && props.content.benefits.length
    ? props.content.benefits
    : fallbackBenefits;

const form = useForm({
    subtitle: props.content.subtitle ?? 'testimonials',
    title_before: props.content.title_before ?? 'Read what they have to say about',
    title_highlight: props.content.title_highlight ?? 'working with us',
    title_after: props.content.title_after ?? '',
    description: props.content.description ?? 'Discover how our clients have achieved success through our innovative solutions and dedicated support.',
    review_score: props.content.review_score ?? '4.9',
    review_label: props.content.review_label ?? '(40+ Reviews)',
    review_heading: props.content.review_heading ?? 'Customer experiences that speak for themselves',
    review_images: Array.from({ length: 5 }, (_, index) => props.content.review_images?.[index] ?? ''),
    benefits: Array.from({ length: Math.max(4, sectionBenefits.length) }, (_, index) => ({
        icon_url: sectionBenefits[index]?.icon_url ?? '',
        title: sectionBenefits[index]?.title ?? '',
        points: [
            sectionBenefits[index]?.points?.[0] ?? '',
            sectionBenefits[index]?.points?.[1] ?? '',
        ],
    })),
});

const items = Array.isArray(props.content.items) ? props.content.items : [];
const submitting = ref(false);

const submit = () => {
    submitting.value = true;
    form.put('/admin/content/testimonial', {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
};

const deleteItem = (id) => {
    if (!window.confirm('Delete this testimonial?')) {
        return;
    }

    router.delete(`/admin/content/testimonial/${id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Content - Testimonial" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Content Manage / Testimonial</h1>
            <Link href="/admin/content/testimonial/create" class="btn btn-primary">
                Create Testimonial
            </Link>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Testimonial Section</h5>
            </div>
            <div class="card-body">
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
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">Review Score</label>
                            <input v-model="form.review_score" class="form-control" type="text" />
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">Review Label</label>
                            <input v-model="form.review_label" class="form-control" type="text" />
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">Review Box Heading</label>
                            <input v-model="form.review_heading" class="form-control" type="text" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div
                        v-for="(image, index) in form.review_images"
                        :key="`review-image-${index}`"
                        class="col-lg-4"
                    >
                        <MediaUploadInput
                            v-model="form.review_images[index]"
                            :label="`Review Image ${index + 1}`"
                            placeholder="https://..."
                            folder="testimonial"
                            accept="image/*"
                        />
                    </div>
                </div>

                <div class="border rounded p-3 mt-2">
                    <h6 class="mb-3">Benefits Items</h6>
                    <div
                        v-for="(benefit, index) in form.benefits"
                        :key="`benefit-${index}`"
                        class="border rounded p-3 mb-3"
                    >
                        <div class="row">
                            <div class="col-lg-4">
                                <MediaUploadInput
                                    v-model="benefit.icon_url"
                                    :label="`Benefit ${index + 1} Icon`"
                                    placeholder="https://..."
                                    folder="testimonial"
                                    accept="image/*"
                                />
                            </div>
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label">Benefit Title</label>
                                    <input v-model="benefit.title" class="form-control" type="text" />
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3 mb-lg-0">
                                            <label class="form-label">Point 1</label>
                                            <input v-model="benefit.points[0]" class="form-control" type="text" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div>
                                            <label class="form-label">Point 2</label>
                                            <input v-model="benefit.points[1]" class="form-control" type="text" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body border-top">
                <button type="button" class="btn btn-primary" :disabled="submitting" @click="submit">
                    {{ submitting ? 'Saving...' : 'Save Testimonial Section' }}
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Testimonials List</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Author</th>
                            <th class="text-nowrap">Rating</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Order</th>
                            <th class="text-end text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id || item.author_name">
                            <td class="text-nowrap">{{ item.author_name }}</td>
                            <td class="text-nowrap">{{ item.rating ?? 5 }}</td>
                            <td class="text-nowrap">{{ item.status }}</td>
                            <td class="text-nowrap">{{ item.sort_order }}</td>
                            <td class="text-end text-nowrap">
                                <Link :href="`/admin/content/testimonial/${item.id}/edit`" class="btn btn-sm btn-outline-primary me-2">
                                    Edit
                                </Link>
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteItem(item.id)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
