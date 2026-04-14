<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    banners: {
        type: Array,
        required: true,
    },
});

const defaultBanner = () => ({
    heading_prefix: '',
    typed_titles_text: '',
    description: '',
    button_text: 'get in touch',
    button_url: '/contact',
    background_video_url: 'https://demo.awaikenthemes.com/assets/videos/artistic-video.mp4',
    popup_video_url: 'https://www.youtube.com/watch?v=Y-x0efG1seA',
    circle_image_url: 'https://html.awaikenthemes.com/artistic/images/learn-more-circle.svg',
    position: 'Home Top',
    status: 'Active',
    sort_order: 0,
});

const mapBannerToForm = (item, index) => ({
    heading_prefix: item.heading_prefix ?? '',
    typed_titles_text: Array.isArray(item.typed_titles) ? item.typed_titles.join('\n') : '',
    description: item.description ?? '',
    button_text: item.button_text ?? 'get in touch',
    button_url: item.button_url ?? '/contact',
    background_video_url: item.background_video_url ?? 'https://demo.awaikenthemes.com/assets/videos/artistic-video.mp4',
    popup_video_url: item.popup_video_url ?? 'https://www.youtube.com/watch?v=Y-x0efG1seA',
    circle_image_url: item.circle_image_url ?? 'https://html.awaikenthemes.com/artistic/images/learn-more-circle.svg',
    position: item.position ?? 'Home Top',
    status: item.status ?? 'Active',
    sort_order: item.sort_order ?? index + 1,
});

const normalizeTypedTitles = (value) =>
    String(value ?? '')
        .split(/\r?\n|\|/)
        .map((item) => item.trim())
        .filter(Boolean);

const form = useForm({
    items: props.banners.length ? props.banners.map(mapBannerToForm) : [],
    ...defaultBanner(),
});

const submitting = ref(false);
const editingIndex = ref(null);

const sortedItems = computed(() =>
    form.items
        .map((item, index) => ({ ...item, _index: index }))
        .sort((first, second) => (first.sort_order ?? 0) - (second.sort_order ?? 0)),
);

function resetEditor() {
    Object.assign(form, defaultBanner());
    editingIndex.value = null;
}

function buildPayload() {
    return {
        heading_prefix: form.heading_prefix.trim(),
        typed_titles_text: form.typed_titles_text,
        description: form.description.trim(),
        button_text: form.button_text.trim() || 'get in touch',
        button_url: form.button_url.trim() || '/contact',
        background_video_url: form.background_video_url.trim() || 'https://demo.awaikenthemes.com/assets/videos/artistic-video.mp4',
        popup_video_url: form.popup_video_url.trim() || 'https://www.youtube.com/watch?v=Y-x0efG1seA',
        circle_image_url: form.circle_image_url.trim() || 'https://html.awaikenthemes.com/artistic/images/learn-more-circle.svg',
        position: form.position,
        status: form.status,
        sort_order: Number(form.sort_order) || form.items.length + 1,
    };
}

function syncItemsForSubmit() {
    form.items = form.items.map((item, index) => ({
        heading_prefix: item.heading_prefix,
        typed_titles: normalizeTypedTitles(item.typed_titles_text),
        description: item.description,
        button_text: item.button_text,
        button_url: item.button_url,
        background_video_url: item.background_video_url,
        popup_video_url: item.popup_video_url,
        circle_image_url: item.circle_image_url,
        position: item.position,
        status: item.status,
        sort_order: Number(item.sort_order) || index + 1,
    }));
}

function saveBanners() {
    syncItemsForSubmit();
    submitting.value = true;
    form.put('/admin/content/banner', {
        preserveScroll: true,
        onFinish: () => {
            form.items = form.items.map(mapBannerToForm);
            if (!form.hasErrors) {
                resetEditor();
            }
            submitting.value = false;
        },
    });
}

function addOrUpdateBanner() {
    const payload = buildPayload();

    if (!payload.heading_prefix || normalizeTypedTitles(payload.typed_titles_text).length === 0) {
        return;
    }

    if (editingIndex.value !== null) {
        form.items[editingIndex.value] = payload;
    } else {
        form.items.push(payload);
    }

    saveBanners();
}

function editBanner(index) {
    Object.assign(form, mapBannerToForm(form.items[index], index));
    editingIndex.value = index;
}

function removeBanner(index) {
    form.items.splice(index, 1);
    saveBanners();
}
</script>

<template>
    <Head title="Content - Banner" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Banner</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Hero Banner Form</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="addOrUpdateBanner">
                            <div class="mb-3">
                                <label class="form-label">Heading Prefix</label>
                                <textarea v-model="form.heading_prefix" class="form-control" rows="2" placeholder="Innovative solutions for"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Typed Words</label>
                                <textarea v-model="form.typed_titles_text" class="form-control" rows="4" placeholder="One item per line&#10;Digital World&#10;Social Marketing&#10;Art &amp; Design"></textarea>
                                <small class="text-muted">These lines feed the frontend typed animation.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea v-model="form.description" class="form-control" rows="4" placeholder="Hero description text"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Button Text</label>
                                <input v-model="form.button_text" class="form-control" type="text" placeholder="get in touch" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Button URL</label>
                                <input v-model="form.button_url" class="form-control" type="text" placeholder="/contact" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Background Video URL</label>
                                <input v-model="form.background_video_url" class="form-control" type="text" placeholder="https://demo.awaikenthemes.com/assets/videos/artistic-video.mp4" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Popup Video URL</label>
                                <input v-model="form.popup_video_url" class="form-control" type="text" placeholder="https://www.youtube.com/watch?v=..." />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Circle Image URL</label>
                                <input v-model="form.circle_image_url" class="form-control" type="text" placeholder="https://html.awaikenthemes.com/artistic/images/learn-more-circle.svg" />
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Position</label>
                                    <select v-model="form.position" class="form-select">
                                        <option>Home Top</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select v-model="form.status" class="form-select">
                                        <option>Active</option>
                                        <option>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input v-model="form.sort_order" class="form-control" type="number" min="1" />
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" :disabled="!form.heading_prefix">
                                    {{ editingIndex === null ? 'Add Hero Banner' : 'Update Hero Banner' }}
                                </button>
                                <button v-if="editingIndex !== null" type="button" class="btn btn-outline-secondary" @click="resetEditor">
                                    Cancel Edit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Existing Hero Banners</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Heading</th>
                                    <th>Typed Items</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(banner, index) in sortedItems" :key="`${banner.heading_prefix}-${index}`">
                                    <td>{{ banner.heading_prefix }}</td>
                                    <td>{{ normalizeTypedTitles(banner.typed_titles_text).length }}</td>
                                    <td>{{ banner.status }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="editBanner(banner._index)">Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="removeBanner(banner._index)">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="!sortedItems.length">
                                    <td colspan="4" class="text-center text-muted py-4">No banner configured yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body border-top">
                        <button type="button" class="btn btn-primary" :disabled="submitting" @click="saveBanners">
                            {{ submitting ? 'Saving...' : 'Save Banner Content' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
