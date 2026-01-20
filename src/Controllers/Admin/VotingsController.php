<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Voting;
use App\Models\Event;
use App\Helpers\Sanitizer;
use App\Helpers\Slug;

/**
 * Votings Controller
 * TLOS - The Last of SaaS
 */
class VotingsController extends Controller
{
    private Voting $votingModel;
    private Event $eventModel;

    public function __construct()
    {
        parent::__construct();
        $this->votingModel = new Voting();
        $this->eventModel = new Event();
    }

    /**
     * List all votings
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page', 1));
        $status = $this->getQuery('status');
        $eventId = $this->getQuery('event_id');

        $conditions = [];
        if ($status) {
            $conditions['status'] = $status;
        }
        if ($eventId) {
            $conditions['event_id'] = (int) $eventId;
        }

        $result = $this->votingModel->paginate($page, 20, $conditions, ['created_at' => 'DESC']);
        $events = $this->eventModel->all(['start_date' => 'DESC']);

        $this->renderAdmin('votings/index', [
            'title' => 'Votaciones',
            'votings' => $result['data'],
            'pagination' => $result['pagination'],
            'events' => $events,
            'currentStatus' => $status,
            'currentEventId' => $eventId,
            'statusOptions' => Voting::getStatusOptions(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $events = $this->eventModel->all(['start_date' => 'DESC']);

        $this->renderAdmin('votings/form', [
            'title' => 'Nueva Votación',
            'voting' => null,
            'candidates' => [],
            'events' => $events,
            'statusOptions' => Voting::getStatusOptions(),
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Store new voting
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/votings/create');
        }

        $data = $this->validateVotingData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/votings/create');
        }

        // Generate slug
        $data['slug'] = Slug::unique($data['title'], 'votings');

        try {
            $votingId = $this->votingModel->create($data);
            $this->flash('success', 'Votación creada correctamente.');
            $this->redirect('/admin/votings/' . $votingId . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear la votación: ' . $e->getMessage());
            $this->redirect('/admin/votings/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $voting = $this->votingModel->find((int) $id);

        if (!$voting) {
            $this->flash('error', 'Votación no encontrada.');
            $this->redirect('/admin/votings');
        }

        $candidates = $this->votingModel->getCandidates((int) $id);
        $events = $this->eventModel->all(['start_date' => 'DESC']);
        $totalVotes = $this->votingModel->getTotalVotes((int) $id);

        $this->renderAdmin('votings/form', [
            'title' => 'Editar Votación',
            'voting' => $voting,
            'candidates' => $candidates,
            'events' => $events,
            'totalVotes' => $totalVotes,
            'statusOptions' => Voting::getStatusOptions(),
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Update voting
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/votings/' . $id . '/edit');
        }

        $voting = $this->votingModel->find((int) $id);

        if (!$voting) {
            $this->flash('error', 'Votación no encontrada.');
            $this->redirect('/admin/votings');
        }

        $data = $this->validateVotingData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/votings/' . $id . '/edit');
        }

        // Update slug if title changed
        if ($data['title'] !== $voting['title']) {
            $data['slug'] = Slug::unique($data['title'], 'votings', 'slug', (int) $id);
        }

        try {
            $this->votingModel->update((int) $id, $data);
            $this->flash('success', 'Votación actualizada correctamente.');
            $this->redirect('/admin/votings/' . $id . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar la votación: ' . $e->getMessage());
            $this->redirect('/admin/votings/' . $id . '/edit');
        }
    }

    /**
     * Delete voting
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/votings');
        }

        $voting = $this->votingModel->find((int) $id);

        if (!$voting) {
            $this->flash('error', 'Votación no encontrada.');
            $this->redirect('/admin/votings');
        }

        try {
            $this->votingModel->delete((int) $id);
            $this->flash('success', 'Votación eliminada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar la votación: ' . $e->getMessage());
        }

        $this->redirect('/admin/votings');
    }

    /**
     * Show candidates management page
     */
    public function candidates(string $id): void
    {
        $this->requireAuth();

        $voting = $this->votingModel->find((int) $id);

        if (!$voting) {
            $this->flash('error', 'Votación no encontrada.');
            $this->redirect('/admin/votings');
            return;
        }

        $candidates = $this->votingModel->getCandidates((int) $id);
        $totalVotes = $this->votingModel->getTotalVotes((int) $id);

        $this->renderAdmin('votings/candidates', [
            'title' => 'Candidatos: ' . $voting['title'],
            'voting' => $voting,
            'candidates' => $candidates,
            'totalVotes' => $totalVotes,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Add candidate
     */
    public function addCandidate(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $voting = $this->votingModel->find((int) $id);

        if (!$voting) {
            $this->jsonError('Votación no encontrada.');
            return;
        }

        $name = Sanitizer::string($this->getPost('name'));
        $description = $this->getPost('description');
        $logoUrl = Sanitizer::url($this->getPost('logo_url'));
        $websiteUrl = Sanitizer::url($this->getPost('website_url'));
        $baseVotes = (int) $this->getPost('base_votes', 0);
        $displayOrder = (int) $this->getPost('display_order', 0);
        $active = Sanitizer::bool($this->getPost('active', true)) ? 1 : 0;

        if (empty($name)) {
            $this->jsonError('El nombre es obligatorio.');
            return;
        }

        try {
            $candidateId = $this->votingModel->addCandidate((int) $id, [
                'name' => $name,
                'description' => $description,
                'logo_url' => $logoUrl,
                'website_url' => $websiteUrl,
                'base_votes' => $baseVotes,
                'display_order' => $displayOrder,
                'active' => $active,
            ]);

            $this->jsonSuccess(['id' => $candidateId, 'message' => 'Candidato añadido.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Update candidate
     */
    public function updateCandidate(string $id, string $candidateId): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $candidate = $this->votingModel->getCandidate((int) $candidateId);

        if (!$candidate || $candidate['voting_id'] != (int) $id) {
            $this->jsonError('Candidato no encontrado.');
            return;
        }

        $data = [
            'name' => Sanitizer::string($this->getPost('name')),
            'description' => $this->getPost('description'),
            'logo_url' => Sanitizer::url($this->getPost('logo_url')),
            'website_url' => Sanitizer::url($this->getPost('website_url')),
            'base_votes' => (int) $this->getPost('base_votes', 0),
            'display_order' => (int) $this->getPost('display_order', 0),
            'active' => Sanitizer::bool($this->getPost('active')) ? 1 : 0,
        ];

        try {
            $this->votingModel->updateCandidate((int) $candidateId, $data);
            $this->jsonSuccess(['message' => 'Candidato actualizado.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete candidate
     */
    public function deleteCandidate(string $id, string $candidateId): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $candidate = $this->votingModel->getCandidate((int) $candidateId);

        if (!$candidate || $candidate['voting_id'] != (int) $id) {
            $this->jsonError('Candidato no encontrado.');
            return;
        }

        try {
            $this->votingModel->deleteCandidate((int) $candidateId);
            $this->jsonSuccess(['message' => 'Candidato eliminado.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Results page
     */
    public function results(string $id): void
    {
        $this->requireAuth();

        $voting = $this->votingModel->find((int) $id);

        if (!$voting) {
            $this->flash('error', 'Votación no encontrada.');
            $this->redirect('/admin/votings');
        }

        $results = $this->votingModel->getResults((int) $id);

        $this->renderAdmin('votings/results', [
            'title' => 'Resultados: ' . $voting['title'],
            'voting' => $voting,
            'candidates' => $results['candidates'],
            'totalVotes' => $results['total_votes'],
            'statusOptions' => Voting::getStatusOptions(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Reset votes
     */
    public function resetVotes(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        try {
            // Reset all candidate votes to 0
            $candidates = $this->votingModel->getCandidates((int) $id);
            foreach ($candidates as $candidate) {
                $this->votingModel->db->execute(
                    "UPDATE voting_candidates SET votes = 0 WHERE id = ?",
                    [$candidate['id']]
                );
            }

            // Delete vote records
            $this->votingModel->db->execute(
                "DELETE FROM votes WHERE voting_id = ?",
                [(int) $id]
            );

            $this->jsonSuccess(['message' => 'Votos reiniciados.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Validate voting form data
     */
    private function validateVotingData(): array
    {
        $errors = [];

        $title = Sanitizer::string($this->getPost('title'));
        $description = $this->getPost('description');
        $eventId = $this->getPost('event_id') ? (int) $this->getPost('event_id') : null;
        $status = $this->getPost('status', 'draft');
        $showVoteCounts = Sanitizer::bool($this->getPost('show_vote_counts'));
        $showRanking = Sanitizer::bool($this->getPost('show_ranking'));
        $allowMultipleVotes = Sanitizer::bool($this->getPost('allow_multiple_votes'));
        $votingStart = $this->getPost('voting_start');
        $votingEnd = $this->getPost('voting_end');
        $featuredImage = Sanitizer::url($this->getPost('featured_image'));

        if (empty($title)) {
            $errors[] = 'El título es obligatorio.';
        }

        if (!in_array($status, array_keys(Voting::getStatusOptions()))) {
            $errors[] = 'Estado no válido.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'title' => $title,
            'description' => $description ?: null,
            'event_id' => $eventId,
            'status' => $status,
            'show_vote_counts' => $showVoteCounts ? 1 : 0,
            'show_ranking' => $showRanking ? 1 : 0,
            'allow_multiple_votes' => $allowMultipleVotes ? 1 : 0,
            'voting_start' => $votingStart ?: null,
            'voting_end' => $votingEnd ?: null,
            'featured_image' => $featuredImage ?: null,
        ];
    }
}
