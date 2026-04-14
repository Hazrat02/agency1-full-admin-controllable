<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import MediaUploadInput from '../../../Components/Admin/MediaUploadInput.vue';

const props = defineProps({
    mode: {
        type: String,
        required: true,
    },
    member: {
        type: Object,
        required: true,
    },
    originalSlug: {
        type: String,
        default: null,
    },
});

const defaultSkills = () => ([
    { label: 'Tecnology', percent: 90 },
    { label: 'Marketing', percent: 80 },
    { label: 'Business', percent: 75 },
]);

const defaultSocialLinks = () => ([
    { icon: 'fab fa-facebook-f', url: '#' },
    { icon: 'fab fa-pinterest-p', url: '#' },
    { icon: 'fab fa-instagram', url: '#' },
    { icon: 'fab fa-twitter', url: '#' },
]);

const socialIconOptions = [
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
    name: props.member.name ?? '',
    slug: props.member.slug ?? '',
    designation: props.member.designation ?? '',
    short_bio: props.member.short_bio ?? '',
    background_image_url: props.member.background_image_url ?? '',
    card_image_url: props.member.card_image_url ?? '',
    detail_image_url: props.member.detail_image_url ?? '',
    email: props.member.email ?? '',
    phone: props.member.phone ?? '',
    website: props.member.website ?? '',
    experience_title: props.member.experience_title ?? 'Personal Experience',
    experience_text: props.member.experience_text ?? '',
    skills: Array.isArray(props.member.skills) && props.member.skills.length ? props.member.skills : defaultSkills(),
    social_links: Array.isArray(props.member.social_links) && props.member.social_links.length ? props.member.social_links : defaultSocialLinks(),
    status: props.member.status ?? 'Active',
    show_on_home: props.member.show_on_home ?? true,
    show_on_team_page: props.member.show_on_team_page ?? true,
    sort_order: props.member.sort_order ?? 1,
});

const submit = () => {
    const url = props.mode === 'create'
        ? '/admin/content/team'
        : `/admin/content/team/${props.originalSlug}`;

    if (props.mode === 'create') {
        form.post(url);
        return;
    }

    form.put(url);
};
</script>

<template>
    <Head :title="mode === 'create' ? 'Create Team Member' : 'Edit Team Member'" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">{{ mode === 'create' ? 'Create Team Member' : 'Edit Team Member' }}</h1>
            <Link href="/admin/content/team" class="btn btn-outline-secondary">Back</Link>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Team Form</h5>
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
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Designation</label>
                                <input v-model="form.designation" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input v-model="form.sort_order" class="form-control" type="number" min="0" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Bio</label>
                        <textarea v-model="form.short_bio" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <MediaUploadInput
                                v-model="form.background_image_url"
                                label="Background Image URL"
                                placeholder="https://..."
                                folder="team"
                                accept="image/*"
                            />
                        </div>
                        <div class="col-lg-4">
                            <MediaUploadInput
                                v-model="form.card_image_url"
                                label="Card Image URL"
                                placeholder="https://..."
                                folder="team"
                                accept="image/*"
                            />
                        </div>
                        <div class="col-lg-4">
                            <MediaUploadInput
                                v-model="form.detail_image_url"
                                label="Detail Image URL"
                                placeholder="https://..."
                                folder="team"
                                accept="image/*"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input v-model="form.email" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input v-model="form.phone" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Website</label>
                                <input v-model="form.website" class="form-control" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Experience Title</label>
                        <input v-model="form.experience_title" class="form-control" type="text" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Experience Text</label>
                        <textarea v-model="form.experience_text" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div v-for="(skill, index) in form.skills" :key="`skill-${index}`" class="col-lg-4">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">Skill {{ index + 1 }}</h6>
                                <div class="mb-3">
                                    <label class="form-label">Label</label>
                                    <input v-model="skill.label" class="form-control" type="text" />
                                </div>
                                <div>
                                    <label class="form-label">Percent</label>
                                    <input v-model="skill.percent" class="form-control" type="number" min="0" max="100" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div v-for="(link, index) in form.social_links" :key="`social-${index}`" class="col-lg-6">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">Social Link {{ index + 1 }}</h6>
                                <div class="mb-3">
                                    <label class="form-label">Icon</label>
                                    <select v-model="link.icon" class="form-select">
                                        <option v-for="icon in socialIconOptions" :key="icon.value" :value="icon.value">
                                            {{ icon.label }}
                                        </option>
                                    </select>
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <span>Preview:</span>
                                        <i :class="link.icon"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">URL</label>
                                    <input v-model="link.url" class="form-control" type="text" placeholder="#" />
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
                        <input id="team_show_on_home" v-model="form.show_on_home" class="form-check-input" type="checkbox" />
                        <label class="form-check-label" for="team_show_on_home">Show on home section</label>
                    </div>
                    <div class="form-check mb-3">
                        <input id="team_show_on_page" v-model="form.show_on_team_page" class="form-check-input" type="checkbox" />
                        <label class="form-check-label" for="team_show_on_page">Show on team page</label>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.name || !form.designation">
                        {{ mode === 'create' ? 'Create Team Member' : 'Update Team Member' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
