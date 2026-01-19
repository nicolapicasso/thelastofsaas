<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Sponsor;
use App\Models\Event;
use App\Helpers\Sanitizer;
use App\Helpers\Slug;

/**
 * Sponsors Controller
 * TLOS - The Last of SaaS
 */
class SponsorsController extends Controller
{
    private Sponsor $sponsorModel;
    private Event $eventModel;

    public function __construct()
    {
        parent::__construct();
        $this->sponsorModel = new Sponsor();
        $this->eventModel = new Event();
    }

    /**
     * List all sponsors
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page', 1));
        $active = $this->getQuery('active');

        $conditions = [];
        if ($active !== null && $active !== '') {
            $conditions['active'] = (int) $active;
        }

        $result = $this->sponsorModel->paginate($page, 20, $conditions, ['name' => 'ASC']);

        $this->renderAdmin('sponsors/index', [
            'title' => 'Sponsors',
            'sponsors' => $result['data'],
            'pagination' => $result['pagination'],
            'currentActive' => $active,
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

        $this->renderAdmin('sponsors/form', [
            'title' => 'Nuevo Sponsor',
            'sponsor' => null,
            'events' => [],
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new sponsor
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/sponsors/create');
        }

        $data = $this->validateSponsorData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/sponsors/create');
        }

        // Generate slug and unique code
        $data['slug'] = Slug::unique($data['name'], 'sponsors');
        $data['code'] = substr(strtoupper(bin2hex(random_bytes(5))), 0, 10);

        try {
            $sponsorId = $this->sponsorModel->create($data);
            $this->flash('success', 'Sponsor creado correctamente.');
            $this->redirect('/admin/sponsors/' . $sponsorId . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el sponsor: ' . $e->getMessage());
            $this->redirect('/admin/sponsors/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $sponsor = $this->sponsorModel->find((int) $id);

        if (!$sponsor) {
            $this->flash('error', 'Sponsor no encontrado.');
            $this->redirect('/admin/sponsors');
        }

        $events = $this->sponsorModel->getEvents((int) $id);

        $this->renderAdmin('sponsors/form', [
            'title' => 'Editar Sponsor',
            'sponsor' => $sponsor,
            'events' => $events,
            'levelOptions' => Sponsor::getLevelOptions(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update sponsor
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/sponsors/' . $id . '/edit');
        }

        $sponsor = $this->sponsorModel->find((int) $id);

        if (!$sponsor) {
            $this->flash('error', 'Sponsor no encontrado.');
            $this->redirect('/admin/sponsors');
        }

        $data = $this->validateSponsorData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/sponsors/' . $id . '/edit');
        }

        // Update slug if name changed
        if ($data['name'] !== $sponsor['name']) {
            $data['slug'] = Slug::unique($data['name'], 'sponsors', 'slug', (int) $id);
        }

        try {
            $this->sponsorModel->update((int) $id, $data);
            $this->flash('success', 'Sponsor actualizado correctamente.');
            $this->redirect('/admin/sponsors/' . $id . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el sponsor: ' . $e->getMessage());
            $this->redirect('/admin/sponsors/' . $id . '/edit');
        }
    }

    /**
     * Delete sponsor
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/sponsors');
        }

        $sponsor = $this->sponsorModel->find((int) $id);

        if (!$sponsor) {
            $this->flash('error', 'Sponsor no encontrado.');
            $this->redirect('/admin/sponsors');
        }

        try {
            $this->sponsorModel->delete((int) $id);
            $this->flash('success', 'Sponsor eliminado correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el sponsor: ' . $e->getMessage());
        }

        $this->redirect('/admin/sponsors');
    }

    /**
     * Import sponsors from CSV
     */
    public function import(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->renderAdmin('sponsors/import', [
                'title' => 'Importar Sponsors',
                'csrf_token' => $this->generateCsrf(),
            ]);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/sponsors/import');
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Error al subir el archivo.');
            $this->redirect('/admin/sponsors/import');
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
            $this->redirect('/admin/sponsors/import');
        }

