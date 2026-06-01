<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsSectionTranslation;
use Illuminate\Database\Seeder;

/**
 * Seeds a demo home page with hero, FAQ, and pricing sections.
 * Demonstrates JSONB data shapes per section_type — see doc/cms-headless-architecture.md.
 */
class CmsSectionSeeder extends Seeder
{
    public function run(): void
    {
        $page = CmsPage::updateOrCreate(
            ['key' => 'home'],
            ['status' => true]
        );

        // Remove pre-refactor demo section (section_key: hero) if present.
        CmsSection::query()
            ->where('cms_page_id', $page->id)
            ->where('section_key', 'hero')
            ->delete();

        $this->seedHeroSection($page);
        $this->seedFaqSection($page);
        $this->seedPricingSection($page);
    }

    private function seedHeroSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'hero_main'],
            [
                'section_type' => 'hero',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            [
                'data' => [
                    'headline' => 'Expert Bankruptcy Guidance You Can Trust',
                    'subheadline' => 'Protect your assets and rebuild your financial future.',
                    'cta' => [
                        'label' => 'Free Consultation',
                        'href' => '/contact',
                        'variant' => 'primary',
                    ],
                    'media' => [
                        'type' => 'image',
                        'src' => '/assets/hero-home.jpg',
                        'alt' => 'Attorney meeting with client',
                    ],
                ],
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            [
                'data' => [
                    'headline' => 'Orientación experta en bancarrota en la que puede confiar',
                    'subheadline' => 'Proteja sus activos y reconstruya su futuro financiero.',
                    'cta' => [
                        'label' => 'Consulta gratuita',
                        'href' => '/contact',
                        'variant' => 'primary',
                    ],
                    'media' => [
                        'type' => 'image',
                        'src' => '/assets/hero-home.jpg',
                        'alt' => 'Abogado reunido con cliente',
                    ],
                ],
            ]
        );
    }

    private function seedFaqSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'faq_primary'],
            [
                'section_type' => 'faq',
                'sort_order' => 10,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            [
                'data' => [
                    'title' => 'Frequently Asked Questions',
                    'items' => [
                        [
                            'id' => 'faq-1',
                            'question' => 'How long does bankruptcy take?',
                            'answer' => 'Chapter 7 typically completes in 3–6 months; Chapter 13 spans 3–5 years.',
                        ],
                        [
                            'id' => 'faq-2',
                            'question' => 'Will I lose my home?',
                            'answer' => 'Exemption laws may protect your primary residence depending on equity and chapter filed.',
                        ],
                    ],
                ],
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            [
                'data' => [
                    'title' => 'Preguntas frecuentes',
                    'items' => [
                        [
                            'id' => 'faq-1',
                            'question' => '¿Cuánto tiempo tarda la bancarrota?',
                            'answer' => 'El Capítulo 7 suele completarse en 3–6 meses; el Capítulo 13 dura 3–5 años.',
                        ],
                        [
                            'id' => 'faq-2',
                            'question' => '¿Perderé mi casa?',
                            'answer' => 'Las leyes de exención pueden proteger su residencia principal según el capital y el capítulo.',
                        ],
                    ],
                ],
            ]
        );
    }

    private function seedPricingSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'pricing_plans'],
            [
                'section_type' => 'pricing',
                'sort_order' => 20,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            [
                'data' => [
                    'title' => 'Transparent Legal Plans',
                    'subtitle' => 'Choose the level of support that fits your situation.',
                    'plans' => [
                        [
                            'id' => 'plan-basic',
                            'name' => 'Essential',
                            'price' => ['amount' => 999, 'currency' => 'USD', 'period' => 'flat'],
                            'features' => ['Initial consultation', 'Document review', 'Filing preparation'],
                            'highlighted' => false,
                            'cta' => ['label' => 'Get Started', 'href' => '/contact?plan=essential'],
                        ],
                        [
                            'id' => 'plan-plus',
                            'name' => 'Full Representation',
                            'price' => ['amount' => 2499, 'currency' => 'USD', 'period' => 'flat'],
                            'features' => ['Everything in Essential', 'Court representation', 'Creditor communication'],
                            'highlighted' => true,
                            'cta' => ['label' => 'Most Popular', 'href' => '/contact?plan=full'],
                        ],
                    ],
                ],
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            [
                'data' => [
                    'title' => 'Planes legales transparentes',
                    'subtitle' => 'Elija el nivel de apoyo que se adapte a su situación.',
                    'plans' => [
                        [
                            'id' => 'plan-basic',
                            'name' => 'Esencial',
                            'price' => ['amount' => 999, 'currency' => 'USD', 'period' => 'flat'],
                            'features' => ['Consulta inicial', 'Revisión de documentos', 'Preparación de declaración'],
                            'highlighted' => false,
                            'cta' => ['label' => 'Comenzar', 'href' => '/contact?plan=essential'],
                        ],
                        [
                            'id' => 'plan-plus',
                            'name' => 'Representación completa',
                            'price' => ['amount' => 2499, 'currency' => 'USD', 'period' => 'flat'],
                            'features' => ['Todo en Esencial', 'Representación en tribunal', 'Comunicación con acreedores'],
                            'highlighted' => true,
                            'cta' => ['label' => 'Más popular', 'href' => '/contact?plan=full'],
                        ],
                    ],
                ],
            ]
        );
    }
}
