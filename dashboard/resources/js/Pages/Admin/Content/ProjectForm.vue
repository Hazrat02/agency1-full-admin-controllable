<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import MediaUploadInput from '../../../Components/Admin/MediaUploadInput.vue';

const props = defineProps({
    mode: { type: String, required: true },
    project: { type: Object, required: true },
    originalSlug: { type: String, default: null },
});

const defaultGallery = () => ['', '', '', ''];
const defaultFacts = () => ['', '', '', ''];
const defaultResults = () => [
    { title: '', description: '' },
    { title: '', description: '' },
];

const form = useForm({
    name: props.project.name ?? '',
    slug: props.project.slug ?? '',
    category: props.project.category ?? 'DEVELOPMENT',
    short_description: props.project.short_description ?? '',
    project_date: props.project.project_date ?? '',
    home_image_url: props.project.home_image_url ?? '',
    list_image_url: props.project.list_image_url ?? '',
    detail_gallery_images: Array.isArray(props.project.detail_gallery_images) && props.project.detail_gallery_images.length ? props.project.detail_gallery_images : defaultGallery(),
    about_title: props.project.about_title ?? 'About The Project',
    about_heading: props.project.about_heading ?? '',
    about_text: props.project.about_text ?? '',
    client_name: props.project.client_name ?? '',
    project_type: props.project.project_type ?? '',
    website_url: props.project.website_url ?? '',
    facts_title: props.project.facts_title ?? 'Interesting facts in|Development',
    facts_text: props.project.facts_text ?? '',
    facts_list: Array.isArray(props.project.facts_list) && props.project.facts_list.length ? props.project.facts_list : defaultFacts(),
    results_title: props.project.results_title ?? 'The Results of|Our Project',
    results_text: props.project.results_text ?? '',
    results_items: Array.isArray(props.project.results_items) && props.project.results_items.length ? props.project.results_items : defaultResults(),
    show_on_home: props.project.show_on_home ?? true,
    show_on_projects_page: props.project.show_on_projects_page ?? true,
    status: props.project.status ?? 'Active',
    sort_order: props.project.sort_order ?? 1,
});

const submit = () => {
    const url = props.mode === 'create'
        ? '/admin/content/projects'
        : `/admin/content/projects/${props.originalSlug}`;

    if (props.mode === 'create') {
        form.post(url);
        return;
    }

    form.put(url);
};
</script>

<template>
    <Head :title="mode === 'create' ? 'Create Project' : 'Edit Project'" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">{{ mode === 'create' ? 'Create Project' : 'Edit Project' }}</h1>
            <Link href="/admin/content/projects" class="btn btn-outline-secondary">Back</Link>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Project Form</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
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
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <input v-model="form.category" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Project Date</label>
                                <input v-model="form.project_date" class="form-control" type="text" />
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
                        <label class="form-label">Short Description</label>
                        <textarea v-model="form.short_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <MediaUploadInput v-model="form.home_image_url" label="Home Work Image URL" placeholder="https://..." folder="projects" accept="image/*" />
                        </div>
                        <div class="col-lg-6">
                            <MediaUploadInput v-model="form.list_image_url" label="Projects List Image URL" placeholder="https://..." folder="projects" accept="image/*" />
                        </div>
                    </div>

                    <div class="row">
                        <div v-for="(image, index) in form.detail_gallery_images" :key="`gallery-${index}`" class="col-lg-6">
                            <MediaUploadInput v-model="form.detail_gallery_images[index]" :label="`Detail Gallery Image ${index + 1}`" placeholder="https://..." folder="projects" accept="image/*" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">About Title</label>
                        <input v-model="form.about_title" class="form-control" type="text" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">About Heading</label>
                        <input v-model="form.about_heading" class="form-control" type="text" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">About Text</label>
                        <textarea v-model="form.about_text" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Client Name</label>
                                <input v-model="form.client_name" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Project Type</label>
                                <input v-model="form.project_type" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Website URL</label>
                                <input v-model="form.website_url" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Facts Title</label>
                        <input v-model="form.facts_title" class="form-control" type="text" placeholder="Use | to split title lines" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Facts Text</label>
                        <textarea v-model="form.facts_text" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div v-for="(fact, index) in form.facts_list" :key="`fact-${index}`" class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Fact {{ index + 1 }}</label>
                                <input v-model="form.facts_list[index]" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Results Title</label>
                        <input v-model="form.results_title" class="form-control" type="text" placeholder="Use | to split title lines" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Results Text</label>
                        <textarea v-model="form.results_text" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div v-for="(item, index) in form.results_items" :key="`result-${index}`" class="col-lg-6">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">Result Item {{ index + 1 }}</h6>
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input v-model="item.title" class="form-control" type="text" />
                                </div>
                                <div>
                                    <label class="form-label">Description</label>
                                    <textarea v-model="item.description" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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

                    <div class="form-check mb-2">
                        <input id="project_show_on_home" v-model="form.show_on_home" class="form-check-input" type="checkbox" />
                        <label class="form-check-label" for="project_show_on_home">Show on home works section</label>
                    </div>
                    <div class="form-check mb-3">
                        <input id="project_show_on_page" v-model="form.show_on_projects_page" class="form-check-input" type="checkbox" />
                        <label class="form-check-label" for="project_show_on_page">Show on projects page</label>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.name">
                        {{ mode === 'create' ? 'Create Project' : 'Update Project' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
