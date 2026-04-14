<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

const props = defineProps({
    homeContent: { type: Object, required: true },
    servicesPageContent: { type: Object, required: true },
});

const items = Array.isArray(props.servicesPageContent?.items) ? props.servicesPageContent.items : [];
const form = useForm({
    section: {
        subtitle: props.homeContent?.subtitle ?? 'Our services',
        title_before: props.homeContent?.title_before ?? 'Our',
        title_highlight: props.homeContent?.title_highlight ?? 'digital services',
        title_after: props.homeContent?.title_after ?? 'to grow your brand',
        description: props.homeContent?.description ?? '',
        cta_text: props.homeContent?.cta_text ?? 'all services',
        cta_url: props.homeContent?.cta_url ?? '/services',
        footer_text: props.homeContent?.footer_text ?? "Let's make something great work together.",
        footer_link_text: props.homeContent?.footer_link_text ?? 'get free quote',
        footer_link_url: props.homeContent?.footer_link_url ?? '/contact',
    },
});

const saveSection = () => {
    form.put('/admin/content/services', {
        preserveScroll: true,
    });
};

const deleteService = (slug) => {
    if (!window.confirm('Delete this service?')) {
        return;
    }

    router.delete(`/admin/content/services/${slug}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Content - Services" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Content Manage / Services</h1>
            <Link href="/admin/content/services/create" class="btn btn-primary">
                Create Service
            </Link>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Services Section Content</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input v-model="form.section.subtitle" class="form-control" type="text" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title Before Highlight</label>
                            <input v-model="form.section.title_before" class="form-control" type="text" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Highlighted Title</label>
                            <input v-model="form.section.title_highlight" class="form-control" type="text" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title After Highlight</label>
                            <input v-model="form.section.title_after" class="form-control" type="text" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea v-model="form.section.description" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CTA Text</label>
                            <input v-model="form.section.cta_text" class="form-control" type="text" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CTA URL</label>
                            <input v-model="form.section.cta_url" class="form-control" type="text" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Footer Text</label>
                            <input v-model="form.section.footer_text" class="form-control" type="text" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label">Footer Link Text</label>
                            <input v-model="form.section.footer_link_text" class="form-control" type="text" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label">Footer Link URL</label>
                            <input v-model="form.section.footer_link_url" class="form-control" type="text" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body border-top">
                <button type="button" class="btn btn-primary" :disabled="form.processing" @click="saveSection">
                    {{ form.processing ? 'Saving...' : 'Save Section Content' }}
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Services List</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap">Slug</th>
                            <th class="text-nowrap">Show On Home</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Order</th>
                            <th class="text-end text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.slug || item.name">
                            <td class="text-nowrap">{{ item.name }}</td>
                            <td class="text-nowrap">{{ item.slug }}</td>
                            <td class="text-nowrap">{{ item.show_on_home ? 'Yes' : 'No' }}</td>
                            <td class="text-nowrap">{{ item.status }}</td>
                            <td class="text-nowrap">{{ item.sort_order }}</td>
                            <td class="text-end text-nowrap">
                                <Link :href="`/admin/content/services/${item.slug}/edit`" class="btn btn-sm btn-outline-primary me-2">
                                    Edit
                                </Link>
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteService(item.slug)">
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