        // Detect delimiter
        $firstLine = $lines[0];
        $delimiter = $this->detectDelimiter($firstLine);

        $headers = str_getcsv($firstLine, $delimiter, '"', '');
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);
        $headerCount = count($headers);

        $imported = 0;
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
                $sponsorData = [
                    'name' => $data['name'],
                    'slug' => Slug::unique($data['name'], 'sponsors'),
                    'tagline' => $data['tagline'] ?? null,
                    'description' => $data['description'] ?? null,
                    'website' => $data['website'] ?? null,
                    'logo_url' => $data['logo_url'] ?? null,
                    'contact_name' => $data['contact_name'] ?? null,
                    'contact_email' => $data['contact_email'] ?? null,
                    'contact_phone' => $data['contact_phone'] ?? null,
                    'code' => substr(strtoupper(bin2hex(random_bytes(5))), 0, 10),
                    'active' => 1,
                ];

                $this->sponsorModel->create($sponsorData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Línea {$i}: " . $e->getMessage();
            }
        }

        $message = "Importados {$imported} sponsors.";
        if (!empty($errors)) {
            $message .= " Errores: " . count($errors);
        }

        $this->flash($imported > 0 ? 'success' : 'error', $message);
        $this->redirect('/admin/sponsors');
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
            $this->sponsorModel->update((int) $id, ['code' => $newCode]);
            $this->jsonSuccess(['code' => $newCode, 'message' => 'Código regenerado.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al regenerar código: ' . $e->getMessage());
        }
    }

    /**
     * Export sponsors to CSV
     */
    public function export(): void
    {
        $this->requireAuth();

        $sponsors = $this->sponsorModel->all(['name' => 'ASC']);

        $filename = 'sponsors_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel

        // Headers
        fputcsv($output, ['name', 'tagline', 'description', 'website', 'logo_url', 'contact_name', 'contact_email', 'contact_phone', 'code', 'active'], ';');

        foreach ($sponsors as $sponsor) {
            fputcsv($output, [
                $sponsor['name'],
                $sponsor['tagline'] ?? '',
                $sponsor['description'] ?? '',
                $sponsor['website'] ?? '',
                $sponsor['logo_url'] ?? '',
                $sponsor['contact_name'] ?? '',
                $sponsor['contact_email'] ?? '',
                $sponsor['contact_phone'] ?? '',
                $sponsor['code'] ?? '',
                $sponsor['active'],
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
     * Validate sponsor form data
     */
    private function validateSponsorData(): array
    {
        $errors = [];

        $name = Sanitizer::string($this->getPost('name'));
        $tagline = Sanitizer::string($this->getPost('tagline'));
        $description = $this->getPost('description');
        $website = Sanitizer::url($this->getPost('website'));
        $logoUrl = Sanitizer::url($this->getPost('logo_url'));
        $contactName = Sanitizer::string($this->getPost('contact_name'));
        $contactEmail = Sanitizer::string($this->getPost('contact_email'));
        $contactPhone = Sanitizer::string($this->getPost('contact_phone'));
        $active = Sanitizer::bool($this->getPost('active'));
        $maxSimultaneousMeetings = Sanitizer::int($this->getPost('max_simultaneous_meetings')) ?: 1;
        $linkedinUrl = Sanitizer::url($this->getPost('linkedin_url'));
        $twitterUrl = Sanitizer::url($this->getPost('twitter_url'));

        if (empty($name)) {
            $errors[] = 'El nombre es obligatorio.';
        }

        // Handle logo file upload
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleLogoUpload($_FILES['logo_file'], 'sponsors');
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
            'tagline' => $tagline ?: null,
            'description' => $description ?: null,
            'website' => $website ?: null,
            'logo_url' => $logoUrl ?: null,
            'contact_name' => $contactName ?: null,
            'contact_email' => $contactEmail ?: null,
            'contact_phone' => $contactPhone ?: null,
            'active' => $active ? 1 : 0,
            'max_simultaneous_meetings' => $maxSimultaneousMeetings,
            'linkedin_url' => $linkedinUrl ?: null,
            'twitter_url' => $twitterUrl ?: null,
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
}
