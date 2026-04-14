<?php

namespace App\Support;

class SiteContentDefaults
{
    public static function banners(): array
    {
        return [
            [
                'heading_prefix' => 'Innovative solutions for',
                'typed_titles' => [
                    'Digital World',
                    'Social Marketing',
                    'Art & Design',
                ],
                'description' => 'At our Creative Digital Agency, we bring your ideas to life by crafting engaging, impactful digital experiences that captivate audiences and drive results. From innovative web design to compelling content and cutting-edge digital strategies.',
                'button_text' => 'get in touch',
                'button_url' => '/contact',
                'background_video_url' => 'https://demo.awaikenthemes.com/assets/videos/artistic-video.mp4',
                'popup_video_url' => 'https://www.youtube.com/watch?v=Y-x0efG1seA',
                'circle_image_url' => 'https://html.awaikenthemes.com/artistic/images/learn-more-circle.svg',
                'position' => 'Home Top',
                'status' => 'Active',
                'sort_order' => 1,
            ]
        ];
    }

    public static function sidebarLinks(): array
    {
        return [
            ['label' => 'Home', 'url' => '/', 'status' => 'Active', 'sort_order' => 1],
            ['label' => 'Services', 'url' => '/servics', 'status' => 'Active', 'sort_order' => 2],
            ['label' => 'About Us', 'url' => '/about', 'status' => 'Active', 'sort_order' => 3],
            ['label' => 'Contact Us', 'url' => '/contact', 'status' => 'Active', 'sort_order' => 4],
            ['label' => 'FAQs', 'url' => '/faq', 'status' => 'Active', 'sort_order' => 5],
        ];
    }

    public static function faqs(): array
    {
        $item = [
            'question' => 'What is health and care consulting?',
            'answer' => 'Health and care consulting is a specialized service that provides advisory and support to healthcare organizations and stakeholders to improve operations, patient care.',
            'category' => 'General',
            'status' => 'Published',
        ];

        return [
            $item,
            $item,
            $item,
            $item,
            $item,
            $item,
        ];
    }

    public static function socialLinks(): array
    {
        return [
            [
                'name' => 'Facebook',
                'url' => 'https://facebook.com',
                'icon' => 'fab fa-facebook-f',
                'status' => 'Active',
                'sort_order' => 1,
            ],
            [
                'name' => 'Twitter',
                'url' => 'https://twitter.com',
                'icon' => 'fab fa-twitter',
                'status' => 'Active',
                'sort_order' => 2,
            ],
            [
                'name' => 'Instagram',
                'url' => 'https://instagram.com',
                'icon' => 'fab fa-instagram',
                'status' => 'Active',
                'sort_order' => 3,
            ],
            [
                'name' => 'LinkedIn',
                'url' => 'https://linkedin.com',
                'icon' => 'fab fa-linkedin-in',
                'status' => 'Active',
                'sort_order' => 4,
            ],
        ];
    }

    public static function contactInfo(): array
    {
        return [
            'phone' => '+91-8123781857',
            'email' => 'info@peacemain.com',
            'address' => 'PEACEMAIN Ltd.,Celestia Kyriakou Matsi 5, Limassol 4529, Cyprus',
            'map_url' => 'https://maps.app.goo.gl/sDLkkAWRX1Ho9hUD9',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d10584.26445645795!2d77.58083569448927!3d12.906775654837578!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae1513448609bf%3A0x22d8538479cabefa!2s250%2C%2024th%20Main%20Rd%2C%20KR%20Layout%2C%20JP%20Nagar%20Phase%206%2C%20J.%20P.%20Nagar%2C%20Bengaluru%2C%20Karnataka%20560078%2C%20India!5e0!3m2!1sen!2sbd!4v1731841896879!5m2!1sen!2sbd',
            'welcome_subject' => 'Welcome to PEACEMAIN',
            'welcome_message' => 'Thanks for contacting us. Our team will get back to you shortly.',
            'locations' => [
                [
                    'name' => 'Limassol, Cyprus',
                    'map_url' => 'https://maps.app.goo.gl/sDLkkAWRX1Ho9hUD9',
                    'phone' => '+91-8123781857',
                    'email' => 'info@peacemain.com',
                    'address' => 'PEACEMAIN Ltd.,Celestia Kyriakou Matsi 5, Limassol 4529, Cyprus',
                ],
                [
                    'name' => 'Bangalore, India',
                    'map_url' => 'https://maps.app.goo.gl/nPuSUvb5bbtuYK8W6',
                    'phone' => '+91-8123781857',
                    'email' => 'info@peacemain.com',
                    'address' => 'PEACEMAIN Consulting Pvt. Ltd., 249, 24th Main Rd, KR Layout, JP Nagar Phase 6, J. P. Nagar, Bengaluru, Karnataka 560078, India',
                ]
            ]
        ];
    }

