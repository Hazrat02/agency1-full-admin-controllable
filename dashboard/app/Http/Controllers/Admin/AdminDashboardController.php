<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\SiteContent;
use App\Models\User;
use App\Services\DocumentChecklistService;
use App\Support\SiteContentDefaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $adminCount = User::where('is_admin', true)->count();
        $recentAdmins = User::query()
            ->where('is_admin', true)
            ->latest('id')
            ->limit(6)
            ->get(['id', 'full_name', 'email', 'phone', 'country']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'admins_total' => $adminCount,
                'admins_active' => User::where('is_admin', true)
                    ->whereDate('updated_at', '>=', now()->subDays(7))
                    ->count(),
                'contacts_total' => ContactSubmission::count(),
                'contacts_new' => ContactSubmission::where('status', 'New')->count(),
            ],
            'recent_admins' => $recentAdmins,
        ]);
    }

    public function optimizeClear(): RedirectResponse
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('optimize');

            return redirect()->route('admin.dashboard')->with('status', 'Application cache cleared and optimized successfully.');
        } catch (\Throwable $exception) {
            Log::error('Optimize clear route failed', [
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('admin.dashboard')->withErrors([
                'general' => 'Unable to clear and optimize application cache right now.',
            ]);
        }
    }

    public function users(Request $request, DocumentChecklistService $service): Response
    {
        $filters = $request->validate([
            'country' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:admin,user,active,banned'],
            'completion' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $query = User::query()->latest('id');

        if (! empty($filters['country'])) {
            $query->where('country', 'like', '%' . $filters['country'] . '%');
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'admin') {
                $query->where('is_admin', true);
            } elseif ($filters['status'] === 'user') {
                $query->where('is_admin', false);
            } elseif ($filters['status'] === 'active') {
                $query->where('is_banned', false);
            } elseif ($filters['status'] === 'banned') {
                $query->where('is_banned', true);
            }
        }

        $users = $query
            ->limit(100)
            ->get(['id', 'full_name', 'email', 'phone', 'country', 'is_admin', 'is_banned']);

        $rows = $users->map(function (User $user) use ($service): array {
            $completion = $user->is_admin ? 0 : (int) ($service->syncAndGetProgress($user)['completion_percentage'] ?? 0);

            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'is_admin' => $user->is_admin,
                'is_banned' => $user->is_banned,
                'completion_percentage' => $completion,
            ];
        });

        if (isset($filters['completion']) && $filters['completion'] !== null) {
            $rows = $rows->filter(fn (array $row) => (int) $row['completion_percentage'] >= (int) $filters['completion'])->values();
        }

        $countries = User::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->values();

        return Inertia::render('Admin/Users', [
            'users' => $rows->values(),
            'countries' => $countries,
            'filters' => [
                'country' => $filters['country'] ?? '',
                'status' => $filters['status'] ?? '',
                'completion' => isset($filters['completion']) ? (string) $filters['completion'] : '',
            ],
        ]);
    }

    public function userView(User $user): Response
    {
        $user->loadCount([
            'requiredDocuments as documents_total',
            'requiredDocuments as documents_missing' => fn ($query) => $query->where('status', 'missing'),
            'requiredDocuments as documents_approved' => fn ($query) => $query->where('status', 'approved'),
            'requiredDocuments as documents_rejected' => fn ($query) => $query->where('status', 'rejected'),
            'requiredDocuments as documents_waived' => fn ($query) => $query->where('status', 'waived'),
            'documentSubmissions as submissions_total',
        ]);

        $recentSubmissions = $user->documentSubmissions()
            ->latest('id')
            ->limit(8)
            ->get(['id', 'file_name', 'review_status', 'review_note', 'created_at']);

        return Inertia::render('Admin/UserView', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'marital_status' => $user->marital_status,
                'passport_type' => $user->passport_type,
                'destination_country' => $user->destination_country,
                'is_admin' => $user->is_admin,
                'is_banned' => $user->is_banned,
                'banned_at' => optional($user->banned_at)->toDateTimeString(),
                'profile_image_url' => $user->profile_image_url,
                'email_verified_at' => optional($user->email_verified_at)->toDateTimeString(),
                'created_at' => optional($user->created_at)->toDateTimeString(),
                'updated_at' => optional($user->updated_at)->toDateTimeString(),
            ],
            'document_summary' => [
                'total' => (int) $user->documents_total,
                'missing' => (int) $user->documents_missing,
                'approved' => (int) $user->documents_approved,
                'rejected' => (int) $user->documents_rejected,
                'waived' => (int) $user->documents_waived,
                'submissions_total' => (int) $user->submissions_total,
            ],
            'recent_submissions' => $recentSubmissions,
        ]);
    }

    public function loginAsUser(Request $request, User $user): RedirectResponse
    {
        if ($user->is_admin) {
            return back()->withErrors(['user' => 'Cannot log in as an admin user.']);
        }

        if ($user->is_banned) {
            return back()->withErrors(['user' => 'Cannot log in as a banned user.']);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard');
    }

    public function toggleBanUser(User $user): RedirectResponse
    {
        if ($user->is_admin) {
            return back()->withErrors(['user' => 'Admin users cannot be banned from this action.']);
        }

        $user->update([
            'is_banned' => ! $user->is_banned,
            'banned_at' => $user->is_banned ? null : now(),
        ]);

        return back()->with('status', $user->is_banned ? 'User banned successfully.' : 'User unbanned successfully.');
    }

    public function contactUs(): Response
    {
        return Inertia::render('Admin/ContactUs', [
            'contacts' => ContactSubmission::query()
                ->latest('id')
                ->limit(200)
                ->get(['id', 'name', 'email', 'phone', 'subject', 'message', 'status', 'is_read', 'read_at', 'replied_at', 'created_at']),
        ]);
    }

    public function contactReply(ContactSubmission $contact): Response
    {
        $this->markAsRead($contact);

        return Inertia::render('Admin/ContactReply', [
            'contact' => $contact->only([
                'id',
                'name',
                'email',
                'phone',
                'subject',
                'message',
                'status',
                'is_read',
                'read_at',
                'replied_at',
                'created_at',
            ]),
        ]);
    }

    public function markContactRead(ContactSubmission $contact): RedirectResponse
    {
        $this->markAsRead($contact);

        return back()->with('status', 'Marked as read.');
    }

    public function updateContactStatus(Request $request, ContactSubmission $contact): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:New,In Progress,Resolved'],
        ]);

        $contact->update([
            'status' => $data['status'],
        ]);

        return back()->with('status', 'Contact status updated.');
    }

    public function sendContactReply(Request $request, ContactSubmission $contact): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'mark_resolved' => ['nullable', 'boolean'],
        ]);

        $this->sendReplyMail($contact, $data['subject'], $data['message']);

        $contact->update([
            'replied_at' => now(),
            'status' => ($data['mark_resolved'] ?? false) ? 'Resolved' : $contact->status,
            'is_read' => true,
            'read_at' => $contact->read_at ?? now(),
        ]);

        return redirect()->route('admin.contact-us.reply', $contact)->with('status', 'Reply sent successfully.');
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'folder' => ['nullable', 'string', 'max:100'],
        ]);

        $folder = trim((string) ($data['folder'] ?? 'content'), '/');
        $path = $request->file('file')->store($folder !== '' ? $folder : 'content', 'public');

        return response()->json([
            'url' => '/storage/' . ltrim($path, '/'),
        ]);
    }

    public function roles(): Response
    {
        return Inertia::render('Admin/Roles', [
            'stats' => [
                'admins_total' => User::where('is_admin', true)->count(),
                'admins_active' => User::where('is_admin', true)
                    ->whereDate('updated_at', '>=', now()->subDays(7))
                    ->count(),
            ],
            'admins' => User::query()
                ->where('is_admin', true)
                ->latest('id')
                ->get(['id', 'full_name', 'email', 'phone', 'country', 'profile_image']),
        ]);
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
        }

        User::create([
            'name' => $data['full_name'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'country' => $data['country'],
            'profile_image' => $profileImagePath,
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        return back()->with('status', 'Admin created successfully.');
    }

    public function contentBanner(): Response
    {
        return Inertia::render('Admin/Content/Banner', [
            'banners' => $this->normalizedBannerItems(
                $this->contentValue('banner', SiteContentDefaults::banners())
            ),
        ]);
    }

    public function contentSidebar(): Response
    {
        return Inertia::render('Admin/Content/Sidebar', [
            'links' => $this->contentValue('sidebar', SiteContentDefaults::sidebarLinks()),
        ]);
    }

    public function contentFaq(): Response
    {
        return Inertia::render('Admin/Content/Faq', [
            'faqs' => $this->contentValue('faq', SiteContentDefaults::faqs()),
        ]);
    }

    public function contentAbout(): Response
    {
        return Inertia::render('Admin/Content/About', [
            'content' => $this->contentValue('about', SiteContentDefaults::aboutContent()),
        ]);
    }

    public function contentWhyChooseUs(): Response
    {
        return Inertia::render('Admin/Content/WhyChooseUs', [
            'content' => $this->contentValue('why_choose_us', SiteContentDefaults::whyChooseUsContent()),
        ]);
    }

    public function contentMission(): Response
    {
        return Inertia::render('Admin/Content/Mission', [
            'content' => $this->contentValue('mission', SiteContentDefaults::missionContent()),
        ]);
    }

    public function contentWorkingProcess(): Response
    {
        return Inertia::render('Admin/Content/WorkingProcess', [
            'content' => $this->contentValue('working_process', SiteContentDefaults::workingProcessContent()),
        ]);
    }

    public function contentWhoWeAre(): Response
    {
        return Inertia::render('Admin/Content/WhoWeAre', [
            'content' => $this->contentValue('who_we_are', SiteContentDefaults::whoWeAreContent()),
        ]);
    }

    public function contentHomeFeatures(): Response
    {
        return Inertia::render('Admin/Content/HomeFeatures', [
            'content' => $this->contentValue('home_features', SiteContentDefaults::homeFeaturesContent()),
        ]);
    }

    public function contentTestimonial(): Response
    {
        return Inertia::render('Admin/Content/Testimonial', [
            'content' => $this->contentValue('testimonial', SiteContentDefaults::testimonialContent()),
        ]);
    }

    public function contentTestimonialCreate(): Response
    {
        return Inertia::render('Admin/Content/TestimonialForm', [
            'mode' => 'create',
            'testimonial' => $this->emptyTestimonialPayload(),
            'originalId' => null,
        ]);
    }

    public function contentTestimonialEdit(string $id): Response
    {
        $content = $this->contentValue('testimonial', SiteContentDefaults::testimonialContent());
        $testimonial = collect($content['items'] ?? [])->first(fn (array $item) => ($item['id'] ?? '') === $id);

        abort_unless($testimonial, 404);

        return Inertia::render('Admin/Content/TestimonialForm', [
            'mode' => 'edit',
            'testimonial' => $testimonial,
            'originalId' => $id,
        ]);
    }

    public function contentServices(): Response
    {
        return Inertia::render('Admin/Content/Services', [
            'homeContent' => $this->contentValue('home_services', SiteContentDefaults::homeServicesContent()),
            'servicesPageContent' => $this->contentValue('services_page', SiteContentDefaults::servicesPageContent()),
        ]);
    }

    public function contentServicesCreate(): Response
    {
        return Inertia::render('Admin/Content/ServiceForm', [
            'mode' => 'create',
            'service' => $this->emptyServicePayload(),
            'originalSlug' => null,
        ]);
    }

    public function contentServicesEdit(string $slug): Response
    {
        $content = $this->contentValue('services_page', SiteContentDefaults::servicesPageContent());
        $service = collect($content['items'] ?? [])->first(fn (array $item) => ($item['slug'] ?? '') === $slug);

        abort_unless($service, 404);

        return Inertia::render('Admin/Content/ServiceForm', [
            'mode' => 'edit',
            'service' => $service,
            'originalSlug' => $slug,
        ]);
    }

    public function contentTeam(): Response
    {
        return Inertia::render('Admin/Content/Team', [
            'content' => $this->contentValue('team', SiteContentDefaults::teamContent()),
        ]);
    }

    public function contentTeamCreate(): Response
    {
        return Inertia::render('Admin/Content/TeamForm', [
            'mode' => 'create',
            'member' => $this->emptyTeamPayload(),
            'originalSlug' => null,
        ]);
    }

    public function contentTeamEdit(string $slug): Response
    {
        $content = $this->contentValue('team', SiteContentDefaults::teamContent());
        $member = collect($content['items'] ?? [])->first(fn (array $item) => ($item['slug'] ?? '') === $slug);

        abort_unless($member, 404);

        return Inertia::render('Admin/Content/TeamForm', [
            'mode' => 'edit',
            'member' => $member,
            'originalSlug' => $slug,
        ]);
    }

    public function contentProjects(): Response
    {
        return Inertia::render('Admin/Content/Projects', [
            'content' => $this->contentValue('projects', SiteContentDefaults::projectsContent()),
        ]);
    }

    public function contentProjectsCreate(): Response
    {
        return Inertia::render('Admin/Content/ProjectForm', [
            'mode' => 'create',
            'project' => $this->emptyProjectPayload(),
            'originalSlug' => null,
        ]);
    }

    public function contentProjectsEdit(string $slug): Response
    {
        $content = $this->contentValue('projects', SiteContentDefaults::projectsContent());
        $project = collect($content['items'] ?? [])->first(fn (array $item) => ($item['slug'] ?? '') === $slug);

        abort_unless($project, 404);

        return Inertia::render('Admin/Content/ProjectForm', [
            'mode' => 'edit',
            'project' => $project,
            'originalSlug' => $slug,
        ]);
    }

    public function contentSocialLinks(): Response
    {
        return Inertia::render('Admin/Content/SocialLinks', [
            'links' => $this->contentValue('social_links', SiteContentDefaults::socialLinks()),
        ]);
    }

    public function contentContactInfo(): Response
    {
        return Inertia::render('Admin/Content/ContactInfo', [
            'info' => $this->contentValue('contact_info', SiteContentDefaults::contactInfo()),
        ]);
    }

    public function updateContentBanner(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.heading_prefix' => ['required', 'string', 'max:255'],
            'items.*.typed_titles' => ['required', 'array', 'min:1'],
            'items.*.typed_titles.*' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.button_text' => ['nullable', 'string', 'max:100'],
            'items.*.button_url' => ['nullable', 'string', 'max:500'],
            'items.*.background_video_url' => ['nullable', 'string', 'max:2000'],
            'items.*.popup_video_url' => ['nullable', 'string', 'max:2000'],
            'items.*.circle_image_url' => ['nullable', 'string', 'max:2000'],
            'items.*.position' => ['nullable', 'string', 'max:100'],
            'items.*.status' => ['nullable', 'string', 'max:20'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $items = collect($data['items'])
            ->map(fn (array $item, int $index): array => $this->normalizeBannerItem($item, $index + 1))
            ->sortBy('sort_order')
            ->values()
            ->all();

        $this->storeContentValue('banner', $items);

        return back()->with('status', 'Banner content updated.');
    }

    private function normalizedBannerItems(mixed $value): array
    {
        return collect(is_array($value) ? $value : [])
            ->map(fn (array $item, int $index): array => $this->normalizeBannerItem($item, $index + 1))
            ->sortBy('sort_order')
            ->values()
            ->all();
    }

    private function normalizeBannerItem(array $item, int $defaultSortOrder): array
    {
        $default = collect(SiteContentDefaults::banners())
            ->first(fn (array $banner) => ($banner['position'] ?? '') === ($item['position'] ?? ''))
            ?? SiteContentDefaults::banners()[0];

        $typedTitles = $item['typed_titles'] ?? $item['subtitle'] ?? [];

        if (is_string($typedTitles)) {
            $typedTitles = preg_split('/\r\n|\r|\n|\|/', $typedTitles);
        }

        $typedTitles = collect(is_array($typedTitles) ? $typedTitles : [])
            ->map(fn ($title) => trim((string) $title))
            ->filter()
            ->values()
            ->all();

        return [
            'heading_prefix' => $item['heading_prefix'] ?? $item['title'] ?? ($default['heading_prefix'] ?? ''),
            'typed_titles' => $typedTitles !== [] ? $typedTitles : ($default['typed_titles'] ?? []),
            'description' => $item['description'] ?? ($default['description'] ?? ''),
            'button_text' => $item['button_text'] ?? ($default['button_text'] ?? 'get in touch'),
            'button_url' => $item['button_url'] ?? ($default['button_url'] ?? '/contact'),
            'background_video_url' => $item['background_video_url'] ?? ($item['image_url'] ?? '') ?: ($default['background_video_url'] ?? ''),
            'popup_video_url' => $item['popup_video_url'] ?? $item['video_url'] ?? ($default['popup_video_url'] ?? ''),
            'circle_image_url' => $item['circle_image_url'] ?? ($default['circle_image_url'] ?? ''),
            'position' => $item['position'] ?? ($default['position'] ?? 'Home Top'),
            'status' => $item['status'] ?? ($default['status'] ?? 'Active'),
            'sort_order' => $item['sort_order'] ?? $defaultSortOrder,
        ];
    }

    public function updateContentSidebar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.label' => ['required', 'string', 'max:100'],
            'items.*.url' => ['required', 'string', 'max:500'],
            'items.*.status' => ['nullable', 'string', 'max:20'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $items = collect($data['items'])->map(function (array $item, int $index): array {
            return [
                'label' => $item['label'],
                'url' => $item['url'],
                'status' => $item['status'] ?? 'Active',
                'sort_order' => $item['sort_order'] ?? ($index + 1),
            ];
        })->sortBy('sort_order')->values()->all();

        $this->storeContentValue('sidebar', $items);

        return back()->with('status', 'Sidebar content updated.');
    }

    public function updateContentFaq(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.question' => ['required', 'string', 'max:500'],
            'items.*.answer' => ['required', 'string'],
            'items.*.category' => ['nullable', 'string', 'max:100'],
            'items.*.status' => ['nullable', 'string', 'max:20'],
        ]);

        $items = collect($data['items'])->map(function (array $item): array {
            return [
                'question' => $item['question'],
                'answer' => $item['answer'],
                'category' => $item['category'] ?? 'General',
                'status' => $item['status'] ?? 'Published',
            ];
        })->values()->all();

        $this->storeContentValue('faq', $items);

        return back()->with('status', 'FAQ content updated.');
    }

    public function updateContentAbout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'agency_subtitle' => ['required', 'string', 'max:255'],
            'agency_title' => ['required', 'string', 'max:255'],
            'agency_cta_text' => ['nullable', 'string', 'max:100'],
            'agency_cta_url' => ['nullable', 'string', 'max:500'],
            'agency_items' => ['required', 'array', 'size:4'],
            'agency_items.*.icon_url' => ['nullable', 'string', 'max:2000'],
            'agency_items.*.title' => ['required', 'string', 'max:255'],
            'agency_items.*.description' => ['nullable', 'string'],
        ]);

        $defaults = SiteContentDefaults::aboutContent();
        $existing = $this->contentValue('about', $defaults);

        $this->storeContentValue('about', [
            'page_title' => $existing['page_title'] ?? ($defaults['page_title'] ?? 'About|us'),
            'breadcrumb_label' => $existing['breadcrumb_label'] ?? ($defaults['breadcrumb_label'] ?? 'About Us'),
            'page_title_bg_url' => $existing['page_title_bg_url'] ?? ($defaults['page_title_bg_url'] ?? ''),
            'main_image_url' => $existing['main_image_url'] ?? ($defaults['main_image_url'] ?? ''),
            'subtitle' => $existing['subtitle'] ?? ($defaults['subtitle'] ?? 'About Company'),
            'title' => $existing['title'] ?? ($defaults['title'] ?? ''),
            'experience_value' => $existing['experience_value'] ?? ($defaults['experience_value'] ?? '0'),
            'experience_suffix' => $existing['experience_suffix'] ?? ($defaults['experience_suffix'] ?? '+'),
            'experience_label' => $existing['experience_label'] ?? ($defaults['experience_label'] ?? ''),
            'icon_url' => $existing['icon_url'] ?? ($defaults['icon_url'] ?? ''),
            'description' => $existing['description'] ?? ($defaults['description'] ?? ''),
            'cta_text' => $existing['cta_text'] ?? ($defaults['cta_text'] ?? 'More About'),
            'cta_url' => $existing['cta_url'] ?? ($defaults['cta_url'] ?? '/about'),
            'agency_subtitle' => $data['agency_subtitle'],
            'agency_title' => $data['agency_title'],
            'agency_cta_text' => $data['agency_cta_text'] ?? ($defaults['agency_cta_text'] ?? 'more about'),
            'agency_cta_url' => $data['agency_cta_url'] ?? ($defaults['agency_cta_url'] ?? '/about'),
            'agency_items' => collect($data['agency_items'])->map(function (array $item, int $index) use ($defaults): array {
                $defaultItem = collect($defaults['agency_items'] ?? [])->get($index, []);

                return [
                    'icon_url' => $item['icon_url'] ?: ($defaultItem['icon_url'] ?? ''),
                    'title' => $item['title'] ?? ($defaultItem['title'] ?? ''),
                    'description' => $item['description'] ?? ($defaultItem['description'] ?? ''),
                ];
            })->values()->all(),
            'success_subtitle' => $existing['success_subtitle'] ?? ($defaults['success_subtitle'] ?? ''),
            'success_title' => $existing['success_title'] ?? ($defaults['success_title'] ?? ''),
            'success_year_value' => $existing['success_year_value'] ?? ($defaults['success_year_value'] ?? '0'),
            'success_year_suffix' => $existing['success_year_suffix'] ?? ($defaults['success_year_suffix'] ?? 'Year'),
            'success_year_description' => $existing['success_year_description'] ?? ($defaults['success_year_description'] ?? ''),
            'success_award_image_url' => $existing['success_award_image_url'] ?? ($defaults['success_award_image_url'] ?? ''),
            'success_circle_icon_url' => $existing['success_circle_icon_url'] ?? ($defaults['success_circle_icon_url'] ?? ''),
            'success_stats' => $existing['success_stats'] ?? ($defaults['success_stats'] ?? []),
        ]);

        return back()->with('status', 'About content updated.');
    }

    public function updateContentWhyChooseUs(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subtitle' => ['required', 'string', 'max:255'],
            'title_before' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'title_after' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'items' => ['required', 'array', 'size:3'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:2000'],
        ]);

        $defaults = SiteContentDefaults::whyChooseUsContent();

        $items = collect($data['items'])->map(function (array $item, int $index) use ($defaults): array {
            $defaultItem = collect($defaults['items'] ?? [])->get($index, []);

            return [
                'title' => $item['title'],
                'description' => $item['description'] ?? '',
            ];
        })->values()->all();

        $this->storeContentValue('why_choose_us', [
            'subtitle' => $data['subtitle'],
            'title_before' => $data['title_before'],
            'title_highlight' => $data['title_highlight'],
            'title_after' => $data['title_after'] ?? '',
            'description' => $data['description'],
            'items' => $items,
            'image_url' => $data['image_url'] ?: ($defaults['image_url'] ?? ''),
        ]);

        return back()->with('status', 'Why Choose Us content updated.');
    }

    public function updateContentMission(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'icon_url' => ['nullable', 'string', 'max:2000'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'bullet_icon_url' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'size:4'],
            'items.*' => ['nullable', 'string', 'max:255'],
        ]);

        $defaults = SiteContentDefaults::missionContent();

        $this->storeContentValue('mission', [
            'icon_url' => $data['icon_url'] ?: ($defaults['icon_url'] ?? ''),
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'bullet_icon_url' => $data['bullet_icon_url'] ?: ($defaults['bullet_icon_url'] ?? ''),
            'items' => collect($data['items'])->map(fn ($item, $index) => $item ?: (collect($defaults['items'] ?? [])->get($index, '')))->values()->all(),
        ]);

        return back()->with('status', 'Mission content updated.');
    }

    public function updateContentWorkingProcess(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subtitle' => ['required', 'string', 'max:255'],
            'title_before' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'title_after' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'items' => ['required', 'array', 'size:3'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.icon_url' => ['nullable', 'string', 'max:2000'],
            'items.*.link_url' => ['nullable', 'string', 'max:500'],
        ]);

        $defaults = SiteContentDefaults::workingProcessContent();

        $items = collect($data['items'])->map(function (array $item, int $index) use ($defaults): array {
            $defaultItem = collect($defaults['items'] ?? [])->get($index, []);

            return [
                'title' => $item['title'],
                'description' => $item['description'] ?? '',
                'icon_url' => $item['icon_url'] ?: ($defaultItem['icon_url'] ?? ''),
                'link_url' => $item['link_url'] ?? ($defaultItem['link_url'] ?? '/contact'),
            ];
        })->values()->all();

        $this->storeContentValue('working_process', [
            'subtitle' => $data['subtitle'],
            'title_before' => $data['title_before'],
            'title_highlight' => $data['title_highlight'],
            'title_after' => $data['title_after'] ?? '',
            'description' => $data['description'],
            'items' => $items,
        ]);

        return back()->with('status', 'Working Process content updated.');
    }

    public function updateContentHomeFeatures(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subtitle' => ['required', 'string', 'max:255'],
            'title_before' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'title_after' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'cta_text' => ['required', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'size:2'],
            'items.*.image_url' => ['nullable', 'string', 'max:2000'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
        ]);

        $defaults = SiteContentDefaults::homeFeaturesContent();

        $items = collect($data['items'])->map(function (array $item, int $index) use ($defaults): array {
            $defaultItem = collect($defaults['items'] ?? [])->get($index, []);

            return [
                'image_url' => $item['image_url'] ?: ($defaultItem['image_url'] ?? ''),
                'title' => $item['title'],
                'description' => $item['description'] ?? '',
            ];
        })->values()->all();

        $this->storeContentValue('home_features', [
            'subtitle' => $data['subtitle'],
            'title_before' => $data['title_before'],
            'title_highlight' => $data['title_highlight'],
            'title_after' => $data['title_after'] ?? '',
            'description' => $data['description'],
            'cta_text' => $data['cta_text'],
            'cta_url' => $data['cta_url'] ?? ($defaults['cta_url'] ?? '/contact'),
            'items' => $items,
        ]);

        return back()->with('status', 'Home Features content updated.');
    }

    public function updateContentWhoWeAre(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subtitle' => ['required', 'string', 'max:255'],
            'title_before' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'title_after' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'video_url' => ['nullable', 'string', 'max:2000'],
            'video_image_url' => ['nullable', 'string', 'max:2000'],
            'cta_text' => ['required', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'review_label' => ['required', 'string', 'max:255'],
            'review_images' => ['required', 'array', 'size:5'],
            'review_images.*' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'size:4'],
            'items.*.icon_url' => ['nullable', 'string', 'max:2000'],
            'items.*.value' => ['required', 'string', 'max:50'],
            'items.*.suffix' => ['nullable', 'string', 'max:20'],
            'items.*.label' => ['required', 'string', 'max:255'],
        ]);

        $defaults = SiteContentDefaults::whoWeAreContent();

        $items = collect($data['items'])->map(function (array $item, int $index) use ($defaults): array {
            $defaultItem = collect($defaults['items'] ?? [])->get($index, []);

            return [
                'icon_url' => $item['icon_url'] ?: ($defaultItem['icon_url'] ?? ''),
                'value' => $item['value'] ?? ($defaultItem['value'] ?? '0'),
                'suffix' => $item['suffix'] ?? ($defaultItem['suffix'] ?? ''),
                'label' => $item['label'] ?? ($defaultItem['label'] ?? ''),
            ];
        })->values()->all();

        $reviewImages = collect($data['review_images'])->map(function ($image, int $index) use ($defaults) {
            return $image ?: (collect($defaults['review_images'] ?? [])->get($index, ''));
        })->values()->all();

        $this->storeContentValue('who_we_are', [
            'subtitle' => $data['subtitle'],
            'title_before' => $data['title_before'],
            'title_highlight' => $data['title_highlight'],
            'title_after' => $data['title_after'] ?? '',
            'description' => $data['description'],
            'video_url' => $data['video_url'] ?: ($defaults['video_url'] ?? ''),
            'video_image_url' => $data['video_image_url'] ?: ($defaults['video_image_url'] ?? ''),
            'cta_text' => $data['cta_text'],
            'cta_url' => $data['cta_url'] ?? ($defaults['cta_url'] ?? '/contact'),
            'review_label' => $data['review_label'],
            'review_images' => $reviewImages,
            'items' => $items,
        ]);

        return back()->with('status', 'Who We Are content updated.');
    }

    public function updateContentTestimonial(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subtitle' => ['required', 'string', 'max:255'],
            'title_before' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'title_after' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'review_score' => ['required', 'string', 'max:50'],
            'review_label' => ['required', 'string', 'max:255'],
            'review_heading' => ['required', 'string', 'max:255'],
            'review_images' => ['nullable', 'array'],
            'review_images.*' => ['nullable', 'string', 'max:2000'],
            'benefits' => ['nullable', 'array'],
            'benefits.*.icon_url' => ['nullable', 'string', 'max:2000'],
            'benefits.*.title' => ['required', 'string', 'max:255'],
            'benefits.*.points' => ['nullable', 'array'],
            'benefits.*.points.*' => ['nullable', 'string', 'max:255'],
        ]);

        $content = $this->contentValue('testimonial', SiteContentDefaults::testimonialContent());
        $defaults = SiteContentDefaults::testimonialContent();
        $defaultBenefits = $defaults['benefits'] ?? [];
        $validatedBenefits = collect($data['benefits'] ?? $defaultBenefits)
            ->values()
            ->map(function (array $benefit, int $index) use ($defaultBenefits): array {
                $defaultBenefit = $defaultBenefits[$index] ?? ['icon_url' => '', 'title' => '', 'points' => ['', '']];
                $points = collect($benefit['points'] ?? ($defaultBenefit['points'] ?? []))
                    ->values()
                    ->pad(2, '')
                    ->take(2)
                    ->map(fn ($point) => (string) ($point ?? ''))
                    ->all();

                return [
                    'icon_url' => $benefit['icon_url'] ?: ($defaultBenefit['icon_url'] ?? ''),
                    'title' => $benefit['title'] ?? ($defaultBenefit['title'] ?? ''),
                    'points' => $points,
                ];
            })
            ->pad(count($defaultBenefits), ['icon_url' => '', 'title' => '', 'points' => ['', '']])
            ->take(max(count($defaultBenefits), count($data['benefits'] ?? [])))
            ->all();

        $this->storeContentValue('testimonial', [
            'subtitle' => $data['subtitle'],
            'title_before' => $data['title_before'],
            'title_highlight' => $data['title_highlight'],
            'title_after' => $data['title_after'] ?? '',
            'description' => $data['description'],
            'review_score' => $data['review_score'],
            'review_label' => $data['review_label'],
            'review_heading' => $data['review_heading'],
            'review_images' => collect($data['review_images'] ?? ($content['review_images'] ?? $defaults['review_images'] ?? []))
                ->values()
                ->pad(5, '')
                ->take(5)
                ->map(fn ($image, int $index) => $image ?: (($content['review_images'][$index] ?? '') ?: ($defaults['review_images'][$index] ?? '')))
                ->all(),
            'benefits' => $validatedBenefits,
            'items' => collect($content['items'] ?? [])->values()->all(),
        ]);

        return back()->with('status', 'Testimonial content updated.');
    }

    public function storeContentTestimonial(Request $request): RedirectResponse
    {
        $content = $this->contentValue('testimonial', SiteContentDefaults::testimonialContent());
        $items = collect($content['items'] ?? []);
        $items->push($this->validatedTestimonialPayload($request));

        $this->storeContentValue('testimonial', [
            'subtitle' => $content['subtitle'] ?? SiteContentDefaults::testimonialContent()['subtitle'],
            'title_before' => $content['title_before'] ?? SiteContentDefaults::testimonialContent()['title_before'],
            'title_highlight' => $content['title_highlight'] ?? SiteContentDefaults::testimonialContent()['title_highlight'],
            'title_after' => $content['title_after'] ?? SiteContentDefaults::testimonialContent()['title_after'],
            'description' => $content['description'] ?? SiteContentDefaults::testimonialContent()['description'],
            'review_score' => $content['review_score'] ?? SiteContentDefaults::testimonialContent()['review_score'],
            'review_label' => $content['review_label'] ?? SiteContentDefaults::testimonialContent()['review_label'],
            'review_heading' => $content['review_heading'] ?? SiteContentDefaults::testimonialContent()['review_heading'],
            'review_images' => $content['review_images'] ?? SiteContentDefaults::testimonialContent()['review_images'],
            'benefits' => $content['benefits'] ?? SiteContentDefaults::testimonialContent()['benefits'],
            'items' => $items->sortBy('sort_order')->values()->all(),
        ]);

        return redirect()->route('admin.content.testimonial')->with('status', 'Testimonial created successfully.');
    }

    public function updateContentTestimonialItem(Request $request, string $id): RedirectResponse
    {
        $content = $this->contentValue('testimonial', SiteContentDefaults::testimonialContent());
        $items = collect($content['items'] ?? []);
        $index = $items->search(fn (array $item) => ($item['id'] ?? '') === $id);

        abort_if($index === false, 404);

        $items[$index] = $this->validatedTestimonialPayload($request, $id);

        $this->storeContentValue('testimonial', [
            'subtitle' => $content['subtitle'] ?? SiteContentDefaults::testimonialContent()['subtitle'],
            'title_before' => $content['title_before'] ?? SiteContentDefaults::testimonialContent()['title_before'],
            'title_highlight' => $content['title_highlight'] ?? SiteContentDefaults::testimonialContent()['title_highlight'],
            'title_after' => $content['title_after'] ?? SiteContentDefaults::testimonialContent()['title_after'],
            'description' => $content['description'] ?? SiteContentDefaults::testimonialContent()['description'],
            'review_score' => $content['review_score'] ?? SiteContentDefaults::testimonialContent()['review_score'],
            'review_label' => $content['review_label'] ?? SiteContentDefaults::testimonialContent()['review_label'],
            'review_heading' => $content['review_heading'] ?? SiteContentDefaults::testimonialContent()['review_heading'],
            'review_images' => $content['review_images'] ?? SiteContentDefaults::testimonialContent()['review_images'],
            'benefits' => $content['benefits'] ?? SiteContentDefaults::testimonialContent()['benefits'],
            'items' => $items->sortBy('sort_order')->values()->all(),
        ]);

        return redirect()->route('admin.content.testimonial')->with('status', 'Testimonial updated successfully.');
    }

    public function deleteContentTestimonialItem(string $id): RedirectResponse
    {
        $content = $this->contentValue('testimonial', SiteContentDefaults::testimonialContent());
        $items = collect($content['items'] ?? [])
            ->reject(fn (array $item) => ($item['id'] ?? '') === $id)
            ->values()
            ->all();

        $this->storeContentValue('testimonial', [
            'subtitle' => $content['subtitle'] ?? SiteContentDefaults::testimonialContent()['subtitle'],
            'title_before' => $content['title_before'] ?? SiteContentDefaults::testimonialContent()['title_before'],
            'title_highlight' => $content['title_highlight'] ?? SiteContentDefaults::testimonialContent()['title_highlight'],
            'title_after' => $content['title_after'] ?? SiteContentDefaults::testimonialContent()['title_after'],
            'description' => $content['description'] ?? SiteContentDefaults::testimonialContent()['description'],
            'review_score' => $content['review_score'] ?? SiteContentDefaults::testimonialContent()['review_score'],
            'review_label' => $content['review_label'] ?? SiteContentDefaults::testimonialContent()['review_label'],
            'review_heading' => $content['review_heading'] ?? SiteContentDefaults::testimonialContent()['review_heading'],
            'review_images' => $content['review_images'] ?? SiteContentDefaults::testimonialContent()['review_images'],
            'benefits' => $content['benefits'] ?? SiteContentDefaults::testimonialContent()['benefits'],
            'items' => $items,
        ]);

        return back()->with('status', 'Testimonial deleted successfully.');
    }

    public function updateContentServices(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section.subtitle' => ['required', 'string', 'max:255'],
            'section.title_before' => ['required', 'string', 'max:255'],
            'section.title_highlight' => ['required', 'string', 'max:255'],
            'section.title_after' => ['required', 'string', 'max:255'],
            'section.description' => ['nullable', 'string'],
            'section.cta_text' => ['nullable', 'string', 'max:100'],
            'section.cta_url' => ['nullable', 'string', 'max:500'],
            'section.footer_text' => ['nullable', 'string', 'max:500'],
            'section.footer_link_text' => ['nullable', 'string', 'max:100'],
            'section.footer_link_url' => ['nullable', 'string', 'max:500'],
        ]);

        $homeDefaults = SiteContentDefaults::homeServicesContent();
        $servicesPageDefaults = SiteContentDefaults::servicesPageContent();
        $existingServicesPage = $this->contentValue('services_page', SiteContentDefaults::servicesPageContent());

        $sharedSection = [
            'subtitle' => $data['section']['subtitle'],
            'title_before' => $data['section']['title_before'],
            'title_highlight' => $data['section']['title_highlight'],
            'title_after' => $data['section']['title_after'],
            'description' => $data['section']['description'] ?? '',
            'cta_text' => $data['section']['cta_text'] ?? ($homeDefaults['cta_text'] ?? 'all services'),
            'cta_url' => $data['section']['cta_url'] ?? ($homeDefaults['cta_url'] ?? '/services'),
            'footer_text' => $data['section']['footer_text'] ?? ($homeDefaults['footer_text'] ?? ''),
            'footer_link_text' => $data['section']['footer_link_text'] ?? ($homeDefaults['footer_link_text'] ?? ''),
            'footer_link_url' => $data['section']['footer_link_url'] ?? ($homeDefaults['footer_link_url'] ?? '/contact'),
        ];

        $this->storeContentValue('home_services', [
            ...$sharedSection,
        ]);

        $this->storeContentValue('services_page', [
            'page_header' => $existingServicesPage['page_header'] ?? $servicesPageDefaults['page_header'],
            'services_section' => [
                ...$sharedSection,
            ],
            'sidebar_cta' => $existingServicesPage['sidebar_cta'] ?? $servicesPageDefaults['sidebar_cta'],
            'items' => collect($existingServicesPage['items'] ?? [])->sortBy('sort_order')->values()->all(),
        ]);

        SiteContent::query()->where('key', 'services')->delete();

        return back()->with('status', 'Services content updated.');
    }

    public function storeContentService(Request $request): RedirectResponse
    {
        $content = $this->contentValue('services_page', SiteContentDefaults::servicesPageContent());
        $items = collect($content['items'] ?? []);
        $items->push($this->validatedServicePayload($request));

        $this->storeContentValue('services_page', [
            'page_header' => $content['page_header'] ?? SiteContentDefaults::servicesPageContent()['page_header'],
            'services_section' => $content['services_section'] ?? SiteContentDefaults::servicesPageContent()['services_section'],
            'sidebar_cta' => $content['sidebar_cta'] ?? SiteContentDefaults::servicesPageContent()['sidebar_cta'],
            'items' => $items->sortBy('sort_order')->values()->all(),
        ]);

        return redirect()->route('admin.content.services')->with('status', 'Service created successfully.');
    }

    public function updateContentService(Request $request, string $slug): RedirectResponse
    {
        $content = $this->contentValue('services_page', SiteContentDefaults::servicesPageContent());
        $items = collect($content['items'] ?? []);
        $index = $items->search(fn (array $item) => ($item['slug'] ?? '') === $slug);

        abort_if($index === false, 404);

        $items[$index] = $this->validatedServicePayload($request);

        $this->storeContentValue('services_page', [
            'page_header' => $content['page_header'] ?? SiteContentDefaults::servicesPageContent()['page_header'],
            'services_section' => $content['services_section'] ?? SiteContentDefaults::servicesPageContent()['services_section'],
            'sidebar_cta' => $content['sidebar_cta'] ?? SiteContentDefaults::servicesPageContent()['sidebar_cta'],
            'items' => $items->sortBy('sort_order')->values()->all(),
        ]);

        return redirect()->route('admin.content.services')->with('status', 'Service updated successfully.');
    }

    public function deleteContentService(string $slug): RedirectResponse
    {
        $content = $this->contentValue('services_page', SiteContentDefaults::servicesPageContent());
        $items = collect($content['items'] ?? [])
            ->reject(fn (array $item) => ($item['slug'] ?? '') === $slug)
            ->values()
            ->all();

        $this->storeContentValue('services_page', [
            'page_header' => $content['page_header'] ?? SiteContentDefaults::servicesPageContent()['page_header'],
            'services_section' => $content['services_section'] ?? SiteContentDefaults::servicesPageContent()['services_section'],
            'sidebar_cta' => $content['sidebar_cta'] ?? SiteContentDefaults::servicesPageContent()['sidebar_cta'],
            'items' => $items,
        ]);

        return back()->with('status', 'Service deleted successfully.');
    }

    public function updateContentTeam(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section.subtitle' => ['required', 'string', 'max:255'],
            'section.title' => ['required', 'string', 'max:255'],
            'section.description' => ['nullable', 'string'],
            'section.cta_text' => ['nullable', 'string', 'max:100'],
            'section.cta_url' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array'],
        ]);

        $defaults = SiteContentDefaults::teamContent();
        $defaultSection = $defaults['section'] ?? [];

        $section = [
            'subtitle' => $data['section']['subtitle'],
            'title' => $data['section']['title'],
            'description' => $data['section']['description'] ?? '',
            'cta_text' => $data['section']['cta_text'] ?? ($defaultSection['cta_text'] ?? 'All Member'),
            'cta_url' => $data['section']['cta_url'] ?? ($defaultSection['cta_url'] ?? '/team'),
        ];

        $this->storeContentValue('team', [
            'section' => $section,
            'items' => collect($data['items'])->values()->all(),
        ]);

        return back()->with('status', 'Team content updated.');
    }

    public function storeContentTeam(Request $request): RedirectResponse
    {
        $content = $this->contentValue('team', SiteContentDefaults::teamContent());
        $items = collect($content['items'] ?? []);
        $payload = $this->validatedTeamPayload($request);

        $items->push($payload);

        $this->storeContentValue('team', [
            'section' => $content['section'] ?? SiteContentDefaults::teamContent()['section'],
            'items' => $items->sortBy('sort_order')->values()->all(),
        ]);

        return redirect()->route('admin.content.team')->with('status', 'Team member created successfully.');
    }

    public function updateContentTeamMember(Request $request, string $slug): RedirectResponse
    {
        $content = $this->contentValue('team', SiteContentDefaults::teamContent());
        $items = collect($content['items'] ?? []);
        $index = $items->search(fn (array $item) => ($item['slug'] ?? '') === $slug);

        abort_if($index === false, 404);

        $items[$index] = $this->validatedTeamPayload($request);

        $this->storeContentValue('team', [
            'section' => $content['section'] ?? SiteContentDefaults::teamContent()['section'],
            'items' => $items->sortBy('sort_order')->values()->all(),
        ]);

        return redirect()->route('admin.content.team')->with('status', 'Team member updated successfully.');
    }

    public function deleteContentTeamMember(string $slug): RedirectResponse
    {
        $content = $this->contentValue('team', SiteContentDefaults::teamContent());
        $items = collect($content['items'] ?? [])
            ->reject(fn (array $item) => ($item['slug'] ?? '') === $slug)
            ->values()
            ->all();

        $this->storeContentValue('team', [
            'section' => $content['section'] ?? SiteContentDefaults::teamContent()['section'],
            'items' => $items,
        ]);

        return back()->with('status', 'Team member deleted successfully.');
    }

    public function updateContentProjects(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section.home_title' => ['required', 'string', 'max:255'],
            'section.home_heading_before' => ['nullable', 'string', 'max:255'],
            'section.home_heading_highlight' => ['nullable', 'string', 'max:255'],
            'section.home_heading_after' => ['nullable', 'string', 'max:255'],
            'section.home_description' => ['nullable', 'string'],
            'items' => ['required', 'array'],
        ]);

        $this->storeContentValue('projects', [
            'section' => [
                'home_title' => $data['section']['home_title'],
                'home_heading_before' => $data['section']['home_heading_before'] ?? (SiteContentDefaults::projectsContent()['section']['home_heading_before'] ?? ''),
                'home_heading_highlight' => $data['section']['home_heading_highlight'] ?? (SiteContentDefaults::projectsContent()['section']['home_heading_highlight'] ?? ''),
                'home_heading_after' => $data['section']['home_heading_after'] ?? (SiteContentDefaults::projectsContent()['section']['home_heading_after'] ?? ''),
                'home_description' => $data['section']['home_description'] ?? (SiteContentDefaults::projectsContent()['section']['home_description'] ?? ''),
            ],
            'items' => collect($data['items'])->values()->all(),
        ]);

        return back()->with('status', 'Projects content updated.');
    }

    public function storeContentProject(Request $request): RedirectResponse
    {
        $content = $this->contentValue('projects', SiteContentDefaults::projectsContent());
        $items = collect($content['items'] ?? []);
        $items->push($this->validatedProjectPayload($request));

        $this->storeContentValue('projects', [
            'section' => $content['section'] ?? SiteContentDefaults::projectsContent()['section'],
            'items' => $items->sortBy('sort_order')->values()->all(),
        ]);

        return redirect()->route('admin.content.projects')->with('status', 'Project created successfully.');
    }

    public function updateContentProject(Request $request, string $slug): RedirectResponse
    {
        $content = $this->contentValue('projects', SiteContentDefaults::projectsContent());
        $items = collect($content['items'] ?? []);
        $index = $items->search(fn (array $item) => ($item['slug'] ?? '') === $slug);

        abort_if($index === false, 404);

        $items[$index] = $this->validatedProjectPayload($request);

        $this->storeContentValue('projects', [
            'section' => $content['section'] ?? SiteContentDefaults::projectsContent()['section'],
            'items' => $items->sortBy('sort_order')->values()->all(),
        ]);

        return redirect()->route('admin.content.projects')->with('status', 'Project updated successfully.');
    }

    public function deleteContentProject(string $slug): RedirectResponse
    {
        $content = $this->contentValue('projects', SiteContentDefaults::projectsContent());
        $items = collect($content['items'] ?? [])
            ->reject(fn (array $item) => ($item['slug'] ?? '') === $slug)
            ->values()
            ->all();

        $this->storeContentValue('projects', [
            'section' => $content['section'] ?? SiteContentDefaults::projectsContent()['section'],
            'items' => $items,
        ]);

        return back()->with('status', 'Project deleted successfully.');
    }

    public function updateContentSocialLinks(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.name' => ['required', 'string', 'max:100'],
            'items.*.url' => ['required', 'string', 'max:1000'],
            'items.*.icon' => ['required', 'string', 'max:100'],
            'items.*.status' => ['nullable', 'string', 'max:20'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $items = collect($data['items'])->map(function (array $item, int $index): array {
            return [
                'name' => $item['name'],
                'url' => $item['url'],
                'icon' => $item['icon'],
                'status' => $item['status'] ?? 'Active',
                'sort_order' => $item['sort_order'] ?? ($index + 1),
            ];
        })->sortBy('sort_order')->values()->all();

        $this->storeContentValue('social_links', $items);

        return back()->with('status', 'Social links updated.');
    }

    public function updateContentContactInfo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'map_url' => ['nullable', 'string', 'max:2000'],
            'map_embed_url' => ['nullable', 'string', 'max:5000'],
            'welcome_subject' => ['nullable', 'string', 'max:255'],
            'welcome_message' => ['nullable', 'string', 'max:5000'],
        ]);

        $existing = $this->contentValue('contact_info', SiteContentDefaults::contactInfo());
        $value = array_merge($existing, [
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'map_url' => $data['map_url'] ?? '',
            'map_embed_url' => $data['map_embed_url'] ?? ($existing['map_embed_url'] ?? ''),
            'welcome_subject' => $data['welcome_subject'] ?? ($existing['welcome_subject'] ?? 'Welcome to PEACEMAIN'),
            'welcome_message' => $data['welcome_message'] ?? ($existing['welcome_message'] ?? 'Thanks for contacting us. Our team will get back to you shortly.'),
        ]);

        $this->storeContentValue('contact_info', $value);

        return back()->with('status', 'Contact info updated.');
    }

    public function settings(): Response
    {
        return Inertia::render('Admin/Settings', [
            'settings' => $this->contentValue('general_settings', SiteContentDefaults::generalSettings()),
            'smtp' => $this->contentValue('smtp_settings', SiteContentDefaults::smtpSettings()),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'timezone' => ['required', 'string', 'max:100'],
            'default_country' => ['nullable', 'string', 'max:100'],
            'support_email' => ['required', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'in:none,ssl,tls'],
            'from_email' => ['nullable', 'email', 'max:255'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'mail_template_html' => ['nullable', 'string'],
        ]);

        $existingGeneral = $this->contentValue('general_settings', SiteContentDefaults::generalSettings());
        $logoUrl = $existingGeneral['logo_url'] ?? '';

        if ($request->hasFile('logo')) {
            if ($logoUrl && str_contains($logoUrl, '/storage/')) {
                $logoPath = parse_url($logoUrl, PHP_URL_PATH) ?: $logoUrl;
                $oldPath = ltrim(str_replace('/storage/', '', $logoPath), '/');
                if ($oldPath !== '' && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('logo')->store('settings', 'public');
            $logoUrl = '/storage/' . ltrim($path, '/');
        }

        $this->storeContentValue('general_settings', [
            'site_name' => $data['site_name'],
            'timezone' => $data['timezone'],
            'default_country' => $data['default_country'] ?? '',
            'support_email' => $data['support_email'],
            'logo_url' => $logoUrl,
        ]);

        $this->storeContentValue('smtp_settings', [
            'host' => $data['smtp_host'] ?? '',
            'port' => (int) ($data['smtp_port'] ?? 587),
            'username' => $data['smtp_username'] ?? '',
            'password' => $data['smtp_password'] ?? '',
            'encryption' => $data['smtp_encryption'] ?? 'tls',
            'from_email' => $data['from_email'] ?? '',
            'from_name' => $data['from_name'] ?? '',
            'mail_template_html' => $data['mail_template_html'] ?? '',
        ]);

        return back()->with('status', 'Settings updated.');
    }

    private function serviceValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'card_icon_url' => ['nullable', 'string', 'max:2000'],
            'short_description' => ['nullable', 'string'],
            'detail_feature_image_url' => ['nullable', 'string', 'max:2000'],
            'intro_paragraph_1' => ['nullable', 'string'],
            'intro_paragraph_2' => ['nullable', 'string'],
            'key_features_title_before' => ['required', 'string', 'max:255'],
            'key_features_title_highlight' => ['required', 'string', 'max:255'],
            'key_features_title_after' => ['required', 'string', 'max:255'],
            'key_features_paragraph_1' => ['nullable', 'string'],
            'key_features_paragraph_2' => ['nullable', 'string'],
            'features_list' => ['required', 'array', 'size:6'],
            'features_list.*' => ['nullable', 'string', 'max:255'],
            'feature_side_image_url' => ['nullable', 'string', 'max:2000'],
            'process_title_before' => ['required', 'string', 'max:255'],
            'process_title_highlight' => ['required', 'string', 'max:255'],
            'process_title_after' => ['required', 'string', 'max:255'],
            'process_description' => ['nullable', 'string'],
            'process_steps' => ['required', 'array', 'size:3'],
            'process_steps.*.icon_url' => ['nullable', 'string', 'max:2000'],
            'process_steps.*.image_url' => ['nullable', 'string', 'max:2000'],
            'process_steps.*.title' => ['required', 'string', 'max:255'],
            'process_steps.*.description' => ['nullable', 'string'],
            'show_on_home' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function validatedServicePayload(Request $request): array
    {
        $data = $request->validate($this->serviceValidationRules());
        $defaultItems = collect(SiteContentDefaults::servicesPageContent()['items'] ?? []);
        $defaultItem = $defaultItems->first(fn (array $item) => ($item['slug'] ?? '') === ($data['slug'] ?? ''))
            ?? $defaultItems->first(fn (array $item) => ($item['name'] ?? '') === $data['name'])
            ?? [];

        $processSteps = collect($data['process_steps'] ?? [])->map(function (array $step, int $index) use ($defaultItem): array {
            $defaultStep = collect($defaultItem['process_steps'] ?? [])->get($index, []);

            return [
                'icon_url' => $step['icon_url'] ?: ($defaultStep['icon_url'] ?? ''),
                'image_url' => $step['image_url'] ?: ($defaultStep['image_url'] ?? ''),
                'title' => $step['title'] ?? ($defaultStep['title'] ?? ''),
                'description' => $step['description'] ?? ($defaultStep['description'] ?? ''),
            ];
        })->values()->all();

        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['slug'] ?: $data['name']),
            'card_icon_url' => $data['card_icon_url'] ?: ($defaultItem['card_icon_url'] ?? ''),
            'short_description' => $data['short_description'] ?? '',
            'detail_feature_image_url' => $data['detail_feature_image_url'] ?: ($defaultItem['detail_feature_image_url'] ?? ''),
            'intro_paragraph_1' => $data['intro_paragraph_1'] ?? '',
            'intro_paragraph_2' => $data['intro_paragraph_2'] ?? '',
            'key_features_title_before' => $data['key_features_title_before'],
            'key_features_title_highlight' => $data['key_features_title_highlight'],
            'key_features_title_after' => $data['key_features_title_after'],
            'key_features_paragraph_1' => $data['key_features_paragraph_1'] ?? '',
            'key_features_paragraph_2' => $data['key_features_paragraph_2'] ?? '',
            'features_list' => collect($data['features_list'] ?? [])
                ->map(fn ($item, $index) => $item ?: (collect($defaultItem['features_list'] ?? [])->get($index, '')))
                ->values()
                ->all(),
            'feature_side_image_url' => $data['feature_side_image_url'] ?: ($defaultItem['feature_side_image_url'] ?? ''),
            'process_title_before' => $data['process_title_before'],
            'process_title_highlight' => $data['process_title_highlight'],
            'process_title_after' => $data['process_title_after'],
            'process_description' => $data['process_description'] ?? '',
            'process_steps' => $processSteps,
            'show_on_home' => (bool) ($data['show_on_home'] ?? false),
            'status' => $data['status'] ?? 'Active',
            'sort_order' => $data['sort_order'] ?? 1,
        ];
    }

    private function emptyServicePayload(): array
    {
        return [
            'name' => '',
            'slug' => '',
            'card_icon_url' => '',
            'short_description' => '',
            'detail_feature_image_url' => '',
            'intro_paragraph_1' => '',
            'intro_paragraph_2' => '',
            'key_features_title_before' => 'Key',
            'key_features_title_highlight' => 'feature',
            'key_features_title_after' => 'of digital marketing',
            'key_features_paragraph_1' => '',
            'key_features_paragraph_2' => '',
            'features_list' => ['', '', '', '', '', ''],
            'feature_side_image_url' => '',
            'process_title_before' => 'Our',
            'process_title_highlight' => 'process',
            'process_title_after' => 'of digital marketing',
            'process_description' => '',
            'process_steps' => [
                ['icon_url' => '', 'image_url' => '', 'title' => '', 'description' => ''],
                ['icon_url' => '', 'image_url' => '', 'title' => '', 'description' => ''],
                ['icon_url' => '', 'image_url' => '', 'title' => '', 'description' => ''],
            ],
            'show_on_home' => false,
            'status' => 'Active',
            'sort_order' => 1,
        ];
    }

    private function teamValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'short_bio' => ['nullable', 'string'],
            'background_image_url' => ['nullable', 'string', 'max:2000'],
            'card_image_url' => ['nullable', 'string', 'max:2000'],
            'detail_image_url' => ['nullable', 'string', 'max:2000'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:500'],
            'experience_title' => ['nullable', 'string', 'max:255'],
            'experience_text' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*.label' => ['nullable', 'string', 'max:255'],
            'skills.*.percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.icon' => ['nullable', 'string', 'max:100'],
            'social_links.*.url' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'string', 'max:20'],
            'show_on_home' => ['nullable', 'boolean'],
            'show_on_team_page' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function validatedTeamPayload(Request $request): array
    {
        $data = $request->validate($this->teamValidationRules());
        $defaultItems = collect(SiteContentDefaults::teamContent()['items'] ?? []);
        $defaultItem = $defaultItems->first(fn (array $item) => ($item['slug'] ?? '') === ($data['slug'] ?? ''))
            ?? $defaultItems->first(fn (array $item) => ($item['name'] ?? '') === $data['name'])
            ?? [];

        $skills = collect($data['skills'] ?? [])->map(function (array $skill, int $index) use ($defaultItem): array {
            $defaultSkill = collect($defaultItem['skills'] ?? [])->get($index, []);

            return [
                'label' => $skill['label'] ?? ($defaultSkill['label'] ?? ''),
                'percent' => (int) ($skill['percent'] ?? ($defaultSkill['percent'] ?? 0)),
            ];
        })->filter(fn (array $skill) => $skill['label'] !== '')->values()->all();

        $socialLinks = collect($data['social_links'] ?? [])->map(function (array $link, int $index) use ($defaultItem): array {
            $defaultLink = collect($defaultItem['social_links'] ?? [])->get($index, []);

            return [
                'icon' => $link['icon'] ?? ($defaultLink['icon'] ?? ''),
                'url' => $link['url'] ?? ($defaultLink['url'] ?? '#'),
            ];
        })->filter(fn (array $link) => $link['icon'] !== '')->values()->all();

        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['slug'] ?: $data['name']),
            'designation' => $data['designation'],
            'short_bio' => $data['short_bio'] ?? '',
            'background_image_url' => $data['background_image_url'] ?: ($defaultItem['background_image_url'] ?? ''),
            'card_image_url' => $data['card_image_url'] ?: ($defaultItem['card_image_url'] ?? ''),
            'detail_image_url' => $data['detail_image_url'] ?: ($defaultItem['detail_image_url'] ?? ''),
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'website' => $data['website'] ?? '',
            'experience_title' => $data['experience_title'] ?? 'Personal Experience',
            'experience_text' => $data['experience_text'] ?? '',
            'skills' => $skills,
            'social_links' => $socialLinks,
            'status' => $data['status'] ?? 'Active',
            'show_on_home' => (bool) ($data['show_on_home'] ?? true),
            'show_on_team_page' => (bool) ($data['show_on_team_page'] ?? true),
            'sort_order' => $data['sort_order'] ?? 1,
        ];
    }

    private function emptyTeamPayload(): array
    {
        return [
            'name' => '',
            'slug' => '',
            'designation' => '',
            'short_bio' => '',
            'background_image_url' => '',
            'card_image_url' => '',
            'detail_image_url' => '',
            'email' => '',
            'phone' => '',
            'website' => '',
            'experience_title' => 'Personal Experience',
            'experience_text' => '',
            'skills' => [
                ['label' => 'Tecnology', 'percent' => 90],
                ['label' => 'Marketing', 'percent' => 80],
                ['label' => 'Business', 'percent' => 75],
            ],
            'social_links' => [
                ['icon' => 'fab fa-facebook-f', 'url' => '#'],
                ['icon' => 'fab fa-pinterest-p', 'url' => '#'],
                ['icon' => 'fab fa-instagram', 'url' => '#'],
                ['icon' => 'fab fa-twitter', 'url' => '#'],
            ],
            'status' => 'Active',
            'show_on_home' => true,
            'show_on_team_page' => true,
            'sort_order' => 1,
        ];
    }

    private function projectValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'project_date' => ['nullable', 'string', 'max:255'],
            'home_image_url' => ['nullable', 'string', 'max:2000'],
            'list_image_url' => ['nullable', 'string', 'max:2000'],
            'detail_gallery_images' => ['nullable', 'array'],
            'detail_gallery_images.*' => ['nullable', 'string', 'max:2000'],
            'about_title' => ['nullable', 'string', 'max:255'],
            'about_heading' => ['nullable', 'string', 'max:255'],
            'about_text' => ['nullable', 'string'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'string', 'max:2000'],
            'facts_title' => ['nullable', 'string', 'max:255'],
            'facts_text' => ['nullable', 'string'],
            'facts_list' => ['nullable', 'array'],
            'facts_list.*' => ['nullable', 'string', 'max:500'],
            'results_title' => ['nullable', 'string', 'max:255'],
            'results_text' => ['nullable', 'string'],
            'results_items' => ['nullable', 'array'],
            'results_items.*.title' => ['nullable', 'string', 'max:255'],
            'results_items.*.description' => ['nullable', 'string'],
            'show_on_home' => ['nullable', 'boolean'],
            'show_on_projects_page' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function validatedProjectPayload(Request $request): array
    {
        $data = $request->validate($this->projectValidationRules());
        $defaultItems = collect(SiteContentDefaults::projectsContent()['items'] ?? []);
        $defaultItem = $defaultItems->first(fn (array $item) => ($item['slug'] ?? '') === ($data['slug'] ?? ''))
            ?? $defaultItems->first(fn (array $item) => ($item['name'] ?? '') === $data['name'])
            ?? [];

        $gallery = collect($data['detail_gallery_images'] ?? [])
            ->map(fn ($image, $index) => $image ?: (collect($defaultItem['detail_gallery_images'] ?? [])->get($index, '')))
            ->filter()
            ->values()
            ->all();

        $facts = collect($data['facts_list'] ?? [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();

        $results = collect($data['results_items'] ?? [])
            ->map(fn (array $item) => [
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
            ])
            ->filter(fn (array $item) => $item['title'] || $item['description'])
            ->values()
            ->all();

        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['slug'] ?: $data['name']),
            'category' => $data['category'] ?? 'DEVELOPMENT',
            'short_description' => $data['short_description'] ?? '',
            'project_date' => $data['project_date'] ?? '',
            'home_image_url' => $data['home_image_url'] ?: ($defaultItem['home_image_url'] ?? ''),
            'list_image_url' => $data['list_image_url'] ?: ($defaultItem['list_image_url'] ?? ''),
            'detail_gallery_images' => $gallery,
            'about_title' => $data['about_title'] ?? 'About The Project',
            'about_heading' => $data['about_heading'] ?? '',
            'about_text' => $data['about_text'] ?? '',
            'client_name' => $data['client_name'] ?? '',
            'project_type' => $data['project_type'] ?? '',
            'website_url' => $data['website_url'] ?? '',
            'facts_title' => $data['facts_title'] ?? 'Interesting facts in|Development',
            'facts_text' => $data['facts_text'] ?? '',
            'facts_list' => $facts,
            'results_title' => $data['results_title'] ?? 'The Results of|Our Project',
            'results_text' => $data['results_text'] ?? '',
            'results_items' => $results,
            'show_on_home' => (bool) ($data['show_on_home'] ?? true),
            'show_on_projects_page' => (bool) ($data['show_on_projects_page'] ?? true),
            'status' => $data['status'] ?? 'Active',
            'sort_order' => $data['sort_order'] ?? 1,
        ];
    }

    private function emptyProjectPayload(): array
    {
        return [
            'name' => '',
            'slug' => '',
            'category' => 'DEVELOPMENT',
            'short_description' => '',
            'project_date' => '',
            'home_image_url' => '',
            'list_image_url' => '',
            'detail_gallery_images' => ['', '', '', ''],
            'about_title' => 'About The Project',
            'about_heading' => '',
            'about_text' => '',
            'client_name' => '',
            'project_type' => '',
            'website_url' => '',
            'facts_title' => 'Interesting facts in|Development',
            'facts_text' => '',
            'facts_list' => ['', '', '', ''],
            'results_title' => 'The Results of|Our Project',
            'results_text' => '',
            'results_items' => [
                ['title' => '', 'description' => ''],
                ['title' => '', 'description' => ''],
            ],
            'show_on_home' => true,
            'show_on_projects_page' => true,
            'status' => 'Active',
            'sort_order' => 1,
        ];
    }

    private function testimonialValidationRules(): array
    {
        return [
            'id' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'quote' => ['required', 'string'],
            'author_image_url' => ['nullable', 'string', 'max:2000'],
            'author_name' => ['required', 'string', 'max:255'],
            'author_role' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function validatedTestimonialPayload(Request $request, ?string $existingId = null): array
    {
        $data = $request->validate($this->testimonialValidationRules());
        $defaultItems = collect(SiteContentDefaults::testimonialContent()['items'] ?? []);
        $defaultItem = $defaultItems->first(fn (array $item) => ($item['id'] ?? '') === ($existingId ?? ($data['id'] ?? '')))
            ?? $defaultItems->first()
            ?? [];

        return [
            'id' => $existingId ?? $data['id'] ?? (string) Str::uuid(),
            'rating' => $data['rating'] ?? ($defaultItem['rating'] ?? 5),
            'quote' => $data['quote'] ?: ($defaultItem['quote'] ?? ($defaultItem['description'] ?? '')),
            'author_image_url' => $data['author_image_url'] ?: ($defaultItem['author_image_url'] ?? ($defaultItem['image_url'] ?? '')),
            'author_name' => $data['author_name'],
            'author_role' => $data['author_role'] ?? '',
            'status' => $data['status'] ?? 'Active',
            'sort_order' => $data['sort_order'] ?? 1,
        ];
    }

    private function emptyTestimonialPayload(): array
    {
        return [
            'id' => '',
            'rating' => 5,
            'quote' => '',
            'author_image_url' => '',
            'author_name' => '',
            'author_role' => '',
            'status' => 'Active',
            'sort_order' => 1,
        ];
    }

    private function contentValue(string $key, mixed $default): mixed
    {
        $record = SiteContent::query()->where('key', $key)->first();

        return $record?->value ?? $default;
    }

    private function storeContentValue(string $key, mixed $value): void
    {
        SiteContent::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    private function markAsRead(ContactSubmission $contact): void
    {
        if (! $contact->is_read) {
            $contact->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    private function sendReplyMail(ContactSubmission $contact, string $subject, string $body): void
    {
        $smtp = $this->contentValue('smtp_settings', SiteContentDefaults::smtpSettings());

        if (empty($smtp['host']) || empty($smtp['port']) || empty($smtp['from_email'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'subject' => 'SMTP settings are incomplete. Please configure mail settings first.',
            ]);
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $smtp['host'],
            'mail.mailers.smtp.port' => (int) $smtp['port'],
            'mail.mailers.smtp.username' => $smtp['username'] ?? '',
            'mail.mailers.smtp.password' => $smtp['password'] ?? '',
            'mail.mailers.smtp.encryption' => ($smtp['encryption'] ?? 'tls') === 'none' ? null : ($smtp['encryption'] ?? 'tls'),
            'mail.from.address' => $smtp['from_email'],
            'mail.from.name' => $smtp['from_name'] ?: 'PEACEMAIN',
        ]);

        $html = '<div style="font-family:Arial,sans-serif;line-height:1.6">' . nl2br(e($body)) . '</div>';

        try {
            Mail::html($html, function ($message) use ($contact, $subject): void {
                $message->to($contact->email)->subject($subject);
            });
        } catch (\Throwable $exception) {
            Log::error('Admin reply mail send failed', [
                'contact_submission_id' => $contact->id,
                'error' => $exception->getMessage(),
            ]);

            throw \Illuminate\Validation\ValidationException::withMessages([
                'subject' => 'Failed to send email. Check SMTP settings and try again.',
            ]);
        }
    }
}
