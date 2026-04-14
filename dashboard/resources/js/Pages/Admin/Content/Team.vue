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
        subtitle: section.subtitle ?? 'Our Team',
        title: section.title ?? 'Meet Professional|Team members',
        description: section.description ?? '',
        cta_text: section.cta_text ?? 'All Member',
        cta_url: section.cta_url ?? '/team',
    },
    items,
});

const submitting = ref(false);

const saveSection = () => {
    submitting.value = true;
    form.put('/admin/content/team', {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
};

const deleteMember = (slug) => {
    if (!window.confirm('Delete this team member?')) {
        return;
    }

    router.delete(`/admin/content/team/${slug}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Content - Team" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Content Manage / Team</h1>
            <Link href="/admin/content/team/create" class="btn btn-primary">
                Create Team Member
            </Link>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Home Team Section</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input v-model="form.section.subtitle" class="form-control" type="text" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <textarea v-model="form.section.title" class="form-control" rows="3" placeholder="Use | to split title lines"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea v-model="form.section.description" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">CTA Text</label>
                            <input v-model="form.section.cta_text" class="form-control" type="text" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CTA URL</label>
                            <input v-model="form.section.cta_url" class="form-control" type="text" placeholder="/team or https://..." />
                        </div>
                    </div>
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
                <h5 class="card-title mb-0">Team List</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap">Slug</th>
                            <th class="text-nowrap">Designation</th>
                            <th class="text-nowrap">Home</th>
                            <th class="text-nowrap">List</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Order</th>
                            <th class="text-end text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in form.items" :key="item.slug || item.name">
                            <td class="text-nowrap">{{ item.name }}</td>
                            <td class="text-nowrap">{{ item.slug }}</td>
                            <td class="text-nowrap">{{ item.designation }}</td>
                            <td class="text-nowrap">{{ item.show_on_home ? 'Yes' : 'No' }}</td>
                            <td class="text-nowrap">{{ item.show_on_team_page ? 'Yes' : 'No' }}</td>
                            <td class="text-nowrap">{{ item.status }}</td>
                            <td class="text-nowrap">{{ item.sort_order }}</td>
                            <td class="text-end text-nowrap">
                                <Link :href="`/admin/content/team/${item.slug}/edit`" class="btn btn-sm btn-outline-primary me-2">
                                    Edit
                                </Link>
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteMember(item.slug)">
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