    public static function smtpSettings(): array
    {
        return [
            'host' => '',
            'port' => 587,
            'username' => '',
            'password' => '',
            'encryption' => 'tls',
            'from_email' => 'info@peacemain.com',
            'from_name' => 'PEACEMAIN',
            'mail_template_html' => '<div style="font-family:Arial,sans-serif;padding:24px"><h2>Welcome {{name}}</h2><p>{{welcome_message}}</p><p>Regards,<br>{{from_name}}</p></div>',
        ];
    }

    public static function generalSettings(): array
    {
        return [
            'site_name' => 'PeaceMain',
            'timezone' => 'America/Los_Angeles',
            'default_country' => 'India',
            'support_email' => 'support@peacemain.com',
            'logo_url' => '',
        ];
    }

    public static function homeServicesContent(): array
    {
        return [
            'subtitle' => 'Our services',
            'title_before' => 'Our',
            'title_highlight' => 'digital services',
            'title_after' => 'to grow your brand',
            'description' => 'Our digital services empower brands with innovative strategies and solutions for sustainable growth and engagement.',
            'cta_text' => 'all services',
            'cta_url' => '/services',
            'footer_text' => "Let's make something great work together.",
            'footer_link_text' => 'get free quote',
            'footer_link_url' => '/contact',
        ];
    }

