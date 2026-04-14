<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    admins: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    full_name: '',
    phone: '',
    country: '',
    email: '',
    password: '',
    password_confirmation: '',
    profile_image: null,
});

const setProfileImage = (event) => {
    form.profile_image = event.target.files?.[0] ?? null;
};

const submit = () => {
    form.post('/admin/roles', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Make Admin" />
    <AdminLayout>
        <div class="adminlte-page-header">
            <div>
                <h1 class="m-0">Make Admin</h1>
                <p class="text-muted mb-0">Create admin accounts directly and manage the current admin list.</p>
            </div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                <li class="breadcrumb-item active">Make Admin</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ stats.admins_total || 0 }}</h3>
                        <p>Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ stats.admins_active || 0 }}</h3>
                        <p>Active in 7 Days</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ admins.length }}</h3>
                        <p>Active Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create New Admin</h3>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input v-model="form.full_name" type="text" class="form-control" />
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input v-model="form.phone" type="text" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Country</label>
                                        <input v-model="form.country" type="text" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input v-model="form.email" type="email" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" class="form-control" accept="image/*" @change="setProfileImage" />
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input v-model="form.password" type="password" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input v-model="form.password_confirmation" type="password" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="form.processing || !form.full_name || !form.email || !form.password"
                            >
                                Make Admin
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Admin List</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Country</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="admin in admins" :key="admin.id">
                                    <td>{{ admin.full_name }}</td>
                                    <td>{{ admin.email }}</td>
                                    <td>{{ admin.phone || 'N/A' }}</td>
                                    <td>{{ admin.country || 'N/A' }}</td>
                                </tr>
                                <tr v-if="!admins.length">
                                    <td colspan="4" class="text-center text-muted">No admin users found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Admin Access Summary</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Access</th>
                                    <th>Admins</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Admin</td>
                                    <td>{{ stats.admins_total || 0 }}</td>
                                    <td>Full admin panel access</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
