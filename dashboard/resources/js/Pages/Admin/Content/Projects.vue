<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
});

const section = props.content?.section ?? {};
const items = Array.isArray(props.content?.items) ? props.content.items : [];

const form = useForm({
    section: {
        home_title: section.home_title ?? 'WORKS',
        home_heading_before: section.home_heading_before ?? 'Brands with cutting-edge digital',
        home_heading_highlight: section.home_heading_highlight ?? 'solutions & design',
        home_heading_after: section.home_heading_after ?? '',
        home_description: section.home_description ?? 'Empowering brands through innovative digital strategies, immersive design, and tailored solutions that drive growth and engagement.',
    },
    items,
});

const submitting = ref(false);

const saveSection = () => {
    submitting.value = true;
    form.put('/admin/content/projects', {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
};

const deleteProject = (slug) => {
    if (!window.confirm('Delete this project?')) {
        return;
    }

    router.delete(`/admin/content/projects/${slug}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Content - Projects" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Content Manage / Projects</h1>
            <Link href="/admin/content/projects/create" class="btn btn-primary">
                Create Project
            </Link>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Home Works Section</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Home Title</label>
                    <input v-model="form.section.home_title" class="form-control" type="text" />
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">Heading Before Highlight</label>
                            <input v-model="form.section.home_heading_before" class="form-control" type="text" />
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">Heading Highlight</label>
                            <input v-model="form.section.home_heading_highlight" class="form-control" type="text" />
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">Heading After Highlight</label>
                            <input v-model="form.section.home_heading_after" class="form-control" type="text" />
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Home Description</label>
                    <textarea v-model="form.section.home_description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="card-body border-top">
                <button type="button" class="btn btn-primary" :disabled="submitting" @click="saveSection">
                    {{ submitting ? 'Saving...' : 'Save Section Content' }}
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Projects List</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Category</th>
                            <th>Home</th>
                            <th>List</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in form.items" :key="item.slug || item.name">
                            <td class="text-nowrap">{{ item.name }}</td>
                            <td class="text-nowrap">{{ item.slug }}</td>
                            <td class="text-nowrap">{{ item.category }}</td>
                            <td class="text-nowrap">{{ item.show_on_home ? 'Yes' : 'No' }}</td>
                            <td class="text-nowrap">{{ item.show_on_projects_page ? 'Yes' : 'No' }}</td>
                            <td class="text-nowrap">{{ item.status }}</td>
                            <td class="text-nowrap">{{ item.sort_order }}</td>
                            <td class="text-end text-nowrap">
                                <Link :href="`/admin/content/projects/${item.slug}/edit`" class="btn btn-sm btn-outline-primary me-2">
                                    Edit
                                </Link>
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteProject(item.slug)">
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