    public static function servicesPageContent(): array
    {
        return [
            'page_header' => [
                'title_before' => 'Our',
                'title_highlight' => 'Services',
                'breadcrumb_home_label' => 'home',
                'breadcrumb_home_url' => '/',
                'breadcrumb_current_label' => 'services',
            ],
            'services_section' => [
                'subtitle' => 'Our services',
                'title_before' => 'Our',
                'title_highlight' => 'digital services',
                'title_after' => 'to grow your brand',
                'description' => 'Our digital services empower brands with innovative strategies and solutions for sustainable growth and engagement.',
                'cta_text' => 'all services',
                'cta_url' => '/services',
                'footer_text' => "Let's make something great work together.",
                'footer_link_text' => 'get free quote',
                'footer_link_url' => '/contact',
            ],
            'sidebar_cta' => [
                'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-sidebar-cta.svg',
                'title' => 'You have different questions?',
                'description' => 'Our team will answer all your questions. we ensure a quick response.',
                'phone_text' => '(30) 8855-314',
                'phone_url' => 'tel:308855314',
                'phone_icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-sidebar-cta-phone.svg',
            ],
            'items' => [
                [
                    'name' => 'branding and identity',
                    'slug' => 'branding-and-identity',
                    'card_icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-1.svg',
                    'short_description' => 'Developing a compelling brand identity through strategy, visuals, and  to build strong customer connections.',
                    'detail_feature_image_url' => 'https://html.awaikenthemes.com/artistic/images/service-single-img.jpg',
                    'intro_paragraph_1' => 'Our digital marketing services are designed to elevate your brand and reach your target audience effectively. We develop customized strategies that include SEO, PPC advertising, social media marketing, content marketing, and email campaigns. By leveraging data-driven insights and the latest trends, we maximize engagement, drive traffic, and boost conversions, ensuring a measurable return on your investment. Let us help you grow your digital presence and achieve your business goals.',
                    'intro_paragraph_2' => 'With a focus on data-driven insights, we build customized strategies that drive traffic, boost engagement, and maximize ROI. Let us help you reach your business goals with impactful digital marketing',
                    'key_features_title_before' => 'Key',
                    'key_features_title_highlight' => 'feature',
                    'key_features_title_after' => 'of digital marketing',
                    'key_features_paragraph_1' => 'Our digital marketing approach is a data-driven strategy that combines targeted outreach with creative content. We focus on reaching the right audience through precision techniques like SEO, PPC, and social media advertising, supported by engaging content marketing and email campaigns.',
                    'key_features_paragraph_2' => 'We utilize audience segmentation to divide your customer base into distinct groups based on demographics, interests, and behaviors. This allows us to deliver personalized content and messaging, ensuring relevance and higher engagement rates.',
                    'features_list' => [
                        'content marketing that tells your brand\'s story',
                        'personalized Email Marketing to Nurture Leads',
                        'social media marketing that engages and converts',
                        'comprehensive SEO services for visibility',
                        'performance analytics & reporting',
                        'performance analytics & reporting',
                    ],
                    'feature_side_image_url' => 'https://html.awaikenthemes.com/artistic/images/service-entry-img.jpg',
                    'process_title_before' => 'Our',
                    'process_title_highlight' => 'process',
                    'process_title_after' => 'of digital marketing',
                    'process_description' => 'Our digital marketing process begins with discovery and research to understand your goals. We then develop a tailored strategy and implement campaigns across various channels. Continuous monitoring and optimization ensure effectiveness, followed by regular reporting to track performance. Finally, we refine and scale efforts for sustained growth and success.',
                    'process_steps' => [
                        [
                            'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-1.svg',
                            'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-1.jpg',
                            'title' => 'discovery phase',
                            'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                        ],
                        [
                            'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-2.svg',
                            'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-2.jpg',
                            'title' => 'implementation',
                            'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                        ],
                        [
                            'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-3.svg',
                            'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-3.jpg',
                            'title' => 'collaboration',
                            'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                        ],
                    ],
                    'faq_title_before' => 'Lets address your',
                    'faq_title_highlight' => 'questions',
                    'faq_title_after' => 'today!',
                    'faqs' => [
                        [
                            'question' => 'What services does your agency offer?',
                            'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                        ],
                        [
                            'question' => 'How long does a typical project take?',
                            'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                        ],
                        [
                            'question' => 'Do you work with small businesses?',
                            'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                        ],
                        [
                            'question' => 'Can you help with existing websites?',
                            'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                        ],
                    ],
                    'show_on_home' => true,
                    'status' => 'Active',
                    'sort_order' => 1,
                ],
                    [
                        'name' => 'digital marketing',
                        'slug' => 'digital-marketing',
                        'card_icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-2.svg',
                        'short_description' => 'Developing a compelling brand identity through strategy, visuals, and  to build strong customer connections.',
                        'detail_feature_image_url' => 'https://html.awaikenthemes.com/artistic/images/service-single-img.jpg',
                        'intro_paragraph_1' => 'Our digital marketing services are designed to elevate your brand and reach your target audience effectively. We develop customized strategies that include SEO, PPC advertising, social media marketing, content marketing, and email campaigns. By leveraging data-driven insights and the latest trends, we maximize engagement, drive traffic, and boost conversions, ensuring a measurable return on your investment. Let us help you grow your digital presence and achieve your business goals.',
                        'intro_paragraph_2' => 'With a focus on data-driven insights, we build customized strategies that drive traffic, boost engagement, and maximize ROI. Let us help you reach your business goals with impactful digital marketing',
                        'key_features_title_before' => 'Key',
                        'key_features_title_highlight' => 'feature',
                        'key_features_title_after' => 'of digital marketing',
                        'key_features_paragraph_1' => 'Our digital marketing approach is a data-driven strategy that combines targeted outreach with creative content. We focus on reaching the right audience through precision techniques like SEO, PPC, and social media advertising, supported by engaging content marketing and email campaigns.',
                        'key_features_paragraph_2' => 'We utilize audience segmentation to divide your customer base into distinct groups based on demographics, interests, and behaviors. This allows us to deliver personalized content and messaging, ensuring relevance and higher engagement rates.',
                        'features_list' => [
                            'content marketing that tells your brand\'s story',
                            'personalized Email Marketing to Nurture Leads',
                            'social media marketing that engages and converts',
                            'comprehensive SEO services for visibility',
                            'performance analytics & reporting',
                            'performance analytics & reporting',
                        ],
                        'feature_side_image_url' => 'https://html.awaikenthemes.com/artistic/images/service-entry-img.jpg',
                        'process_title_before' => 'Our',
                        'process_title_highlight' => 'process',
                        'process_title_after' => 'of digital marketing',
                        'process_description' => 'Our digital marketing process begins with discovery and research to understand your goals. We then develop a tailored strategy and implement campaigns across various channels. Continuous monitoring and optimization ensure effectiveness, followed by regular reporting to track performance. Finally, we refine and scale efforts for sustained growth and success.',
                        'process_steps' => [
                            [
                                'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-1.svg',
                                'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-1.jpg',
                                'title' => 'discovery phase',
                                'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                            ],
                            [
                                'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-2.svg',
                                'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-2.jpg',
                                'title' => 'implementation',
                                'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                            ],
                            [
                                'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-3.svg',
                                'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-3.jpg',
                                'title' => 'collaboration',
                                'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                            ],
                        ],
                        'faq_title_before' => 'Lets address your',
                        'faq_title_highlight' => 'questions',
                        'faq_title_after' => 'today!',
                        'faqs' => [
                            [
                                'question' => 'What services does your agency offer?',
                                'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                            ],
                            [
                                'question' => 'How long does a typical project take?',
                                'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                            ],
                            [
                                'question' => 'Do you work with small businesses?',
                                'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                            ],
                            [
                                'question' => 'Can you help with existing websites?',
                                'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                            ],
                        ],
                        'show_on_home' => true,
                        'status' => 'Active',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'creative content production',
                        'slug' => 'creative-content-production',
                        'card_icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-3.svg',
                        'short_description' => 'Developing a compelling brand identity through strategy, visuals, and  to build strong customer connections.',
                        'detail_feature_image_url' => 'https://html.awaikenthemes.com/artistic/images/service-single-img.jpg',
                        'intro_paragraph_1' => 'Our digital marketing services are designed to elevate your brand and reach your target audience effectively. We develop customized strategies that include SEO, PPC advertising, social media marketing, content marketing, and email campaigns. By leveraging data-driven insights and the latest trends, we maximize engagement, drive traffic, and boost conversions, ensuring a measurable return on your investment. Let us help you grow your digital presence and achieve your business goals.',
                        'intro_paragraph_2' => 'With a focus on data-driven insights, we build customized strategies that drive traffic, boost engagement, and maximize ROI. Let us help you reach your business goals with impactful digital marketing',
                        'key_features_title_before' => 'Key',
                        'key_features_title_highlight' => 'feature',
                        'key_features_title_after' => 'of digital marketing',
                        'key_features_paragraph_1' => 'Our digital marketing approach is a data-driven strategy that combines targeted outreach with creative content. We focus on reaching the right audience through precision techniques like SEO, PPC, and social media advertising, supported by engaging content marketing and email campaigns.',
                        'key_features_paragraph_2' => 'We utilize audience segmentation to divide your customer base into distinct groups based on demographics, interests, and behaviors. This allows us to deliver personalized content and messaging, ensuring relevance and higher engagement rates.',
                        'features_list' => [
                            'content marketing that tells your brand\'s story',
                            'personalized Email Marketing to Nurture Leads',
                            'social media marketing that engages and converts',
                            'comprehensive SEO services for visibility',
                            'performance analytics & reporting',
                            'performance analytics & reporting',
                        ],
                        'feature_side_image_url' => 'https://html.awaikenthemes.com/artistic/images/service-entry-img.jpg',
                        'process_title_before' => 'Our',
                        'process_title_highlight' => 'process',
                        'process_title_after' => 'of digital marketing',
                        'process_description' => 'Our digital marketing process begins with discovery and research to understand your goals. We then develop a tailored strategy and implement campaigns across various channels. Continuous monitoring and optimization ensure effectiveness, followed by regular reporting to track performance. Finally, we refine and scale efforts for sustained growth and success.',
                        'process_steps' => [
                            [
                                'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-1.svg',
                                'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-1.jpg',
                                'title' => 'discovery phase',
                                'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                            ],
                            [
                                'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-2.svg',
                                'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-2.jpg',
                                'title' => 'implementation',
                                'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                            ],
                            [
                                'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-service-process-step-3.svg',
                                'image_url' => 'https://html.awaikenthemes.com/artistic/images/service-process-step-3.jpg',
                                'title' => 'collaboration',
                                'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research.',
                            ],
                        ],
                        'faq_title_before' => 'Lets address your',
                        'faq_title_highlight' => 'questions',
                        'faq_title_after' => 'today!',
                        'faqs' => [
                            [
                                'question' => 'What services does your agency offer?',
                                'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                            ],
                            [
                                'question' => 'How long does a typical project take?',
                                'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                            ],
                            [
                                'question' => 'Do you work with small businesses?',
                                'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                            ],
                            [
                                'question' => 'Can you help with existing websites?',
                                'answer' => 'Project timelines vary based on complexity and scope. We provide a detailed timeline during the initial consultation.',
                            ],
                        ],
                        'show_on_home' => true,
                        'status' => 'Active',
                        'sort_order' => 3,
                    ],
                ]
        ];
    }

    public static function teamContent(): array
    {
        return [
            'section' => [
                'subtitle' => 'Our Team',
                'title' => 'Meet Professional|Team members',
                'description' => 'Createlize is a dynamic best digital marketing dedicated to empowering businesses through innovative online modern technology we have used',
                'cta_text' => 'All Member',
                'cta_url' => '/team',
            ],
            'items' => [
                [
                    'name' => 'John D. Alexon',
                    'slug' => 'john-d-alexon',
                    'designation' => 'Web Developer',
                    'short_bio' => 'I help brands build practical digital experiences that convert visitors into loyal customers.',
                    'background_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/team-bg01.jpg',
                    'card_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/team01.png',
                    'detail_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/inner/team-details.jpg',
                    'email' => 'john@createlize.com',
                    'phone' => '+012-3456-789',
                    'website' => 'www.createlize.com',
                    'experience_title' => 'Personal Experience',
                    'experience_text' => 'John leads frontend delivery with a focus on usability, performance, and maintainable interfaces for modern digital products.',
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
                ],
                [
                    'name' => 'Anjelina Watson',
                    'slug' => 'anjelina-watson',
                    'designation' => 'Digital Marketer',
                    'short_bio' => 'I align creative campaigns with measurable growth so teams can scale what works.',
                    'background_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/team-bg01.jpg',
                    'card_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/team02.png',
                    'detail_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/inner/team-details.jpg',
                    'email' => 'anjelina@createlize.com',
                    'phone' => '+012-3456-789',
                    'website' => 'www.createlize.com',
                    'experience_title' => 'Personal Experience',
                    'experience_text' => 'Anjelina specializes in digital campaign planning, funnel optimization, and content performance across paid and organic channels.',
                    'skills' => [
                        ['label' => 'Tecnology', 'percent' => 86],
                        ['label' => 'Marketing', 'percent' => 92],
                        ['label' => 'Business', 'percent' => 78],
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
                    'sort_order' => 2,
                ],
                [
                    'name' => 'David T. Larner',
                    'slug' => 'david-t-larner',
                    'designation' => 'SEO Specialist',
                    'short_bio' => 'I build search visibility strategies that help businesses capture qualified demand consistently.',
                    'background_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/team-bg01.jpg',
                    'card_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/team03.png',
                    'detail_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/inner/team-details.jpg',
                    'email' => 'david@createlize.com',
                    'phone' => '+012-3456-789',
                    'website' => 'www.createlize.com',
                    'experience_title' => 'Personal Experience',
                    'experience_text' => 'David works on technical SEO, content architecture, and performance reporting to drive steady organic growth.',
                    'skills' => [
                        ['label' => 'Tecnology', 'percent' => 88],
                        ['label' => 'Marketing', 'percent' => 84],
                        ['label' => 'Business', 'percent' => 80],
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
                    'sort_order' => 3,
                ],
            ],
        ];
    }

    public static function projectsContent(): array
    {
        return [
            'section' => [
                'home_title' => 'WORKS',
                'home_heading_before' => 'Brands with cutting-edge digital',
                'home_heading_highlight' => 'solutions & design',
                'home_heading_after' => '',
                'home_description' => 'Empowering brands through innovative digital strategies, immersive design, and tailored solutions that drive growth and engagement.',
            ],
            'items' => [
                [
                    'name' => 'Axior Webflow Solutions',
                    'slug' => 'axior-webflow-solutions',
                    'category' => 'DEVELOPMENT',
                    'short_description' => "Createlize adopts client-centric approach main business's and challenge latest digital technology",
                    'project_date' => '16 May,2025',
                    'home_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/work-img1.png',
                    'list_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/home-4/project/project-01.jpg',
                    'detail_gallery_images' => [
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-1.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-2.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-3.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-4.jpg',
                    ],
                    'about_title' => 'About The Project',
                    'about_heading' => 'Deeper Dive into Our Digital Product Design Masterpieces',
                    'about_text' => 'Sed ut perspiciatis unde omniste natus voluptatem accusantiume rem aperia eaque ipsa quae abillo inventore veritatis quasi architecto beatae vitae dicta sunt explicabo. Nemo enim epsam voluptatem quia voluptas aspernatur odites sed quia consequunture',
                    'client_name' => 'Design Studio In USA',
                    'project_type' => 'Digital Product Design',
                    'website_url' => 'https://yourdomain.com',
                    'facts_title' => 'Interesting facts in|Development',
                    'facts_text' => 'Must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter',
                    'facts_list' => [
                        'Efficient Sprint Planning',
                        'Standups and Demos',
                        'Iterative Delivery Approach',
                        'Problem-solving',
                    ],
                    'results_title' => 'The Results of|Our Project',
                    'results_text' => 'Will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness rejects, dislikes, or avoids pleasure',
                    'results_items' => [
                        [
                            'title' => 'Support clients',
                            'description' => 'Sed ut perspiciatis unde omnis natus voluptatem accusantium doloremque laudantium, totam rem aperiam inventore',
                        ],
                        [
                            'title' => 'Solve problems',
                            'description' => 'Sed ut perspiciatis unde omnis natus voluptatem accusantium doloremque laudantium, totam rem aperiam inventore',
                        ],
                    ],
                    'show_on_home' => true,
                    'show_on_projects_page' => true,
                    'status' => 'Active',
                    'sort_order' => 1,
                ],
                [
                    'name' => '3D Mockup Design',
                    'slug' => '3d-mockup-design',
                    'category' => 'DEVELOPMENT',
                    'short_description' => "Createlize adopts client-centric approach main business's and challenge latest digital technology",
                    'project_date' => '16 May,2025',
                    'home_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/work-img2.png',
                    'list_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/home-4/project/project-02.jpg',
                    'detail_gallery_images' => [
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-1.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-2.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-3.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-4.jpg',
                    ],
                    'about_title' => 'About The Project',
                    'about_heading' => 'Design systems that bring visual mockups into product-ready execution',
                    'about_text' => 'We translate concept visuals into production-aligned design systems with practical structure, consistency, and measurable delivery goals.',
                    'client_name' => 'Creative Lab',
                    'project_type' => 'Brand Experience',
                    'website_url' => 'https://yourdomain.com',
                    'facts_title' => 'Interesting facts in|Development',
                    'facts_text' => 'Teams need visual systems that support implementation, reduce ambiguity, and help stakeholders approve faster with confidence.',
                    'facts_list' => [
                        'Visual prototyping',
                        'System consistency',
                        'Faster approvals',
                        'Scalable components',
                    ],
                    'results_title' => 'The Results of|Our Project',
                    'results_text' => 'A stronger production-ready visual system reduced revision cycles and improved delivery consistency.',
                    'results_items' => [
                        [
                            'title' => 'Clearer scope',
                            'description' => 'Delivery moved faster because design decisions were documented and aligned early.',
                        ],
                        [
                            'title' => 'Better presentation',
                            'description' => 'The final output improved both internal collaboration and external client confidence.',
                        ],
                    ],
                    'show_on_home' => true,
                    'show_on_projects_page' => true,
                    'status' => 'Active',
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Neon Website Design',
                    'slug' => 'neon-website-design',
                    'category' => 'DEVELOPMENT',
                    'short_description' => "Createlize adopts client-centric approach main business's and challenge latest digital technology",
                    'project_date' => '16 May,2025',
                    'home_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/work-img3.png',
                    'list_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/home-4/project/project-03.jpg',
                    'detail_gallery_images' => [
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-1.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-2.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-3.jpg',
                        'https://html.kodesolution.com/2025/nexella-html/images/inner/project-details-4.jpg',
                    ],
                    'about_title' => 'About The Project',
                    'about_heading' => 'Modern website design aligned with strong conversion-focused UX',
                    'about_text' => 'This project focused on balancing visual identity, fast navigation, and conversion intent across the user journey.',
                    'client_name' => 'Nexa Brand',
                    'project_type' => 'Website Design',
                    'website_url' => 'https://yourdomain.com',
                    'facts_title' => 'Interesting facts in|Development',
                    'facts_text' => 'Good website design requires content structure, user flow planning, and implementation discipline, not just visuals.',
                    'facts_list' => [
                        'UX-focused layout',
                        'Clear navigation',
                        'Improved messaging',
                        'Conversion direction',
                    ],
                    'results_title' => 'The Results of|Our Project',
                    'results_text' => 'The final site provided stronger brand clarity and a smoother browsing experience across pages.',
                    'results_items' => [
                        [
                            'title' => 'Higher clarity',
                            'description' => 'Users could understand the offer faster through improved layout and content flow.',
                        ],
                        [
                            'title' => 'Better engagement',
                            'description' => 'A more focused page structure supported stronger interaction with core calls to action.',
                        ],
                    ],
                    'show_on_home' => true,
                    'show_on_projects_page' => true,
                    'status' => 'Active',
                    'sort_order' => 3,
                ],
            ],
        ];
    }

    public static function whyChooseUsContent(): array
    {
        return [
            'subtitle' => 'why choose',
            'title_before' => 'Expertise for',
            'title_highlight' => 'your digital',
            'title_after' => 'growth journey',
            'description' => 'Our dedicated team is committed to understanding your unique needs, ensuring that we provide innovative strategies that drive results. With a focus on quality and integrity.',
            'items' => [
                [
                    'title' => 'Data-driven Approach',
                    'description' => 'We leverage data and insights to make informed decisions that lead to more effective and efficient solutions.',
                ],
                [
                    'title' => 'Competitive Pricing',
                    'description' => 'We offer our top-quality services at competitive prices, providing you with great value for your investment.',
                ],
                [
                    'title' => 'Ethical Business Practices',
                    'description' => 'We maintain the highest level of professionalism and ethical standards professionalism in all our business dealings.',
                ],
            ],
            'image_url' => 'https://html.awaikenthemes.com/artistic/images/why-choose-image.jpg',
        ];
    }

    public static function missionContent(): array
    {
        return [
            'icon_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/mission-icon.svg',
            'title' => 'Company Mission',
            'description' => "Createlize adopts a client-centric approach, focusing on understanding business's unique goals and challenges. By leveraging data analytics latest digital tools, the agency formulates",
            'bullet_icon_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/right-icon.svg',
            'items' => [
                'Digital Marketing',
                'Branding Solution',
                'Growth Tracking',
                'Google Rankings',
            ],
        ];
    }

    public static function workingProcessContent(): array
    {
        return [
            'subtitle' => 'how it work',
            'title_before' => 'Our proven',
            'title_highlight' => 'process',
            'title_after' => 'for achieving success',
            'description' => 'Our proven process combines research, strategy, and creativity to deliver tailored solutions that drive measurable results.',
            'items' => [
                [
                    'title' => 'discovery phase',
                    'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research and analysis of market trends.',
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-work-process-1.svg',
                    'link_url' => '/contact',
                ],
                [
                    'title' => 'implementation',
                    'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research and analysis of market trends.',
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-work-process-2.svg',
                    'link_url' => '/contact',
                ],
                [
                    'title' => 'collaboration',
                    'description' => 'Initial consultation to understand your brand, goals, and target audience Conducting research and analysis of market trends.',
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-work-process-3.svg',
                    'link_url' => '/contact',
                ],
            ],
        ];
    }

    public static function whoWeAreContent(): array
    {
        return [
            'subtitle' => 'who we are',
            'title_before' => 'Experts in',
            'title_highlight' => 'digital',
            'title_after' => 'brand innovation',
            'description' => 'We specialize in transforming brands through cutting-edge digital strategies, blending creativity with technology to drive growth, enhance engagement, and deliver memorable experiences.',
            'video_url' => 'https://www.youtube.com/watch?v=Y-x0efG1seA',
            'video_image_url' => 'https://html.awaikenthemes.com/artistic/images/experts-rating-video-bg.jpg',
            'cta_text' => 'contact now',
            'cta_url' => '/contact',
            'review_label' => '(40+ Reviews)',
            'review_images' => [
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-1.jpg',
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-2.jpg',
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-3.jpg',
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-4.jpg',
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-5.jpg',
            ],
            'items' => [
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-who-we-are-counter-1.svg',
                    'value' => '35',
                    'suffix' => 'k+',
                    'label' => 'Happy Customer Around the Word',
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-who-we-are-counter-3.svg',
                    'value' => '250',
                    'suffix' => '+',
                    'label' => 'trusted best partners and sponsers',
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-who-we-are-counter-2.svg',
                    'value' => '120',
                    'suffix' => '+',
                    'label' => 'best client support award achieved',
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-who-we-are-counter-4.svg',
                    'value' => '5',
                    'suffix' => 'k+',
                    'label' => 'active users using our best services',
                ],
            ],
        ];
    }

    public static function homeFeaturesContent(): array
    {
        return [
            'subtitle' => 'features',
            'title_before' => 'Innovative',
            'title_highlight' => 'features',
            'title_after' => 'for your digital success',
            'description' => 'Our digital services empower brands with innovative strategies and solutions for sustainable growth and engagement.',
            'cta_text' => 'learn more',
            'cta_url' => '/contact',
            'items' => [
                [
                    'image_url' => 'https://html.awaikenthemes.com/artistic/images/digital-features-img-1.jpg',
                    'title' => 'custom branding solutions',
                    'description' => 'Unique brand identity development, including logos, color palettes.',
                ],
                [
                    'image_url' => 'https://html.awaikenthemes.com/artistic/images/digital-features-img-2.jpg',
                    'title' => 'data-driven digital marketing',
                    'description' => 'Strategies combining SEO, PPC, content marketing',
                ],
            ],
        ];
    }

    public static function aboutContent(): array
    {
        return [
            'page_title' => 'About|us',
            'breadcrumb_label' => 'About Us',
            'page_title_bg_url' => 'https://html.kodesolution.com/2025/nexella-html/images/inner/page-title-bg.png',
            'main_image_url' => '/images/banner1.jpeg',
            'subtitle' => 'About Company',
            'title' => 'Reimagining the brand|potential best digital|marketing',
            'experience_value' => '30',
            'experience_suffix' => '+',
            'experience_label' => 'Industrial Years of Experience',
            'icon_url' => 'https://html.kodesolution.com/2025/nexella-html/images/main-home/iconss.png',
            'description' => 'Createlize is a dynamic digital marketing agency dedicated to empowering businesses through innovative and results-driven online',
            'cta_text' => 'More About',
            'cta_url' => '/about',
            'agency_subtitle' => 'about agency',
            'agency_title' => 'Crafting|unique digital|experiences that elevate your brand',
            'agency_cta_text' => 'more about',
            'agency_cta_url' => '/about',
            'agency_items' => [
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-about-agency-1.svg',
                    'title' => 'your success, our mission',
                    'description' => 'We measure our success by the success of our clients. With a focus on results and a dedication to quality, our mission is to deliver digital solutions that make a real impact.',
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-about-agency-2.svg',
                    'title' => 'creators of digital excellence',
                    'description' => 'At the core of our agency is a commitment to excellence and creativity. We specialize in crafting digital solutions that not only meet your needs but also elevate your brand.',
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-about-agency-3.svg',
                    'title' => 'innovating the digital landscape',
                    'description' => 'Founded on a passion for creativity and technology, we are a team of dedicated digital experts committed to transforming the way brands connect with audiences.',
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-about-agency-4.svg',
                    'title' => 'helping brands thrive online',
                    'description' => "Our purpose is simple: to help brands succeed in the digital age. We're passionate about building strong relationships with our clients and crafting custom strategies that drive results.",
                ],
            ],
            'success_subtitle' => 'Createlize Success',
            'success_title' => 'Expert-Led Strategies|Online Success',
            'success_year_value' => '25',
            'success_year_suffix' => 'Year',
            'success_year_description' => 'We have won 40+ awards believe in quality.',
            'success_award_image_url' => 'https://html.kodesolution.com/2025/nexella-html/images/home-3/award.png',
            'success_circle_icon_url' => 'https://html.kodesolution.com/2025/nexella-html/images/home-3/vec-icon.png',
            'success_stats' => [
                ['value' => '300', 'suffix' => '+', 'label' => 'Projects Completed'],
                ['value' => '67', 'suffix' => '%', 'label' => 'client success rate'],
                ['value' => '65', 'suffix' => '+', 'label' => 'Expert Members'],
                ['value' => '87', 'suffix' => '%', 'label' => 'Trusted clients 28k'],
            ],
        ];
    }

    public static function testimonialContent(): array
    {
        return [
            'subtitle' => 'testimonials',
            'title_before' => 'Read what they have to say about',
            'title_highlight' => 'working with us',
            'title_after' => '',
            'description' => 'Discover how our clients have achieved success through our innovative solutions and dedicated support.',
            'review_score' => '4.9',
            'review_label' => '(40+ Reviews)',
            'review_heading' => 'Customer experiences that speak for themselves',
            'review_images' => [
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-1.jpg',
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-2.jpg',
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-3.jpg',
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-4.jpg',
                'https://html.awaikenthemes.com/artistic/images/satisfy-client-img-5.jpg',
            ],
            'benefits' => [
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-testimonial-benefits-1.svg',
                    'title' => 'Low Cost',
                    'points' => [
                        'Competitive fee',
                        'Flexible rates',
                    ],
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-testimonial-benefits-2.svg',
                    'title' => 'Permission Less',
                    'points' => [
                        'Open for integration',
                        'Run your own nodes',
                    ],
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-testimonial-benefits-3.svg',
                    'title' => 'Secure Data',
                    'points' => [
                        'Open source sheet',
                        '360 Security',
                    ],
                ],
                [
                    'icon_url' => 'https://html.awaikenthemes.com/artistic/images/icon-testimonial-benefits-4.svg',
                    'title' => '24 X 7 Support',
                    'points' => [
                        'Toll free number',
                        'Ticket systems',
                    ],
                ],
            ],
            'items' => [
                [
                    'id' => 'testimonial-1',
                    'rating' => 5,
                    'quote' => "The team transformed our brand's online presence with creativity and precision. The results exceeded our expectations! Their digital marketing strategies helped us reach a broader audience and significantly boosted our sales.",
                    'author_image_url' => 'https://html.awaikenthemes.com/artistic/images/author-1.jpg',
                    'author_name' => 'Sarah Mitchell',
                    'author_role' => 'Marketing Director',
                    'status' => 'Active',
                    'sort_order' => 1,
                ],
                [
                    'id' => 'testimonial-2',
                    'rating' => 5,
                    'quote' => "The team transformed our brand's online presence with creativity and precision. The results exceeded our expectations! Their digital marketing strategies helped us reach a broader audience and significantly boosted our sales.",
                    'author_image_url' => 'https://html.awaikenthemes.com/artistic/images/author-2.jpg',
                    'author_name' => 'Sarah Mitchell',
                    'author_role' => 'Marketing Director',
                    'status' => 'Active',
                    'sort_order' => 2,
                ],
                [
                    'id' => 'testimonial-3',
                    'rating' => 5,
                    'quote' => "The team transformed our brand's online presence with creativity and precision. The results exceeded our expectations! Their digital marketing strategies helped us reach a broader audience and significantly boosted our sales.",
                    'author_image_url' => 'https://html.awaikenthemes.com/artistic/images/author-1.jpg',
                    'author_name' => 'Sarah Mitchell',
                    'author_role' => 'Marketing Director',
                    'status' => 'Active',
                    'sort_order' => 3,
                ],
            ],
        ];
    }
}
