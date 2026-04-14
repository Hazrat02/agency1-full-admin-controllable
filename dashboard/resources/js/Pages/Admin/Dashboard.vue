<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recent_admins: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <div class="adminlte-page-header">
            <div>
                <h1 class="m-0">Dashboard</h1>
                <p class="text-muted mb-0">Admin overview with the existing live data from your current system.</p>
            </div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.admins_total }}</h3>
                        <p>Total Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <Link href="/admin/roles" class="small-box-footer">
                        Make admin <i class="fas fa-arrow-circle-right"></i>
                    </Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.admins_active }}</h3>
                        <p>Active Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <Link href="/admin/roles" class="small-box-footer">
                        Open admin list <i class="fas fa-arrow-circle-right"></i>
                    </Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.contacts_total }}</h3>
                        <p>Total Contacts</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <Link href="/admin/contact-us" class="small-box-footer">
                        Review contacts <i class="fas fa-arrow-circle-right"></i>
                    </Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.contacts_new }}</h3>
                        <p>New Contacts</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <Link href="/admin/contact-us" class="small-box-footer">
                        Open inbox <i class="fas fa-arrow-circle-right"></i>
                    </Link>
                </div>
            </div>
        </div>

        <div class="row">
            <section class="col-lg-7 connectedSortable">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-user-shield mr-1"></i>
                            Latest Admins
                        </h3>
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
                                <tr v-for="admin in recent_admins" :key="admin.id">
                                    <td>{{ admin.full_name }}</td>
                                    <td>{{ admin.email }}</td>
                                    <td>{{ admin.phone || 'N/A' }}</td>
                                    <td>{{ admin.country || 'N/A' }}</td>
                                </tr>
                                <tr v-if="!recent_admins.length">
                                    <td colspan="4" class="text-center text-muted">No admins found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-1"></i>
                            Quick Access
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="info-box mb-3 bg-light">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-id-card"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Profile</span>
                                        <Link href="/admin/profile" class="small">Update account</Link>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-box mb-3 bg-light">
                                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-cog"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Settings</span>
                                        <Link href="/admin/settings" class="small">Open settings</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="col-lg-5 connectedSortable">
                <div class="card bg-gradient-primary">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Admin Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="progress-group">
                            Admin activity
                            <span class="float-right"><b>{{ stats.admins_active }}</b>/{{ stats.admins_total }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" :style="{ width: `${stats.admins_total ? Math.round((stats.admins_active / stats.admins_total) * 100) : 0}%` }"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            New contact messages
                            <span class="float-right"><b>{{ stats.contacts_new }}</b>/{{ stats.contacts_total }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" :style="{ width: `${stats.contacts_total ? Math.round((stats.contacts_new / stats.contacts_total) * 100) : 0}%` }"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <Link href="/admin/roles" class="text-white">Open admin management</Link>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-1"></i>
                            Management Links
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <Link href="/admin/contact-us" class="nav-link">
                                    Contact messages
                                    <span class="float-right badge bg-primary">Open</span>
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/admin/content/banner" class="nav-link">
                                    Content manager
                                    <span class="float-right badge bg-danger">Edit</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
