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

const form = useForm({
    icon_url: props.content.icon_url ?? '',
    title: props.content.title ?? 'Company Mission',
    description: props.content.description ?? '',
    bullet_icon_url: props.content.bullet_icon_url ?? '',
    items: Array.isArray(props.content.items) && props.content.items.length === 4
        ? props.content.items
        : ['', '', '', ''],
});

const submit = () => {
    form.put('/admin/content/mission');
};
</script>

<template>
    <Head title="Content - Company Mission" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Company Mission</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Company Mission Form</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-lg-6">
                            <MediaUploadInput
                                v-model="form.icon_url"
                                label="Main Icon URL"
                                placeholder="https://..."
                                folder="mission"
                                accept="image/*"
                            />
                        </div>
                        <div class="col-lg-6">
                            <MediaUploadInput
                                v-model="form.bullet_icon_url"
                                label="Bullet Icon URL"
                                placeholder="https://..."
                                folder="mission"
                                accept="image/*"
                            />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input v-model="form.title" class="form-control" type="text" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea v-model="form.description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div v-for="(item, index) in form.items" :key="`mission-item-${index}`" class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Mission Item {{ index + 1 }}</label>
                                <input v-model="form.items[index]" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.title">
                        Save Company Mission
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
