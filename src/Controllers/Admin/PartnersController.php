<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Partner;
use App\Models\SEOMetadata;
use App\Helpers\Sanitizer;

/**
 * Partners Controller (Admin)
 * Omniwallet CMS
 */
class PartnersController extends Controller
{
    private Partner $partnerModel;
    private SEOMetadata $seoModel;

    public function __construct()
    {
        parent::__construct();
        $this->partnerModel = new Partner();
        $this->seoModel = new SEOMetadata();
    }

    /**
     * List all partners
     */
    public function index(): void
    {
        $this->requireAuth();

        $type = $this->getQuery('type');
        $country = $this->getQuery('country');

        $partners = $this->partnerModel->getAllForAdmin();

        // Filter if needed
        if ($type) {
            $partners = array_filter($partners, fn($p) => $p['partner_type'] === $type);
        }
        if ($country) {
            $partners = array_filter($partners, fn($p) => $p['country'] === $country);
        }

        $countries = $this->partnerModel->getCountriesWithCounts();
        $types = $this->partnerModel->getTypesWithCounts();

        $this->renderAdmin('partners/index', [
            'title' => 'Partners',
            'partners' => $partners,
            'countries' => $countries,
            'types' => $types,
            'partnerTypes' => Partner::TYPES,
            'currentType' => $type,
            'currentCountry' => $country,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->renderAdmin('partners/form', [
            'title' => 'Nuevo Partner',
            'partner' => null,
            'partnerTypes' => Partner::TYPES,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new partner
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/partners/create');
        }

        $data = $this->validatePartnerData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/partners/create');
        }

        try {
            $this->partnerModel->createPartner($data);
            $this->flash('success', 'Partner creado correctamente.');
            $this->redirect('/admin/partners');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el partner: ' . $e->getMessage());
            $this->redirect('/admin/partners/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $partner = $this->partnerModel->find((int) $id);

        if (!$partner) {
            $this->flash('error', 'Partner no encontrado.');
            $this->redirect('/admin/partners');
        }

        // Merge SEO metadata from seo_metadata table (for mass generation support)
        $seoData = $this->seoModel->getForEntity('partner', (int) $id, 'es');
        if ($seoData) {
            // Only override if entity table has empty values
            if (empty($partner['meta_title']) && !empty($seoData['meta_title'])) {
                $partner['meta_title'] = $seoData['meta_title'];
            }
            if (empty($partner['meta_description']) && !empty($seoData['meta_description'])) {
                $partner['meta_description'] = $seoData['meta_description'];
            }
        }

        $this->renderAdmin('partners/form', [
            'title' => 'Editar Partner',
            'partner' => $partner,
            'partnerTypes' => Partner::TYPES,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update partner
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/partners/' . $id . '/edit');
        }

        $partner = $this->partnerModel->find((int) $id);

        if (!$partner) {
            $this->flash('error', 'Partner no encontrado.');
            $this->redirect('/admin/partners');
        }

        $data = $this->validatePartnerData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/partners/' . $id . '/edit');
        }

        try {
            $this->partnerModel->updatePartner((int) $id, $data);

            // Also save to seo_metadata for consistency with mass generation
            $metaTitle = $data['meta_title'] ?? null;
            $metaDescription = $data['meta_description'] ?? null;
            if (!empty($metaTitle) || !empty($metaDescription)) {
                $this->seoModel->saveForEntity('partner', (int) $id, 'es', [
                    'meta_title' => $metaTitle,
                    'meta_description' => $metaDescription,
                ]);
            }

            $this->flash('success', 'Partner actualizado correctamente.');
            $this->redirect('/admin/partners');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar: ' . $e->getMessage());
            $this->redirect('/admin/partners/' . $id . '/edit');
        }
    }

    /**
     * Delete partner
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/partners');
        }

        try {
            $this->partnerModel->delete((int) $id);
            $this->flash('success', 'Partner eliminado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar: ' . $e->getMessage());
        }

        $this->redirect('/admin/partners');
    }

    /**
     * Validate partner form data
     */
    private function validatePartnerData(): array
    {
        $errors = [];

        $name = Sanitizer::string($this->getPost('name'));
        $slug = Sanitizer::string($this->getPost('slug'));
        $website = Sanitizer::url($this->getPost('website'));
        $email = Sanitizer::email($this->getPost('email'));
        $phone = Sanitizer::string($this->getPost('phone'));
        $linkedin = Sanitizer::url($this->getPost('linkedin'));
        $logo = Sanitizer::url($this->getPost('logo'));
        $featuredImage = Sanitizer::url($this->getPost('featured_image'));
        $country = Sanitizer::string($this->getPost('country'));
        $city = Sanitizer::string($this->getPost('city'));
        $description = $this->getPost('description');
        $isCertified = Sanitizer::bool($this->getPost('is_certified'));
        $partnerType = Sanitizer::string($this->getPost('partner_type'));
        $testimonial = $this->getPost('testimonial');
        $testimonialAuthor = Sanitizer::string($this->getPost('testimonial_author'));
        $testimonialRole = Sanitizer::string($this->getPost('testimonial_role'));
        $isFeatured = Sanitizer::bool($this->getPost('is_featured'));
        $isActive = Sanitizer::bool($this->getPost('is_active'));
        $sortOrder = Sanitizer::int($this->getPost('sort_order', 0));
        $metaTitle = Sanitizer::string($this->getPost('meta_title'));
        $metaDescription = $this->getPost('meta_description');

        if (empty($name)) {
            $errors[] = 'El nombre es obligatorio.';
        }

        if (!in_array($partnerType, array_keys(Partner::TYPES))) {
            $partnerType = 'agency';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'name' => $name,
            'slug' => $slug ?: null,
            'website' => $website ?: null,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
            'linkedin' => $linkedin ?: null,
            'logo' => $logo ?: null,
            'featured_image' => $featuredImage ?: null,
            'country' => $country ?: null,
            'city' => $city ?: null,
            'description' => $description ?: null,
            'is_certified' => $isCertified ? 1 : 0,
            'partner_type' => $partnerType,
            'testimonial' => $testimonial ?: null,
            'testimonial_author' => $testimonialAuthor ?: null,
            'testimonial_role' => $testimonialRole ?: null,
            'is_featured' => $isFeatured ? 1 : 0,
            'is_active' => $isActive ? 1 : 0,
            'sort_order' => $sortOrder,
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
        ];
    }
}
