<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Sponsor;
use App\Helpers\Sanitizer;
use App\Helpers\Slug;

/**
 * Companies Controller
 * TLOS - The Last of SaaS
 */
class CompaniesController extends Controller
{
    private Company $companyModel;
    private CompanyContact $contactModel;
    private Sponsor $sponsorModel;

    public function __construct()
    {
        parent::__construct();
        $this->companyModel = new Company();
        $this->contactModel = new CompanyContact();
        $this->sponsorModel = new Sponsor();
    }

    /**
     * List all companies
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page', 1));
        $active = $this->getQuery('active');
        $sector = $this->getQuery('sector');
        $search = trim($this->getQuery('search', ''));

        $conditions = [];
        if ($active !== null && $active !== '') {
            $conditions['active'] = (int) $active;
        }
        if ($sector) {
            $conditions['sector'] = $sector;
        }

        // Use search or regular pagination
        if (!empty($search)) {
            $result = $this->companyModel->searchByName($search, $page, 20, $conditions);
        } else {
            $result = $this->companyModel->paginate($page, 20, $conditions, ['name' => 'ASC']);
        }

        // Get unique sectors for filter
        $allCompanies = $this->companyModel->all();
        $sectors = array_unique(array_filter(array_column($allCompanies, 'sector')));
        sort($sectors);

        $this->renderAdmin('companies/index', [
            'title' => 'Empresas',
            'companies' => $result['data'],
            'pagination' => $result['pagination'],
            'sectors' => $sectors,
            'currentActive' => $active,
            'currentSector' => $sector,
            'currentSearch' => $search,
            'sizeOptions' => Company::getSizeOptions(),
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $sponsors = $this->sponsorModel->getActive();

        $this->renderAdmin('companies/form', [
            'title' => 'Nueva Empresa',
            'company' => null,
            'contacts' => [],
            'saasUsage' => [],
            'sponsors' => $sponsors,
            'sizeOptions' => Company::getSizeOptions(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new company
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/companies/create');
        }

        $data = $this->validateCompanyData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/companies/create');
        }

        // Generate slug and unique code
        $data['slug'] = Slug::unique($data['name'], 'companies');
        $data['code'] = substr(strtoupper(bin2hex(random_bytes(5))), 0, 10);

        try {
            $companyId = $this->companyModel->create($data);

            // Save contacts
            $this->saveContacts($companyId);

            // Save SaaS usage
            $saasIds = $this->getPost('saas_usage', []);
            if (is_array($saasIds)) {
                foreach ($saasIds as $sponsorId) {
                    $this->companyModel->addSaasUsage($companyId, (int) $sponsorId);
                }
            }

            $this->flash('success', 'Empresa creada correctamente.');
            $this->redirect('/admin/companies/' . $companyId . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear la empresa: ' . $e->getMessage());
            $this->redirect('/admin/companies/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $company = $this->companyModel->find((int) $id);

        if (!$company) {
            $this->flash('error', 'Empresa no encontrada.');
            $this->redirect('/admin/companies');
        }

        $saasUsage = $this->companyModel->getSaasUsage((int) $id);
        $sponsors = $this->sponsorModel->getActive();
        $contacts = $this->contactModel->getByCompany((int) $id);

        $this->renderAdmin('companies/form', [
            'title' => 'Editar Empresa',
            'company' => $company,
            'contacts' => $contacts,
            'saasUsage' => $saasUsage,
            'saasUsageIds' => array_column($saasUsage, 'id'),
            'sponsors' => $sponsors,
            'sizeOptions' => Company::getSizeOptions(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update company
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/companies/' . $id . '/edit');
        }

        $company = $this->companyModel->find((int) $id);

        if (!$company) {
            $this->flash('error', 'Empresa no encontrada.');
            $this->redirect('/admin/companies');
        }

        $data = $this->validateCompanyData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/companies/' . $id . '/edit');
        }

        // Update slug if name changed
        if ($data['name'] !== $company['name']) {
            $data['slug'] = Slug::unique($data['name'], 'companies', 'slug', (int) $id);
        }

        try {
            $this->companyModel->update((int) $id, $data);

            // Save contacts
            $this->saveContacts((int) $id);

            // Update SaaS usage
            $currentUsage = $this->companyModel->getSaasUsage((int) $id);
            $currentIds = array_column($currentUsage, 'id');
            $newIds = $this->getPost('saas_usage', []);
            if (!is_array($newIds)) $newIds = [];
            $newIds = array_map('intval', $newIds);

            // Remove old
            foreach ($currentIds as $sponsorId) {
                if (!in_array($sponsorId, $newIds)) {
                    $this->companyModel->removeSaasUsage((int) $id, $sponsorId);
                }
            }

            // Add new
            foreach ($newIds as $sponsorId) {
                if (!in_array($sponsorId, $currentIds)) {
                    $this->companyModel->addSaasUsage((int) $id, $sponsorId);
                }
            }

            $this->flash('success', 'Empresa actualizada correctamente.');
            $this->redirect('/admin/companies/' . $id . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar la empresa: ' . $e->getMessage());
            $this->redirect('/admin/companies/' . $id . '/edit');
        }
    }

    /**
     * Delete company
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/companies');
        }

        $company = $this->companyModel->find((int) $id);

        if (!$company) {
            $this->flash('error', 'Empresa no encontrada.');
            $this->redirect('/admin/companies');
        }

        try {
            $this->companyModel->delete((int) $id);
            $this->flash('success', 'Empresa eliminada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar la empresa: ' . $e->getMessage());
        }

        $this->redirect('/admin/companies');
    }

    /**
     * Import companies from CSV
     */
    public function import(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->renderAdmin('companies/import', [
                'title' => 'Importar Empresas',
                'csrf_token' => $this->generateCsrf(),
            ]);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/companies/import');
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Error al subir el archivo.');
            $this->redirect('/admin/companies/import');
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $content = file_get_contents($file);

        // Remove UTF-8 BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        // Normalize line endings
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);

