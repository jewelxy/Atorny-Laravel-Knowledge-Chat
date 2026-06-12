<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsSectionTranslation;
use Illuminate\Database\Seeder;

/**
 * Seeds the home page with hero, attorney introduction, bankruptcy solutions, why choose us, resources & services, testimonials, FAQ, service areas, and consultation CTA sections.
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

        // Remove legacy / unused demo sections.
        CmsSection::query()
            ->where('cms_page_id', $page->id)
            ->whereIn('section_key', ['hero', 'faq_primary', 'pricing_plans'])
            ->delete();

        $this->seedHeroSection($page);
        $this->seedAttorneyIntroductionSection($page);
        $this->seedBankruptcySolutionsSection($page);
        $this->seedWhyChooseUsSection($page);
        $this->seedResourcesAndServicesSection($page);
        $this->seedTestimonialsSection($page);
        $this->seedFaqSection($page);
        $this->seedServiceAreasSection($page);
        $this->seedConsultationCtaSection($page);
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
            ['data' => $this->heroDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->heroDataEs()]
        );
    }

    private function seedAttorneyIntroductionSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'attorney_intro_main'],
            [
                'section_type' => 'attorney_introduction',
                'sort_order' => 10,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            ['data' => $this->attorneyIntroductionDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->attorneyIntroductionDataEs()]
        );
    }

    private function seedBankruptcySolutionsSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'bankruptcy_solutions_main'],
            [
                'section_type' => 'bankruptcy_solutions',
                'sort_order' => 20,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            ['data' => $this->bankruptcySolutionsDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->bankruptcySolutionsDataEs()]
        );
    }

    private function seedWhyChooseUsSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'why_choose_us_main'],
            [
                'section_type' => 'why_choose_us',
                'sort_order' => 30,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            ['data' => $this->whyChooseUsDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->whyChooseUsDataEs()]
        );
    }

    private function seedResourcesAndServicesSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'resources_and_services_main'],
            [
                'section_type' => 'resources_and_services',
                'sort_order' => 40,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            ['data' => $this->resourcesAndServicesDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->resourcesAndServicesDataEs()]
        );
    }

    private function seedTestimonialsSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'testimonials_main'],
            [
                'section_type' => 'testimonials',
                'sort_order' => 50,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            ['data' => $this->testimonialsDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->testimonialsDataEs()]
        );
    }

    /** @return list<array<string, mixed>> */
    private function testimonialsItemsEn(): array
    {
        return [
            [
                'id' => 'sarah-smith',
                'quote' => 'I was referred to MacLean Chung Firm by a family friend and I would refer them to anyone else who is in need of their services. They made me feel comfortable and was always.',
                'name' => 'Sarah Smith',
                'role' => 'Student',
            ],
            [
                'id' => 'ralph-edwards',
                'quote' => 'I was referred to MacLean Chung Firm by a family friend and I would refer them to anyone else who is in need of their services. They made me feel comfortable and was always.',
                'name' => 'Ralph Edwards',
                'role' => 'Student',
            ],
            [
                'id' => 'darrell-steward',
                'quote' => 'I was referred to MacLean Chung Firm by a family friend and I would refer them to anyone else who is in need of their services. They made me feel comfortable and was always.',
                'name' => 'Darrell Steward',
                'role' => 'Student',
            ],
            [
                'id' => 'jenny-wilson',
                'quote' => 'I was referred to MacLean Chung Firm by a family friend and I would refer them to anyone else who is in need of their services. They made me feel comfortable and was always.',
                'name' => 'Jenny Wilson',
                'role' => 'Student',
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function testimonialsDataEn(): array
    {
        return [
            'media' => [
                'background' => ['src' => '/Assets/TestimonialsSection/bg-testimonials.png'],
            ],
            'intro' => [
                'ratingBadge' => 'Rated 5.00 Stars',
                'heading' => 'High Quality & Prestigious Brands',
                'description' => 'Hear from individuals and families who trusted us to guide them through difficult financial situations with compassion, clarity, and confidence.',
                'cta' => [
                    'label' => 'View all reviews',
                    'href' => '/reviews',
                ],
            ],
            'testimonials' => $this->testimonialsItemsEn(),
        ];
    }

    /** @return array<string, mixed> */
    private function testimonialsDataEs(): array
    {
        return [
            'media' => [
                'background' => ['src' => '/Assets/TestimonialsSection/bg-testimonials.png'],
            ],
            'intro' => [
                'ratingBadge' => 'Calificado con 5.00 estrellas',
                'heading' => 'Marcas de alta calidad y prestigio',
                'description' => 'Escuche a personas y familias que confiaron en nosotros para guiarlos a través de situaciones financieras difíciles con compasión, claridad y confianza.',
                'cta' => [
                    'label' => 'Ver todas las reseñas',
                    'href' => '/reviews',
                ],
            ],
            'testimonials' => [
                [
                    'id' => 'sarah-smith',
                    'quote' => 'Me refirieron al bufete MacLean Chung por un familiar y los recomendaría a cualquiera que necesite sus servicios. Me hicieron sentir cómoda y siempre estuvieron disponibles.',
                    'name' => 'Sarah Smith',
                    'role' => 'Estudiante',
                ],
                [
                    'id' => 'ralph-edwards',
                    'quote' => 'Me refirieron al bufete MacLean Chung por un familiar y los recomendaría a cualquiera que necesite sus servicios. Me hicieron sentir cómodo y siempre estuvieron disponibles.',
                    'name' => 'Ralph Edwards',
                    'role' => 'Estudiante',
                ],
                [
                    'id' => 'darrell-steward',
                    'quote' => 'Me refirieron al bufete MacLean Chung por un familiar y los recomendaría a cualquiera que necesite sus servicios. Me hicieron sentir cómodo y siempre estuvieron disponibles.',
                    'name' => 'Darrell Steward',
                    'role' => 'Estudiante',
                ],
                [
                    'id' => 'jenny-wilson',
                    'quote' => 'Me refirieron al bufete MacLean Chung por un familiar y los recomendaría a cualquiera que necesite sus servicios. Me hicieron sentir cómoda y siempre estuvieron disponibles.',
                    'name' => 'Jenny Wilson',
                    'role' => 'Estudiante',
                ],
            ],
        ];
    }

    private function seedFaqSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'faq_main'],
            [
                'section_type' => 'faq',
                'sort_order' => 60,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            ['data' => $this->faqDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->faqDataEs()]
        );
    }

    /** @return list<array<string, mixed>> */
    private function faqItemsEn(): array
    {
        return [
            [
                'id' => 'wage-garnishment',
                'question' => 'Will bankruptcy stop wage garnishment?',
                'answer' => 'Filing bankruptcy triggers an automatic stay that immediately stops most wage garnishments. Creditors must cease collection efforts, including garnishing your paycheck, once your case is filed.',
            ],
            [
                'id' => 'foreclosure',
                'question' => 'Can bankruptcy help prevent foreclosure?',
                'answer' => 'Bankruptcy can temporarily halt foreclosure through the automatic stay and, in Chapter 13 cases, may allow you to catch up on missed mortgage payments through a structured repayment plan.',
            ],
            [
                'id' => 'chapter-7-or-13',
                'question' => 'Should I file Chapter 7 or Chapter 13?',
                'answer' => 'Chapter 7 is typically suited for discharging unsecured debts when you qualify under the means test. Chapter 13 may be better if you need to protect assets, catch up on mortgage or car payments, or have income to fund a repayment plan.',
            ],
            [
                'id' => 'credit-report',
                'question' => 'How long does bankruptcy remain on my credit report?',
                'answer' => 'Chapter 7 bankruptcy generally remains on your credit report for up to 10 years, while Chapter 13 typically stays for up to 7 years from the filing date.',
            ],
            [
                'id' => 'home-or-vehicle',
                'question' => 'Will I lose my home or vehicle?',
                'answer' => 'Not necessarily. Exemption laws may protect equity in your home and vehicle. Chapter 13 can also help you keep property by restructuring past-due payments into a manageable plan.',
            ],
            [
                'id' => 'rebuilding-credit',
                'question' => 'How soon can I start rebuilding my credit?',
                'answer' => 'You can begin rebuilding credit shortly after your bankruptcy discharge by maintaining on-time payments, using secured credit responsibly, and keeping balances low relative to available credit.',
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function faqDataEn(): array
    {
        return [
            'sectionTitle' => 'Frequently Asked Bankruptcy Questions',
            'items' => $this->faqItemsEn(),
        ];
    }

    /** @return array<string, mixed> */
    private function faqDataEs(): array
    {
        return [
            'sectionTitle' => 'Preguntas frecuentes sobre bancarrota',
            'items' => [
                [
                    'id' => 'wage-garnishment',
                    'question' => '¿La bancarrota detendrá el embargo de salarios?',
                    'answer' => 'Presentar la bancarrota activa una suspensión automática que detiene de inmediato la mayoría de los embargos de salarios. Los acreedores deben cesar los esfuerzos de cobro, incluido el embargo de su salario, una vez presentado su caso.',
                ],
                [
                    'id' => 'foreclosure',
                    'question' => '¿Puede la bancarrota ayudar a prevenir la ejecución hipotecaria?',
                    'answer' => 'La bancarrota puede detener temporalmente la ejecución hipotecaria mediante la suspensión automática y, en los casos del Capítulo 13, puede permitirle ponerse al día con los pagos hipotecarios atrasados mediante un plan de reembolso estructurado.',
                ],
                [
                    'id' => 'chapter-7-or-13',
                    'question' => '¿Debo presentar el Capítulo 7 o el Capítulo 13?',
                    'answer' => 'El Capítulo 7 generalmente es adecuado para cancelar deudas no garantizadas cuando califica según la prueba de medios. El Capítulo 13 puede ser mejor si necesita proteger activos, ponerse al día con pagos de hipoteca o automóvil, o tiene ingresos para financiar un plan de reembolso.',
                ],
                [
                    'id' => 'credit-report',
                    'question' => '¿Cuánto tiempo permanece la bancarrota en mi informe crediticio?',
                    'answer' => 'La bancarrota del Capítulo 7 generalmente permanece en su informe crediticio hasta 10 años, mientras que el Capítulo 13 normalmente permanece hasta 7 años desde la fecha de presentación.',
                ],
                [
                    'id' => 'home-or-vehicle',
                    'question' => '¿Perderé mi casa o vehículo?',
                    'answer' => 'No necesariamente. Las leyes de exención pueden proteger el capital en su hogar y vehículo. El Capítulo 13 también puede ayudarle a conservar la propiedad reestructurando los pagos atrasados en un plan manejable.',
                ],
                [
                    'id' => 'rebuilding-credit',
                    'question' => '¿Qué tan pronto puedo comenzar a reconstruir mi crédito?',
                    'answer' => 'Puede comenzar a reconstruir su crédito poco después de la descarga de su bancarrota manteniendo pagos puntuales, usando crédito garantizado de manera responsable y manteniendo saldos bajos en relación con el crédito disponible.',
                ],
            ],
        ];
    }

    private function seedServiceAreasSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'service_areas_main'],
            [
                'section_type' => 'service_areas',
                'sort_order' => 70,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            ['data' => $this->serviceAreasDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->serviceAreasDataEs()]
        );
    }

    /** @return list<list<string>> */
    private function serviceAreaLocationsEn(): array
    {
        return [
            ['Los Angeles', 'San Diego', 'San Jose'],
            ['San Francisco', 'Long Beach', 'Huntington Beach'],
            ['San Bernardino', 'Moreno Valley', 'Rancho Cucamonga'],
            ['Los Angeles', 'San Diego', 'San Jose'],
        ];
    }

    /** @return array<string, mixed> */
    private function serviceAreasDataEn(): array
    {
        return [
            'media' => [
                'background' => ['src' => '/Assets/ServiceAreasSection/Background_City_Skyline_Image.png'],
                'lawOfficeImage' => [
                    'src' => '/Assets/ServiceAreasSection/Law_Office_Interior_Image.png',
                    'alt' => 'Traditional law office interior with wood paneling and bookshelves',
                ],
            ],
            'heading' => 'Serving Clients Across California',
            'description' => "David provides comprehensive bankruptcy representation to individuals and families throughout New York State. Whether you're in the heart of Manhattan or in upstate communities, experienced legal guidance is available to help you navigate your debt relief options.",
            'footerNote' => 'Virtual consultations are available for clients throughout the state, making it convenient to access professional bankruptcy services regardless of your location.',
            'locations' => $this->serviceAreaLocationsEn(),
        ];
    }

    /** @return array<string, mixed> */
    private function serviceAreasDataEs(): array
    {
        return [
            'media' => [
                'background' => ['src' => '/Assets/ServiceAreasSection/Background_City_Skyline_Image.png'],
                'lawOfficeImage' => [
                    'src' => '/Assets/ServiceAreasSection/Law_Office_Interior_Image.png',
                    'alt' => 'Interior tradicional de bufete de abogados con paneles de madera y estanterías',
                ],
            ],
            'heading' => 'Atendiendo clientes en todo California',
            'description' => 'David brinda representación integral en bancarrota a personas y familias en todo el estado de Nueva York. Ya sea en el corazón de Manhattan o en comunidades del norte del estado, hay orientación legal experimentada disponible para ayudarle a navegar sus opciones de alivio de deudas.',
            'footerNote' => 'Las consultas virtuales están disponibles para clientes en todo el estado, lo que facilita el acceso a servicios profesionales de bancarrota sin importar su ubicación.',
            'locations' => [
                ['Los Ángeles', 'San Diego', 'San José'],
                ['San Francisco', 'Long Beach', 'Huntington Beach'],
                ['San Bernardino', 'Moreno Valley', 'Rancho Cucamonga'],
                ['Los Ángeles', 'San Diego', 'San José'],
            ],
        ];
    }

    private function seedConsultationCtaSection(CmsPage $page): void
    {
        $section = CmsSection::updateOrCreate(
            ['cms_page_id' => $page->id, 'section_key' => 'consultation_cta_main'],
            [
                'section_type' => 'consultation_cta',
                'sort_order' => 80,
                'is_active' => true,
            ]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'en'],
            ['data' => $this->consultationCtaDataEn()]
        );

        CmsSectionTranslation::updateOrCreate(
            ['cms_section_id' => $section->id, 'locale' => 'es'],
            ['data' => $this->consultationCtaDataEs()]
        );
    }

    /** @return list<string> */
    private function consultationCtaFeaturesEn(): array
    {
        return [
            'Free Consultation',
            'Confidential Case Review',
            'Flexible Appointment Options',
            'Experienced Bankruptcy Representation',
        ];
    }

    /** @return array<string, mixed> */
    private function consultationCtaDataEn(): array
    {
        return [
            'media' => [
                'backgroundPattern' => ['src' => '/Assets/ConsultationCTASection/PremiumBackgroundPattern.png'],
                'rightSideImage' => [
                    'src' => '/Assets/ConsultationCTASection/right-side-image.png',
                    'alt' => 'Professional attorney consulting with a client by phone',
                ],
            ],
            'heading' => 'Take The First Step Toward Financial Freedom',
            'descriptionLine1' => 'The sooner you understand your options, the sooner you',
            'descriptionLine2' => 'can begin moving forward with confidence.',
            'features' => $this->consultationCtaFeaturesEn(),
            'form' => [
                'title' => 'Request Your Free Consultation',
                'submitCta' => 'Get My Free Consultation',
                'labels' => [
                    'name' => 'Name',
                    'phone' => 'Phone',
                    'email' => 'Email',
                    'message' => 'How Can We Help?',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function consultationCtaDataEs(): array
    {
        return [
            'media' => [
                'backgroundPattern' => ['src' => '/Assets/ConsultationCTASection/PremiumBackgroundPattern.png'],
                'rightSideImage' => [
                    'src' => '/Assets/ConsultationCTASection/right-side-image.png',
                    'alt' => 'Abogado profesional consultando con un cliente por teléfono',
                ],
            ],
            'heading' => 'Dé el primer paso hacia la libertad financiera',
            'descriptionLine1' => 'Cuanto antes comprenda sus opciones, antes podrá',
            'descriptionLine2' => 'avanzar con confianza.',
            'features' => [
                'Consulta gratuita',
                'Revisión confidencial del caso',
                'Opciones de cita flexibles',
                'Representación experimentada en bancarrota',
            ],
            'form' => [
                'title' => 'Solicite su consulta gratuita',
                'submitCta' => 'Obtener mi consulta gratuita',
                'labels' => [
                    'name' => 'Nombre',
                    'phone' => 'Teléfono',
                    'email' => 'Correo electrónico',
                    'message' => '¿Cómo podemos ayudarle?',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function heroDataEn(): array
    {
        return [
            'headline' => [
                'Debt Relief Starts Here.',
                'Get A Fresh',
                'Financial Start.',
            ],
            'subheadline' => 'Helping New York individuals and families eliminate overwhelming debt and regain financial control.',
            'features' => [
                'Free Consultation',
                'Virtual Appointments',
                'Chapter 7 & Chapter 13',
            ],
            'primaryCta' => [
                'label' => 'Book A Free Consultation',
                'href' => '/contact',
            ],
            'callCta' => [
                'labelPrefix' => 'Call Now',
                'phone' => '+(1) 123 456 7890',
                'phoneHref' => 'tel:+11234567890',
            ],
            'media' => [
                'background' => ['src' => '/Assets/hero/componentbackground.png'],
                'portrait' => [
                    'src' => '/Assets/hero/davidimage.png',
                    'alt' => 'David H. Chung',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function heroDataEs(): array
    {
        return [
            'headline' => [
                'La liberación de deudas comienza aquí.',
                'Obtenga un nuevo',
                'comienzo financiero.',
            ],
            'subheadline' => 'Ayudamos a personas y familias de Nueva York a eliminar deudas abrumadoras y recuperar el control financiero.',
            'features' => [
                'Consulta gratuita',
                'Citas virtuales',
                'Capítulo 7 y Capítulo 13',
            ],
            'primaryCta' => [
                'label' => 'Reserve una consulta gratuita',
                'href' => '/contact',
            ],
            'callCta' => [
                'labelPrefix' => 'Llame ahora',
                'phone' => '+(1) 123 456 7890',
                'phoneHref' => 'tel:+11234567890',
            ],
            'media' => [
                'background' => ['src' => '/Assets/hero/componentbackground.png'],
                'portrait' => [
                    'src' => '/Assets/hero/davidimage.png',
                    'alt' => 'David H. Chung',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function attorneyIntroductionDataEn(): array
    {
        return [
            'headline' => 'Meet David — Your Trusted New York Bankruptcy Attorney',
            'body' => 'For years, David has helped individuals and families throughout New York overcome overwhelming debt and financial hardship. He understands that financial challenges can happen to anyone, whether caused by medical bills, job loss, divorce, business struggles, or unexpected life events.',
            'features' => [
                '15+ Years Experience',
                '1000+ Cases Resolved',
                'Chapter 7 & Chapter 13 Focus',
                '30 Mins Free Consultation',
            ],
            'stats' => [
                ['value' => '3000+', 'label' => 'Cases Resolved'],
                ['value' => '15+', 'label' => 'Years Experience'],
                ['value' => '24 Hour', 'label' => 'Response Time'],
                ['value' => 'A+', 'label' => 'Rated by BBB'],
                ['value' => 'Serving', 'label' => 'All the New York'],
            ],
            'primaryCta' => [
                'label' => 'Learn More Details',
                'href' => '/about',
            ],
            'callCta' => [
                'labelPrefix' => 'Call Now',
                'phone' => '+(1) 123 456 7890',
                'phoneHref' => 'tel:+11234567890',
            ],
            'media' => [
                'background' => [
                    'src' => '/Assets/AttorneyIntroductionSection/AttorneyIntroductionSection-component-bg.png',
                ],
                'handshake' => [
                    'src' => '/Assets/AttorneyIntroductionSection/AttorneyHandshakeImage.png',
                    'alt' => 'Attorney David shaking hands with a client',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function attorneyIntroductionDataEs(): array
    {
        return [
            'headline' => 'Conozca a David — Su abogado de bancarrota de confianza en Nueva York',
            'body' => 'Durante años, David ha ayudado a personas y familias en todo Nueva York a superar deudas abrumadoras y dificultades financieras. Entiende que los desafíos financieros pueden ocurrirle a cualquiera, ya sea por facturas médicas, pérdida de empleo, divorcio, problemas empresariales o eventos inesperados.',
            'features' => [
                'Más de 15 años de experiencia',
                'Más de 1000 casos resueltos',
                'Enfoque en Capítulo 7 y Capítulo 13',
                'Consulta gratuita de 30 minutos',
            ],
            'stats' => [
                ['value' => '3000+', 'label' => 'Casos resueltos'],
                ['value' => '15+', 'label' => 'Años de experiencia'],
                ['value' => '24 horas', 'label' => 'Tiempo de respuesta'],
                ['value' => 'A+', 'label' => 'Calificado por BBB'],
                ['value' => 'Sirviendo', 'label' => 'Todo Nueva York'],
            ],
            'primaryCta' => [
                'label' => 'Más detalles',
                'href' => '/about',
            ],
            'callCta' => [
                'labelPrefix' => 'Llame ahora',
                'phone' => '+(1) 123 456 7890',
                'phoneHref' => 'tel:+11234567890',
            ],
            'media' => [
                'background' => [
                    'src' => '/Assets/AttorneyIntroductionSection/AttorneyIntroductionSection-component-bg.png',
                ],
                'handshake' => [
                    'src' => '/Assets/AttorneyIntroductionSection/AttorneyHandshakeImage.png',
                    'alt' => 'El abogado David estrechando la mano de un cliente',
                ],
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function bankruptcyServiceCategoriesEn(): array
    {
        return [
            [
                'id' => 'chapter-7',
                'title' => 'Chapter 7 Bankruptcy',
                'image' => [
                    'src' => '/Assets/BankruptcySolutionsSection/chapter7.png',
                    'alt' => 'Calculator and documents for Chapter 7 bankruptcy filing',
                ],
            ],
            [
                'id' => 'chapter-13',
                'title' => 'Chapter 13 Bankruptcy',
                'image' => [
                    'src' => '/Assets/BankruptcySolutionsSection/chapter13.png',
                    'alt' => 'Petition documents for Chapter 13 bankruptcy',
                ],
            ],
            [
                'id' => 'foreclosure-defense',
                'title' => 'Foreclosure Defense',
                'image' => [
                    'src' => '/Assets/BankruptcySolutionsSection/foreclosureDefense.png',
                    'alt' => 'Model house and legal gavel representing foreclosure defense',
                ],
            ],
            [
                'id' => 'wage-garnishment',
                'title' => 'Wage Garnishment Attorney',
                'image' => [
                    'src' => '/Assets/BankruptcySolutionsSection/wageGranishmentAttorny.png',
                    'alt' => 'Handshake over desk with financial documents',
                ],
            ],
            [
                'id' => 'medical-debt',
                'title' => 'Medical Debt Bankruptcy',
                'image' => [
                    'src' => '/Assets/BankruptcySolutionsSection/medicalDebtBankruptcy.png',
                    'alt' => 'Stethoscope on medical bills and currency',
                ],
            ],
            [
                'id' => 'affordable-low-cost',
                'title' => 'Affordable Low Cost Bankruptcy',
                'image' => [
                    'src' => '/Assets/BankruptcySolutionsSection/affordableLowCostBankruptcy.png',
                    'alt' => 'Hands using calculator for cashflow planning',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function bankruptcySolutionsDataEn(): array
    {
        return [
            'sectionTitle' => 'Debt Relief Solutions Tailored To Your Situation',
            'serviceCategories' => $this->bankruptcyServiceCategoriesEn(),
            'messageBox' => [
                'title' => 'Send and receive messages',
                'description' => 'We design and develop world class websites and apps.',
                'cta' => [
                    'label' => 'Send Message',
                    'href' => '#contact',
                ],
            ],
            'form' => [
                'title' => 'Start Your Fresh Financial Future Today',
                'description' => 'Fill out the form below to schedule a confidential consultation with attorney David H. Chung. We\'ll review your situation and outline the best path toward financial relief.',
                'features' => [
                    'No Obligation Consultation',
                    'Chapter 7 & Chapter 13 Guidance',
                    'Direct Attorney Review',
                    'Flexible Appointment Options',
                ],
                'submitCta' => 'Schedule My Consultation',
                'placeholders' => [
                    'fullName' => 'Enter Your Full Name',
                    'email' => 'Enter your email address',
                    'phone' => 'Enter your phone number',
                    'serviceType' => 'Select Service Type',
                    'consultationType' => 'Select Consultation Type',
                    'description' => 'Briefly describe your debt situation or legal concern.',
                ],
                'serviceTypeOptions' => [
                    'Chapter 7 Bankruptcy',
                    'Chapter 13 Bankruptcy',
                    'Foreclosure Defense',
                    'Wage Garnishment',
                    'Medical Debt Bankruptcy',
                    'Affordable Low Cost Bankruptcy',
                ],
                'consultationTypeOptions' => [
                    'In-Person Consultation',
                    'Virtual Consultation',
                    'Phone Consultation',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function bankruptcySolutionsDataEs(): array
    {
        return [
            'sectionTitle' => 'Soluciones de alivio de deudas adaptadas a su situación',
            'serviceCategories' => [
                [
                    'id' => 'chapter-7',
                    'title' => 'Bancarrota Capítulo 7',
                    'image' => [
                        'src' => '/Assets/BankruptcySolutionsSection/chapter7.png',
                        'alt' => 'Calculadora y documentos para declaración de bancarrota Capítulo 7',
                    ],
                ],
                [
                    'id' => 'chapter-13',
                    'title' => 'Bancarrota Capítulo 13',
                    'image' => [
                        'src' => '/Assets/BankruptcySolutionsSection/chapter13.png',
                        'alt' => 'Documentos de petición para bancarrota Capítulo 13',
                    ],
                ],
                [
                    'id' => 'foreclosure-defense',
                    'title' => 'Defensa contra ejecución hipotecaria',
                    'image' => [
                        'src' => '/Assets/BankruptcySolutionsSection/foreclosureDefense.png',
                        'alt' => 'Casa modelo y mazo legal representando defensa de ejecución hipotecaria',
                    ],
                ],
                [
                    'id' => 'wage-garnishment',
                    'title' => 'Abogado de embargo de salarios',
                    'image' => [
                        'src' => '/Assets/BankruptcySolutionsSection/wageGranishmentAttorny.png',
                        'alt' => 'Apretón de manos sobre escritorio con documentos financieros',
                    ],
                ],
                [
                    'id' => 'medical-debt',
                    'title' => 'Bancarrota por deudas médicas',
                    'image' => [
                        'src' => '/Assets/BankruptcySolutionsSection/medicalDebtBankruptcy.png',
                        'alt' => 'Estetoscopio sobre facturas médicas y moneda',
                    ],
                ],
                [
                    'id' => 'affordable-low-cost',
                    'title' => 'Bancarrota asequible y de bajo costo',
                    'image' => [
                        'src' => '/Assets/BankruptcySolutionsSection/affordableLowCostBankruptcy.png',
                        'alt' => 'Manos usando calculadora para planificación de flujo de efectivo',
                    ],
                ],
            ],
            'messageBox' => [
                'title' => 'Enviar y recibir mensajes',
                'description' => 'Diseñamos y desarrollamos sitios web y aplicaciones de clase mundial.',
                'cta' => [
                    'label' => 'Enviar mensaje',
                    'href' => '#contact',
                ],
            ],
            'form' => [
                'title' => 'Comience su nuevo futuro financiero hoy',
                'description' => 'Complete el formulario para programar una consulta confidencial con el abogado David H. Chung. Revisaremos su situación y trazaremos el mejor camino hacia el alivio financiero.',
                'features' => [
                    'Consulta sin obligación',
                    'Orientación sobre Capítulo 7 y Capítulo 13',
                    'Revisión directa del abogado',
                    'Opciones de cita flexibles',
                ],
                'submitCta' => 'Programar mi consulta',
                'placeholders' => [
                    'fullName' => 'Ingrese su nombre completo',
                    'email' => 'Ingrese su correo electrónico',
                    'phone' => 'Ingrese su número de teléfono',
                    'serviceType' => 'Seleccione el tipo de servicio',
                    'consultationType' => 'Seleccione el tipo de consulta',
                    'description' => 'Describa brevemente su situación de deuda o inquietud legal.',
                ],
                'serviceTypeOptions' => [
                    'Bancarrota Capítulo 7',
                    'Bancarrota Capítulo 13',
                    'Defensa contra ejecución hipotecaria',
                    'Embargo de salarios',
                    'Bancarrota por deudas médicas',
                    'Bancarrota asequible y de bajo costo',
                ],
                'consultationTypeOptions' => [
                    'Consulta en persona',
                    'Consulta virtual',
                    'Consulta telefónica',
                ],
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function whyChooseUsFeaturesEn(): array
    {
        return [
            [
                'id' => 'personalized-legal-strategy',
                'icon' => ['src' => '/Assets/WhyClientsChooseDavid/Personalized-legal-strategy.svg'],
                'title' => 'Personalized Legal Strategy',
                'description' => 'Custom bankruptcy solutions designed specifically for your unique financial situation and goals.',
            ],
            [
                'id' => 'direct-attorney-access',
                'icon' => ['src' => '/Assets/WhyClientsChooseDavid/direct-attorny-access.svg'],
                'title' => 'Direct Attorney Access',
                'description' => 'Work directly with David throughout your case - no paralegals or junior associates.',
            ],
            [
                'id' => 'transparent-communication',
                'icon' => ['src' => '/Assets/WhyClientsChooseDavid/transparent-communication.svg'],
                'title' => 'Transparent Communication',
                'description' => 'Clear, honest updates at every stage with no confusing legal jargon or hidden surprises.',
            ],
            [
                'id' => 'affordable-payment-options',
                'icon' => ['src' => '/Assets/WhyClientsChooseDavid/affpordable-payment-option.svg'],
                'title' => 'Affordable Payment Options',
                'description' => 'Flexible payment plans designed to accommodate your current financial circumstances.',
            ],
            [
                'id' => 'fast-response-times',
                'icon' => ['src' => '/Assets/WhyClientsChooseDavid/fast-response-time.svg'],
                'title' => 'Fast Response Times',
                'description' => 'Quick replies to your questions and concerns, typically within 24 hours or less.',
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function whyChooseUsDataEn(): array
    {
        return [
            'sectionTitle' => 'Why Clients Choose David',
            'cardImage' => [
                'src' => '/Assets/WhyClientsChooseDavid/card-image.png',
                'alt' => 'Attorney David consulting with a client at a desk',
            ],
            'features' => $this->whyChooseUsFeaturesEn(),
            'banner' => [
                'name' => 'David H. Chung',
                'role' => 'New York Bankruptcy Attorney',
                'tagline' => [
                    'Helping You Take The First Step',
                    'Toward Financial Freedom',
                ],
                'portrait' => [
                    'src' => '/Assets/WhyClientsChooseDavid/David-h-chung.png',
                    'alt' => 'David H. Chung',
                ],
                'callCta' => [
                    'phone' => '+(1) 123 456 7890',
                    'phoneHref' => 'tel:+11234567890',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function whyChooseUsDataEs(): array
    {
        return [
            'sectionTitle' => 'Por qué los clientes eligen a David',
            'cardImage' => [
                'src' => '/Assets/WhyClientsChooseDavid/card-image.png',
                'alt' => 'El abogado David consultando con un cliente en un escritorio',
            ],
            'features' => [
                [
                    'id' => 'personalized-legal-strategy',
                    'icon' => ['src' => '/Assets/WhyClientsChooseDavid/Personalized-legal-strategy.svg'],
                    'title' => 'Estrategia legal personalizada',
                    'description' => 'Soluciones de bancarrota personalizadas diseñadas específicamente para su situación financiera y objetivos.',
                ],
                [
                    'id' => 'direct-attorney-access',
                    'icon' => ['src' => '/Assets/WhyClientsChooseDavid/direct-attorny-access.svg'],
                    'title' => 'Acceso directo al abogado',
                    'description' => 'Trabaje directamente con David durante todo su caso, sin paralegales ni asociados junior.',
                ],
                [
                    'id' => 'transparent-communication',
                    'icon' => ['src' => '/Assets/WhyClientsChooseDavid/transparent-communication.svg'],
                    'title' => 'Comunicación transparente',
                    'description' => 'Actualizaciones claras y honestas en cada etapa, sin jerga legal confusa ni sorpresas ocultas.',
                ],
                [
                    'id' => 'affordable-payment-options',
                    'icon' => ['src' => '/Assets/WhyClientsChooseDavid/affpordable-payment-option.svg'],
                    'title' => 'Opciones de pago asequibles',
                    'description' => 'Planes de pago flexibles diseñados para adaptarse a sus circunstancias financieras actuales.',
                ],
                [
                    'id' => 'fast-response-times',
                    'icon' => ['src' => '/Assets/WhyClientsChooseDavid/fast-response-time.svg'],
                    'title' => 'Tiempos de respuesta rápidos',
                    'description' => 'Respuestas rápidas a sus preguntas e inquietudes, generalmente en 24 horas o menos.',
                ],
            ],
            'banner' => [
                'name' => 'David H. Chung',
                'role' => 'Abogado de bancarrota en Nueva York',
                'tagline' => [
                    'Ayudándole a dar el primer paso',
                    'hacia la libertad financiera',
                ],
                'portrait' => [
                    'src' => '/Assets/WhyClientsChooseDavid/David-h-chung.png',
                    'alt' => 'David H. Chung',
                ],
                'callCta' => [
                    'phone' => '+(1) 123 456 7890',
                    'phoneHref' => 'tel:+11234567890',
                ],
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function resourcesAndServicesStepsEn(): array
    {
        return [
            [
                'id' => 'free-consultation',
                'step' => 'Step 1',
                'icon' => ['src' => '/Assets/ResourcesAndServicesSection/free-consultation.svg'],
                'title' => 'Free Consultation',
                'description' => 'Meet with an experienced attorney to discuss your situation at no cost.',
            ],
            [
                'id' => 'digital-intake',
                'step' => 'Step 2',
                'icon' => ['src' => '/Assets/ResourcesAndServicesSection/digital-intake.svg'],
                'title' => 'Digital Intake',
                'description' => 'Complete your paperwork online—no PDFs, no hassle, just simple forms.',
            ],
            [
                'id' => 'file-your-case',
                'step' => 'Step 3',
                'icon' => ['src' => '/Assets/ResourcesAndServicesSection/file-your-case.svg'],
                'title' => 'File Your Case',
                'description' => 'We prepare and file your bankruptcy petition with the court.',
            ],
            [
                'id' => 'fresh-start',
                'step' => 'Step 4',
                'icon' => ['src' => '/Assets/ResourcesAndServicesSection/fresh-start.svg'],
                'title' => 'Fresh Start',
                'description' => 'Get debt relief and begin rebuilding your financial future.',
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function resourcesAndServicesDataEn(): array
    {
        return [
            'sectionTitle' => 'Explore Services & Resources',
            'media' => [
                'background' => ['src' => '/Assets/ResourcesAndServicesSection/bg-image.png'],
                'overflowOverlay' => [
                    'src' => '/Assets/ResourcesAndServicesSection/bg-image-overflow-transparent-bg.png',
                ],
            ],
            'steps' => $this->resourcesAndServicesStepsEn(),
        ];
    }

    /** @return array<string, mixed> */
    private function resourcesAndServicesDataEs(): array
    {
        return [
            'sectionTitle' => 'Explore servicios y recursos',
            'media' => [
                'background' => ['src' => '/Assets/ResourcesAndServicesSection/bg-image.png'],
                'overflowOverlay' => [
                    'src' => '/Assets/ResourcesAndServicesSection/bg-image-overflow-transparent-bg.png',
                ],
            ],
            'steps' => [
                [
                    'id' => 'free-consultation',
                    'step' => 'Paso 1',
                    'icon' => ['src' => '/Assets/ResourcesAndServicesSection/free-consultation.svg'],
                    'title' => 'Consulta gratuita',
                    'description' => 'Reúnase con un abogado experimentado para hablar de su situación sin costo.',
                ],
                [
                    'id' => 'digital-intake',
                    'step' => 'Paso 2',
                    'icon' => ['src' => '/Assets/ResourcesAndServicesSection/digital-intake.svg'],
                    'title' => 'Registro digital',
                    'description' => 'Complete su documentación en línea: sin PDF, sin complicaciones, solo formularios simples.',
                ],
                [
                    'id' => 'file-your-case',
                    'step' => 'Paso 3',
                    'icon' => ['src' => '/Assets/ResourcesAndServicesSection/file-your-case.svg'],
                    'title' => 'Presente su caso',
                    'description' => 'Preparamos y presentamos su petición de bancarrota ante el tribunal.',
                ],
                [
                    'id' => 'fresh-start',
                    'step' => 'Paso 4',
                    'icon' => ['src' => '/Assets/ResourcesAndServicesSection/fresh-start.svg'],
                    'title' => 'Nuevo comienzo',
                    'description' => 'Obtenga alivio de deudas y comience a reconstruir su futuro financiero.',
                ],
            ],
        ];
    }
}
