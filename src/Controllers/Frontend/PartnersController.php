<?php
/**
 * Partners Controller (Frontend)
 * Omniwallet CMS
 */

namespace App\Controllers\Frontend;

use App\Models\Partner;
use App\Models\Setting;

class PartnersController extends BaseController
{
    private Partner $partnerModel;

    public function __construct()
    {
        parent::__construct();
        $this->partnerModel = new Partner();
    }

    /**
     * Partners directory listing
     */
    public function index(): void
    {
        $country = $_GET['pais'] ?? null;
        $city = $_GET['ciudad'] ?? null;
        $type = $_GET['tipo'] ?? null;
        $certified = isset($_GET['certificado']) ? (bool)$_GET['certificado'] : null;
        $search = trim($_GET['buscar'] ?? '');

        // Get partners based on filters
        $partners = $this->partnerModel->search($search ?: null, $country, $city, $type, $certified);

        // Get filter options
        $countries = $this->partnerModel->getCountriesWithCounts();
        $cities = $country ? $this->partnerModel->getCitiesWithCounts($country) : [];
        $types = $this->partnerModel->getTypesWithCounts();
        $totalPartners = $this->partnerModel->countActive();
        $featuredPartners = $this->partnerModel->getFeatured(4);
        $certifiedPartners = $this->partnerModel->getCertified(6);

        // Get certification seal from settings
        $settingModel = new Setting();
        $certificationSeal = $settingModel->get('partner_certification_seal');

        // Translate partners
        $this->translator->translateEntities('partner', $partners);
        $this->translator->translateEntities('partner', $featuredPartners);

        // SEO
        $this->seo->setTitle('Partners | Directorio de Agencias y Tech Partners | Omniwallet');
        $this->seo->setDescription('Encuentra partners oficiales de Omniwallet cerca de ti. Agencias y tech partners certificados que te ayudarán a implementar y sacar el máximo partido a tu programa de fidelización.');
        $this->seo->setCanonical('/partners');

        $this->view('partners/index', [
            'partners' => $partners,
            'featuredPartners' => $featuredPartners,
            'certifiedPartners' => $certifiedPartners,
            'countries' => $countries,
            'cities' => $cities,
            'types' => $types,
            'partnerTypes' => Partner::TYPES,
            'totalPartners' => $totalPartners,
            'currentCountry' => $country,
            'currentCity' => $city,
            'currentType' => $type,
            'currentCertified' => $certified,
            'searchQuery' => $search,
            'certificationSeal' => $certificationSeal
        ]);
    }

    /**
     * Show single partner
     */
    public function show(string $slug): void
    {
        $partner = $this->partnerModel->findBySlug($slug);

        if (!$partner) {
            $this->notFound();
            return;
        }

        // Get related partners (same country or type)
        $relatedPartners = $this->partnerModel->getRelated(
            $partner['id'],
            $partner['country'],
            $partner['partner_type'],
            4
        );

        // Get certification seal from settings
        $settingModel = new Setting();
        $certificationSeal = $settingModel->get('partner_certification_seal');

        // Translate partner and related
        $this->translator->translateEntity('partner', $partner);
        $this->translator->translateEntities('partner', $relatedPartners);

        // SEO
        $title = $partner['meta_title'] ?? "{$partner['name']} | Partner Oficial de Omniwallet";
        $description = $partner['meta_description'] ?? $this->generateMetaDescription($partner);

        $this->seo->setTitle($title);
        $this->seo->setDescription($description);
        $this->seo->setCanonical("/partners/{$slug}");
        $this->seo->setImage($partner['featured_image'] ?? $partner['logo'] ?? '');

        // Organization schema
        $this->seo->addSchema($this->partnerModel->getForSchema($partner));

        $this->view('partners/show', [
            'partner' => $partner,
            'relatedPartners' => $relatedPartners,
            'partnerTypes' => Partner::TYPES,
            'certificationSeal' => $certificationSeal
        ]);
    }

    /**
     * Generate meta description from partner data
     */
    private function generateMetaDescription(array $partner): string
    {
        $parts = [];

        $parts[] = $partner['name'];

        if ($partner['partner_type'] === 'agency') {
            $parts[] = 'es una agencia';
        } else {
            $parts[] = 'es un tech partner';
        }

        if ($partner['is_certified']) {
            $parts[] = 'certificada';
        }

        $parts[] = 'de Omniwallet';

        if ($partner['city'] || $partner['country']) {
            $location = trim(($partner['city'] ?? '') . ', ' . ($partner['country'] ?? ''), ', ');
            $parts[] = "ubicada en {$location}";
        }

        return implode(' ', $parts) . '.';
    }

    /**
     * AJAX: Get cities for a country
     */
    public function getCities(): void
    {
        header('Content-Type: application/json');

        $country = $_GET['country'] ?? '';

        if (empty($country)) {
            echo json_encode([]);
            return;
        }

        $cities = $this->partnerModel->getCitiesWithCounts($country);
        echo json_encode($cities);
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Partner no encontrado');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