        $lines = explode("\n", $content);

        if (count($lines) < 2) {
            $this->flash('error', 'El archivo está vacío o no tiene datos.');
            $this->redirect('/admin/companies/import');
        }

        // Detect delimiter
        $firstLine = $lines[0];
        $delimiter = $this->detectDelimiter($firstLine);

        $headers = str_getcsv($firstLine, $delimiter, '"', '');
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);
        $headerCount = count($headers);

        // Pre-load all sponsors for SaaS matching (by name, case-insensitive)
        $allSponsors = $this->sponsorModel->all();
        $sponsorsByName = [];
        foreach ($allSponsors as $sponsor) {
            $normalizedName = mb_strtolower(trim($sponsor['name']));
            $sponsorsByName[$normalizedName] = $sponsor['id'];
        }

        $imported = 0;
        $saasLinked = 0;
        $errors = [];

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;

            $row = str_getcsv($line, $delimiter, '"', '');

            // Ensure row has same number of elements as headers
            if (count($row) < $headerCount) {
                $row = array_pad($row, $headerCount, '');
            } elseif (count($row) > $headerCount) {
                $row = array_slice($row, 0, $headerCount);
            }

            $data = array_combine($headers, $row);

            if (empty($data['name'])) {
                $errors[] = "Línea {$i}: nombre vacío";
                continue;
            }

            try {
                $companyData = [
                    'name' => $data['name'],
                    'slug' => Slug::unique($data['name'], 'companies'),
                    'description' => $data['description'] ?? null,
                    'website' => $data['website'] ?? null,
                    'logo_url' => $data['logo_url'] ?? null,
                    'sector' => $data['sector'] ?? null,
                    'employees' => $data['employees'] ?? null,
                    'contact_name' => $data['contact_name'] ?? null,
                    'contact_email' => $data['contact_email'] ?? null,
                    'contact_phone' => $data['contact_phone'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'code' => substr(strtoupper(bin2hex(random_bytes(5))), 0, 10),
                    'active' => 1,
                ];

                $companyId = $this->companyModel->create($companyData);
                $imported++;

                // Process SaaS usage if column exists
                $saasField = $data['saas'] ?? $data['saas_usage'] ?? $data['saas que utiliza'] ?? $data['herramientas'] ?? null;
                if (!empty($saasField)) {
                    // Split by comma and try to match each SaaS
                    $saasNames = array_map('trim', explode(',', $saasField));
                    foreach ($saasNames as $saasName) {
                        if (empty($saasName)) continue;
                        $normalizedSaas = mb_strtolower(trim($saasName));

                        // Try to find matching sponsor
                        if (isset($sponsorsByName[$normalizedSaas])) {
                            $this->companyModel->addSaasUsage($companyId, $sponsorsByName[$normalizedSaas]);
                            $saasLinked++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Línea {$i}: " . $e->getMessage();
            }
        }

        $message = "Importadas {$imported} empresas.";
        if ($saasLinked > 0) {
            $message .= " {$saasLinked} relaciones SaaS creadas.";
        }
        if (!empty($errors)) {
            $message .= " Errores: " . count($errors);
        }

        $this->flash($imported > 0 ? 'success' : 'error', $message);
        $this->redirect('/admin/companies');
    }

    /**
     * Regenerate unique code
     */
    public function regenerateCode(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        try {
            $newCode = substr(strtoupper(bin2hex(random_bytes(5))), 0, 10);
            $this->companyModel->update((int) $id, ['code' => $newCode]);
            $this->jsonSuccess(['code' => $newCode, 'message' => 'Código regenerado.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al regenerar código: ' . $e->getMessage());
        }
    }

    /**
     * Export companies to CSV
     */
    public function export(): void
    {
        $this->requireAuth();

        $companies = $this->companyModel->all(['name' => 'ASC']);

        $filename = 'empresas_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel

        // Headers
        fputcsv($output, ['name', 'description', 'website', 'logo_url', 'sector', 'employees', 'contact_name', 'contact_email', 'contact_phone', 'notes', 'code', 'active'], ';');

        foreach ($companies as $company) {
            fputcsv($output, [
                $company['name'],
                $company['description'] ?? '',
                $company['website'] ?? '',
                $company['logo_url'] ?? '',
                $company['sector'] ?? '',
                $company['employees'] ?? '',
                $company['contact_name'] ?? '',
                $company['contact_email'] ?? '',
                $company['contact_phone'] ?? '',
                $company['notes'] ?? '',
                $company['code'] ?? '',
                $company['active'],
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Detect CSV delimiter
     */
    private function detectDelimiter(string $line): string
    {
        $delimiters = [';', ',', "\t"];
        $counts = [];

        foreach ($delimiters as $d) {
            $counts[$d] = substr_count($line, $d);
        }

        return array_search(max($counts), $counts);
    }

    /**
     * Validate company form data
     */
    private function validateCompanyData(): array
    {
        $errors = [];

        $name = Sanitizer::string($this->getPost('name'));
        $description = $this->getPost('description');
        $website = Sanitizer::url($this->getPost('website'));
        $logoUrl = Sanitizer::url($this->getPost('logo_url'));
        $sector = Sanitizer::string($this->getPost('sector'));
        $employees = $this->getPost('employees');
        $contactName = Sanitizer::string($this->getPost('contact_name'));
        $contactPosition = Sanitizer::string($this->getPost('contact_position'));
        $contactEmail = Sanitizer::string($this->getPost('contact_email'));
        $contactPhone = Sanitizer::string($this->getPost('contact_phone'));
        $notes = $this->getPost('notes');
        $active = Sanitizer::bool($this->getPost('active'));

        if (empty($name)) {
            $errors[] = 'El nombre es obligatorio.';
        }

        if ($employees && !array_key_exists($employees, Company::getSizeOptions())) {
            $errors[] = 'Tamano de empresa no valido.';
        }

        // Handle logo file upload
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleLogoUpload($_FILES['logo_file'], 'companies');
            if (isset($uploadResult['error'])) {
                $errors[] = $uploadResult['error'];
            } else {
                $logoUrl = $uploadResult['url'];
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'name' => $name,
            'description' => $description ?: null,
            'website' => $website ?: null,
            'logo_url' => $logoUrl ?: null,
            'sector' => $sector ?: null,
            'employees' => $employees ?: null,
            'contact_name' => $contactName ?: null,
            'contact_position' => $contactPosition ?: null,
            'contact_email' => $contactEmail ?: null,
            'contact_phone' => $contactPhone ?: null,
            'notes' => $notes ?: null,
            'active' => $active ? 1 : 0,
        ];
    }

    /**
     * Handle logo file upload
     */
    private function handleLogoUpload(array $file, string $type): array
    {
        $allowedTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Validate file type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['error' => 'Tipo de archivo no permitido. Use PNG, JPG, GIF, SVG o WebP.'];
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['error' => 'El archivo es demasiado grande. Maximo 2MB.'];
        }

        // Create upload directory if it doesn't exist
        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/logos/' . $type;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = match($mimeType) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            'image/webp' => 'webp',
            default => 'png'
        };
        $filename = uniqid($type . '_') . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['error' => 'Error al guardar el archivo.'];
        }

        // Return the URL
        return ['url' => '/uploads/logos/' . $type . '/' . $filename];
    }

    /**
     * Save company contacts from form
     */
    private function saveContacts(int $companyId): void
    {
        $contacts = $this->getPost('contacts', []);
        $primaryIndex = $this->getPost('primary_contact', '0');

        if (!is_array($contacts)) {
            return;
        }

        // Get existing contact IDs for this company
        $existingContacts = $this->contactModel->getByCompany($companyId);
        $existingIds = array_column($existingContacts, 'id');
        $processedIds = [];

        foreach ($contacts as $index => $contactData) {
            // Skip empty contacts (no name and no email)
            if (empty(trim($contactData['name'] ?? '')) && empty(trim($contactData['email'] ?? ''))) {
                continue;
            }

            $isPrimary = ((string) $index === (string) $primaryIndex) ? 1 : 0;

            $data = [
                'company_id' => $companyId,
                'name' => Sanitizer::string($contactData['name'] ?? ''),
                'position' => Sanitizer::string($contactData['position'] ?? '') ?: null,
                'email' => Sanitizer::string($contactData['email'] ?? '') ?: null,
                'phone' => Sanitizer::string($contactData['phone'] ?? '') ?: null,
                'is_primary' => $isPrimary,
            ];

            $contactId = !empty($contactData['id']) ? (int) $contactData['id'] : null;

            if ($contactId && in_array($contactId, $existingIds)) {
                // Update existing contact
                $this->contactModel->update($contactId, $data);
                $processedIds[] = $contactId;
            } else {
                // Create new contact
                $newId = $this->contactModel->create($data);
                $processedIds[] = $newId;
            }
        }

        // Delete contacts that were removed from the form
        foreach ($existingIds as $existingId) {
            if (!in_array($existingId, $processedIds)) {
                $this->contactModel->delete($existingId);
            }
        }

        // Ensure at least one contact is primary
        $updatedContacts = $this->contactModel->getByCompany($companyId);
        if (!empty($updatedContacts)) {
            $hasPrimary = false;
            foreach ($updatedContacts as $contact) {
                if ($contact['is_primary']) {
                    $hasPrimary = true;
                    break;
                }
            }
            if (!$hasPrimary) {
                // Set first contact as primary
                $this->contactModel->setPrimary((int) $updatedContacts[0]['id'], $companyId);
            }
        }
    }
}
