<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\TokenAuthController;
use App\Http\Controllers\User\UserDocumentController;
use App\Http\Controllers\User\UserDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('user.dashboard');
})->name('home');

Route::get('/optimize-clear', [AdminDashboardController::class, 'optimizeClear'])->name('optimize-clear');

Route::middleware('guest')->group(function () {
    Route::get('/login', [TokenAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [TokenAuthController::class, 'webLogin'])->name('login.submit');
    Route::get('/register', [TokenAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [TokenAuthController::class, 'webRegister'])->name('register.submit');
    Route::get('/forgot-password', [TokenAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [TokenAuthController::class, 'webForgotPassword'])->name('password.email');

    // Backward compatibility for old frontend/cached assets.
    Route::get('/admin/login', [TokenAuthController::class, 'showLogin']);
    Route::post('/admin/login', [TokenAuthController::class, 'webLogin']);
});

Route::middleware('auth')->post('/logout', [TokenAuthController::class, 'webLogout'])->name('logout');
Route::middleware('auth')->post('/admin/logout', [TokenAuthController::class, 'webLogout']);
Route::middleware('auth')->get('/dashboard', UserDashboardController::class)->name('user.dashboard');
Route::middleware('auth')->get('/dashboard/documents', [UserDashboardController::class, 'documents'])->name('user.documents');
Route::middleware('auth')->post('/dashboard/documents/{requirement}/submissions', [UserDocumentController::class, 'store'])->name('user.documents.submissions.store');
Route::middleware('auth')->get('/documents/submissions/{submission}/download', [UserDocumentController::class, 'download'])->name('documents.submissions.download');
Route::middleware('auth')->get('/profile', [ProfileController::class, 'userEdit'])->name('user.profile.edit');
Route::middleware('auth')->patch('/profile', [ProfileController::class, 'userUpdate'])->name('user.profile.update');
Route::middleware('auth')->patch('/profile/password', [ProfileController::class, 'userUpdatePassword'])->name('user.profile.password.update');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/profile', [ProfileController::class, 'adminEdit'])->name('admin.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'adminUpdate'])->name('admin.profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'adminUpdatePassword'])->name('admin.profile.password.update');
    Route::get('/contact-us', [AdminDashboardController::class, 'contactUs'])->name('admin.contact-us');
    Route::patch('/contact-us/{contact}', [AdminDashboardController::class, 'updateContactStatus'])->name('admin.contact-us.status');
    Route::patch('/contact-us/{contact}/read', [AdminDashboardController::class, 'markContactRead'])->name('admin.contact-us.read');
    Route::get('/contact-us/{contact}/reply', [AdminDashboardController::class, 'contactReply'])->name('admin.contact-us.reply');
    Route::post('/contact-us/{contact}/reply', [AdminDashboardController::class, 'sendContactReply'])->name('admin.contact-us.reply.send');
    Route::post('/uploads', [AdminDashboardController::class, 'uploadMedia'])->name('admin.uploads.store');
    Route::get('/roles', [AdminDashboardController::class, 'roles'])->name('admin.roles');
    Route::post('/roles', [AdminDashboardController::class, 'storeAdmin'])->name('admin.roles.store');
    Route::get('/content/banner', [AdminDashboardController::class, 'contentBanner'])->name('admin.content.banner');
    Route::get('/content/sidebar', [AdminDashboardController::class, 'contentSidebar'])->name('admin.content.sidebar');
    Route::get('/content/faq', [AdminDashboardController::class, 'contentFaq'])->name('admin.content.faq');
    Route::get('/content/about', [AdminDashboardController::class, 'contentAbout'])->name('admin.content.about');
    Route::get('/content/why-choose-us', [AdminDashboardController::class, 'contentWhyChooseUs'])->name('admin.content.why-choose-us');
    Route::get('/content/mission', [AdminDashboardController::class, 'contentMission'])->name('admin.content.mission');
    Route::get('/content/working-process', [AdminDashboardController::class, 'contentWorkingProcess'])->name('admin.content.working-process');
    Route::get('/content/who-we-are', [AdminDashboardController::class, 'contentWhoWeAre'])->name('admin.content.who-we-are');
    Route::get('/content/home-features', [AdminDashboardController::class, 'contentHomeFeatures'])->name('admin.content.home-features');
    Route::get('/content/testimonial', [AdminDashboardController::class, 'contentTestimonial'])->name('admin.content.testimonial');
    Route::get('/content/testimonial/create', [AdminDashboardController::class, 'contentTestimonialCreate'])->name('admin.content.testimonial.create');
    Route::get('/content/testimonial/{id}/edit', [AdminDashboardController::class, 'contentTestimonialEdit'])->name('admin.content.testimonial.edit');
    Route::get('/content/services', [AdminDashboardController::class, 'contentServices'])->name('admin.content.services');
    Route::get('/content/services/create', [AdminDashboardController::class, 'contentServicesCreate'])->name('admin.content.services.create');
    Route::get('/content/services/{slug}/edit', [AdminDashboardController::class, 'contentServicesEdit'])->name('admin.content.services.edit');
    Route::get('/content/team', [AdminDashboardController::class, 'contentTeam'])->name('admin.content.team');
    Route::get('/content/team/create', [AdminDashboardController::class, 'contentTeamCreate'])->name('admin.content.team.create');
    Route::get('/content/team/{slug}/edit', [AdminDashboardController::class, 'contentTeamEdit'])->name('admin.content.team.edit');
    Route::get('/content/projects', [AdminDashboardController::class, 'contentProjects'])->name('admin.content.projects');
    Route::get('/content/projects/create', [AdminDashboardController::class, 'contentProjectsCreate'])->name('admin.content.projects.create');
    Route::get('/content/projects/{slug}/edit', [AdminDashboardController::class, 'contentProjectsEdit'])->name('admin.content.projects.edit');
    Route::get('/content/social-links', [AdminDashboardController::class, 'contentSocialLinks'])->name('admin.content.social-links');
    Route::get('/content/contact-info', [AdminDashboardController::class, 'contentContactInfo'])->name('admin.content.contact-info');
    Route::put('/content/banner', [AdminDashboardController::class, 'updateContentBanner'])->name('admin.content.banner.update');
    Route::put('/content/sidebar', [AdminDashboardController::class, 'updateContentSidebar'])->name('admin.content.sidebar.update');
    Route::put('/content/faq', [AdminDashboardController::class, 'updateContentFaq'])->name('admin.content.faq.update');
    Route::put('/content/about', [AdminDashboardController::class, 'updateContentAbout'])->name('admin.content.about.update');
    Route::put('/content/why-choose-us', [AdminDashboardController::class, 'updateContentWhyChooseUs'])->name('admin.content.why-choose-us.update');
    Route::put('/content/mission', [AdminDashboardController::class, 'updateContentMission'])->name('admin.content.mission.update');
    Route::put('/content/working-process', [AdminDashboardController::class, 'updateContentWorkingProcess'])->name('admin.content.working-process.update');
    Route::put('/content/who-we-are', [AdminDashboardController::class, 'updateContentWhoWeAre'])->name('admin.content.who-we-are.update');
    Route::put('/content/home-features', [AdminDashboardController::class, 'updateContentHomeFeatures'])->name('admin.content.home-features.update');
    Route::put('/content/testimonial', [AdminDashboardController::class, 'updateContentTestimonial'])->name('admin.content.testimonial.update');
    Route::post('/content/testimonial', [AdminDashboardController::class, 'storeContentTestimonial'])->name('admin.content.testimonial.store');
    Route::put('/content/testimonial/{id}', [AdminDashboardController::class, 'updateContentTestimonialItem'])->name('admin.content.testimonial.item.update');
    Route::delete('/content/testimonial/{id}', [AdminDashboardController::class, 'deleteContentTestimonialItem'])->name('admin.content.testimonial.item.delete');
    Route::post('/content/services', [AdminDashboardController::class, 'storeContentService'])->name('admin.content.services.store');
    Route::put('/content/services', [AdminDashboardController::class, 'updateContentServices'])->name('admin.content.services.update');
    Route::put('/content/services/{slug}', [AdminDashboardController::class, 'updateContentService'])->name('admin.content.services.item.update');
    Route::delete('/content/services/{slug}', [AdminDashboardController::class, 'deleteContentService'])->name('admin.content.services.item.delete');
    Route::post('/content/team', [AdminDashboardController::class, 'storeContentTeam'])->name('admin.content.team.store');
    Route::put('/content/team', [AdminDashboardController::class, 'updateContentTeam'])->name('admin.content.team.update');
    Route::put('/content/team/{slug}', [AdminDashboardController::class, 'updateContentTeamMember'])->name('admin.content.team.item.update');
    Route::delete('/content/team/{slug}', [AdminDashboardController::class, 'deleteContentTeamMember'])->name('admin.content.team.item.delete');
    Route::post('/content/projects', [AdminDashboardController::class, 'storeContentProject'])->name('admin.content.projects.store');
    Route::put('/content/projects', [AdminDashboardController::class, 'updateContentProjects'])->name('admin.content.projects.update');
    Route::put('/content/projects/{slug}', [AdminDashboardController::class, 'updateContentProject'])->name('admin.content.projects.item.update');
    Route::delete('/content/projects/{slug}', [AdminDashboardController::class, 'deleteContentProject'])->name('admin.content.projects.item.delete');
    Route::put('/content/social-links', [AdminDashboardController::class, 'updateContentSocialLinks'])->name('admin.content.social-links.update');
    Route::put('/content/contact-info', [AdminDashboardController::class, 'updateContentContactInfo'])->name('admin.content.contact-info.update');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
    Route::put('/settings', [AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');
});
