<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use App\Support\SiteContentDefaults;
use Illuminate\Http\JsonResponse;

class SiteContentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->content(),
        ]);
    }

    public function site(): JsonResponse
    {
        return $this->index();
    }

    private function content(): array
    {
        $defaults = $this->defaults();
        $records = SiteContent::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->all();

        $content = [];

        foreach ($defaults as $key => $default) {
            $content[$key] = $records[$key] ?? $default;
        }

        return $content;
    }

    private function defaults(): array
    {
        return [
            'banner' => SiteContentDefaults::banners(),
            'sidebar' => SiteContentDefaults::sidebarLinks(),
            'faq' => SiteContentDefaults::faqs(),
            'social_links' => SiteContentDefaults::socialLinks(),
            'contact_info' => SiteContentDefaults::contactInfo(),
            'general_settings' => SiteContentDefaults::generalSettings(),
            'why_choose_us' => SiteContentDefaults::whyChooseUsContent(),
            'mission' => SiteContentDefaults::missionContent(),
            'working_process' => SiteContentDefaults::workingProcessContent(),
            'who_we_are' => SiteContentDefaults::whoWeAreContent(),
            'home_features' => SiteContentDefaults::homeFeaturesContent(),
            'about' => SiteContentDefaults::aboutContent(),
            'testimonial' => SiteContentDefaults::testimonialContent(),
            'home_services' => SiteContentDefaults::homeServicesContent(),
            'services_page' => SiteContentDefaults::servicesPageContent(),
            'team' => SiteContentDefaults::teamContent(),
            'projects' => SiteContentDefaults::projectsContent(),
        ];
    }
}
