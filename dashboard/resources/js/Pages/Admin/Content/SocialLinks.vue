<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

const iconOptions = [
    { label: 'Facebook', value: 'fab fa-facebook-f' },
    { label: 'Facebook Square', value: 'fab fa-facebook' },
    { label: 'Twitter', value: 'fab fa-twitter' },
    { label: 'X / Twitter', value: 'fa-brands fa-x-twitter' },
    { label: 'Instagram', value: 'fab fa-instagram' },
    { label: 'LinkedIn', value: 'fab fa-linkedin-in' },
    { label: 'YouTube', value: 'fab fa-youtube' },
    { label: 'Pinterest', value: 'fab fa-pinterest-p' },
    { label: 'TikTok', value: 'fab fa-tiktok' },
    { label: 'WhatsApp', value: 'fab fa-whatsapp' },
    { label: 'Telegram', value: 'fab fa-telegram-plane' },
    { label: 'Snapchat', value: 'fab fa-snapchat-ghost' },
    { label: 'Discord', value: 'fab fa-discord' },
    { label: 'Github', value: 'fab fa-github' },
];

const form = useForm({
    items: props.links.length
        ? props.links.map((item, index) => ({
            name: item.name ?? '',
            url: item.url ?? '',
            icon: item.icon ?? 'fab fa-facebook-f',
            status: item.status ?? 'Active',
            sort_order: item.sort_order ?? index + 1,
        }))
        : [],
    name: '',
    url: '',
    icon: 'fab fa-facebook-f',
    status: 'Active',
    sort_order: 0,
});

const submitting = ref(false);
const editingIndex = ref(null);

const resetEditor = () => {
    form.name = '';
    form.url = '';
    form.icon = 'fab fa-facebook-f';
    form.status = 'Active';
    form.sort_order = 0;
    editingIndex.value = null;
};

const addLink = () => {
    const payload = {
        name: form.name,
        url: form.url,
        icon: form.icon,
        status: form.status,
        sort_order: form.sort_order || form.items.length + 1,
    };

    if (editingIndex.value !== null) {
        form.items[editingIndex.value] = payload;
    } else {
        form.items.push(payload);
    }

    resetEditor();
    saveLinks();
};

const editLink = (index) => {
    const item = form.items[index];
    form.name = item.name;
    form.url = item.url;
    form.icon = item.icon;
    form.status = item.status;
    form.sort_order = item.sort_order || 0;
    editingIndex.value = index;
};

const removeLink = (index) => {
    form.items.splice(index, 1);
    saveLinks();
};

const saveLinks = () => {
    submitting.value = true;
    form.put('/admin/content/social-links', {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
};
</script>

<template>
    <Head title="Content - Social Links" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Social Link</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Social Link Form</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="addLink">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input v-model="form.name" class="form-control" type="text" placeholder="Facebook" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Link</label>
                                <input v-model="form.url" class="form-control" type="text" placeholder="https://facebook.com/yourpage" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Icon</label>
                                <select v-model="form.icon" class="form-select">
                                    <option v-for="icon in iconOptions" :key="icon.value" :value="icon.value">
                                        {{ icon.label }}
                                    </option>
                                </select>
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <span>Preview:</span>
                                    <i :class="form.icon"></i>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="!form.name || !form.url || !form.icon">
                                {{ editingIndex === null ? 'Add Social Link' : 'Update Social Link' }}
                            </button>
                            <button v-if="editingIndex !== null" type="button" class="btn btn-outline-secondary ms-2" @click="resetEditor">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Social Links</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Name</th>
                                    <th class="text-nowrap">Icon</th>
                                    <th class="text-nowrap">Link</th>
                                    <th class="text-nowrap">Status</th>
                                    <th class="text-end text-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(link, index) in form.items" :key="`${link.name}-${index}`">
                                    <td class="text-nowrap">{{ link.name }}</td>
                                    <td class="text-nowrap"><i :class="link.icon"></i></td>
                                    <td class="text-nowrap">{{ link.url }}</td>
                                    <td class="text-nowrap">{{ link.status }}</td>
                                    <td class="text-end text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="editLink(index)">Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="removeLink(index)">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body border-top">
                        <button type="button" class="btn btn-primary" :disabled="submitting" @click="saveLinks">
                            {{ submitting ? 'Saving...' : 'Save Social Links' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
