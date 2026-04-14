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
        title: 'discovery phase',
        description: 'Initial consultation to understand your brand, goals, and target audience Conducting research and analysis of market trends.',
        icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-work-process-1.svg',
        link_url: '/contact',
    },
    {
        title: 'implementation',
        description: 'Initial consultation to understand your brand, goals, and target audience Conducting research and analysis of market trends.',
        icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-work-process-2.svg',
        link_url: '/contact',
    },
    {
        title: 'collaboration',
        description: 'Initial consultation to understand your brand, goals, and target audience Conducting research and analysis of market trends.',
        icon_url: 'https://html.awaikenthemes.com/artistic/images/icon-work-process-3.svg',
        link_url: '/contact',
    },
];

const items = Array.isArray(props.content.items) && props.content.items.length === 3
    ? props.content.items
    : defaultItems;

const form = useForm({
    subtitle: props.content.subtitle ?? 'how it work',
    title_before: props.content.title_before ?? 'Our proven',
    title_highlight: props.content.title_highlight ?? 'process',
    title_after: props.content.title_after ?? 'for achieving success',
    description: props.content.description ?? 'Our proven process combines research, strategy, and creativity to deliver tailored solutions that drive measurable results.',
    items: items.map((item) => ({
        title: item.title ?? '',
        description: item.description ?? '',
        icon_url: item.icon_url ?? '',
        link_url: item.link_url ?? '/contact',
    })),
});

const submit = () => {
    form.put('/admin/content/working-process');
};
</script>

<template>
    <Head title="Content - Working Process" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Working Process</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Working Process Form</h5>
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
                        <div v-for="(item, index) in form.items" :key="`process-${index}`" class="col-lg-4">
                            <div class="border rounded p-3 mb-3 h-100">
                                <h6 class="mb-3">Process Box {{ index + 1 }}</h6>
                                <MediaUploadInput
                                    v-model="item.icon_url"
                                    label="Icon URL"
                                    placeholder="https://..."
                                    folder="working-process"
                                    accept="image/*"
                                />
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input v-model="item.title" class="form-control" type="text" />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea v-model="item.description" class="form-control" rows="4"></textarea>
                                </div>
                                <div>
                                    <label class="form-label">Button URL</label>
                                    <input v-model="item.link_url" class="form-control" type="text" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.subtitle || !form.title_before || !form.title_highlight">
                        Save Working Process
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
