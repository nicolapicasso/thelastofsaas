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
        $category = $this->getQuery('category');

        $conditions = [];
        if ($active !== null && $active !== '') {
            $conditions['active'] = (int) $active;
        }
        if ($category) {
            $conditions['category'] = $category;
        }

        $result = $this->sponsorModel->paginate($page, 20, $conditions, ['name' => 'ASC']);

        // Get unique categories for filter
        $allSponsors = $this->sponsorModel->all();
        $categories = array_unique(array_filter(array_column($allSponsors, 'category')));
        sort($categories);

        $this->renderAdmin('sponsors/index', [
            'title' => 'Sponsors',
            'sponsors' => $result['data'],
            'pagination' => $result['pagination'],
            'categories' => $categories,
            'currentActive' => $active,
            'currentCategory' => $category,
            'flash' => $this->getFlash(),
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
        $data['slug'] = Slug::generate($data['name'], 'sponsors');
        $data['unique_code'] = Sponsor::generateUniqueCode();

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
            $data['slug'] = Slug::generate($data['name'], 'sponsors', (int) $id);
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
        $lines = explode("\n", $content);

        if (count($lines) < 2) {
            $this->flash('error', 'El archivo está vacío o no tiene datos.');
            $this->redirect('/admin/sponsors/import');
        }

        // Detect delimiter
        $firstLine = $lines[0];
        $delimiter = $this->detectDelimiter($firstLine);

        $headers = str_getcsv($firstLine, $delimiter);
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);

        $imported = 0;
        $errors = [];

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;

            $row = str_getcsv($line, $delimiter);
            $data = array_combine($headers, $row);

            if (empty($data['name'])) {
                $errors[] = "Línea {$i}: nombre vacío";
                continue;
            }

            try {
                $sponsorData = [
                    'name' => $data['name'],
                    'slug' => Slug::generate($data['name'], 'sponsors'),
                    'category' => $data['category'] ?? null,
                    'description' => $data['description'] ?? null,
                    'website' => $data['website'] ?? null,
                    'logo_url' => $data['logo_url'] ?? null,
                    'contact_emails' => $data['contact_emails'] ?? null,
                    'unique_code' => Sponsor::generateUniqueCode(),
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
            $newCode = Sponsor::generateUniqueCode();
            $this->sponsorModel->update((int) $id, ['unique_code' => $newCode]);
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
        fputcsv($output, ['name', 'category', 'description', 'website', 'logo_url', 'contact_emails', 'unique_code', 'active'], ';');

        foreach ($sponsors as $sponsor) {
            fputcsv($output, [
                $sponsor['name'],
                $sponsor['category'],
                $sponsor['description'],
                $sponsor['website'],
                $sponsor['logo_url'],
                $sponsor['contact_emails'],
                $sponsor['unique_code'],
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
        $category = Sanitizer::string($this->getPost('category'));
        $description = $this->getPost('description');
        $shortDescription = Sanitizer::string($this->getPost('short_description'));
        $website = Sanitizer::url($this->getPost('website'));
        $logoUrl = Sanitizer::url($this->getPost('logo_url'));
        $contactEmails = Sanitizer::string($this->getPost('contact_emails'));
        $contactPhone = Sanitizer::string($this->getPost('contact_phone'));
        $active = Sanitizer::bool($this->getPost('active'));
        $maxSimultaneousMeetings = Sanitizer::int($this->getPost('max_simultaneous_meetings')) ?: 1;
        $canSendMessages = Sanitizer::bool($this->getPost('can_send_messages'));
        $linkedinUrl = Sanitizer::url($this->getPost('linkedin_url'));
        $twitterUrl = Sanitizer::url($this->getPost('twitter_url'));

        if (empty($name)) {
            $errors[] = 'El nombre es obligatorio.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'name' => $name,
            'category' => $category ?: null,
            'description' => $description ?: null,
            'short_description' => $shortDescription ?: null,
            'website' => $website ?: null,
            'logo_url' => $logoUrl ?: null,
            'contact_emails' => $contactEmails ?: null,
            'contact_phone' => $contactPhone ?: null,
            'active' => $active ? 1 : 0,
            'max_simultaneous_meetings' => $maxSimultaneousMeetings,
            'can_send_messages' => $canSendMessages ? 1 : 0,
            'linkedin_url' => $linkedinUrl ?: null,
            'twitter_url' => $twitterUrl ?: null,
        ];
    }
}
