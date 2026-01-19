<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\TeamMember;
use App\Helpers\Sanitizer;

/**
 * Team Controller
 * Omniwallet CMS
 */
class TeamController extends Controller
{
    private TeamMember $teamModel;

    public function __construct()
    {
        parent::__construct();
        $this->teamModel = new TeamMember();
    }

    /**
     * List all team members
     */
    public function index(): void
    {
        $this->requireAuth();

        $members = $this->teamModel->getAllOrdered();

        $this->renderAdmin('team/index', [
            'title' => 'Equipo',
            'members' => $members,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->renderAdmin('team/form', [
            'title' => 'Nuevo Miembro del Equipo',
            'member' => null,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new team member
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/team/create');
        }

        $data = $this->validateMemberData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/team/create');
        }

        try {
            $this->teamModel->createWithSlug($data);
            $this->flash('success', 'Miembro del equipo creado correctamente.');
            $this->redirect('/admin/team');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el miembro: ' . $e->getMessage());
            $this->redirect('/admin/team/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $member = $this->teamModel->find((int) $id);

        if (!$member) {
            $this->flash('error', 'Miembro no encontrado.');
            $this->redirect('/admin/team');
        }

        $this->renderAdmin('team/form', [
            'title' => 'Editar Miembro del Equipo',
            'member' => $member,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update team member
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/team/' . $id . '/edit');
        }

        $member = $this->teamModel->find((int) $id);

        if (!$member) {
            $this->flash('error', 'Miembro no encontrado.');
            $this->redirect('/admin/team');
        }

        $data = $this->validateMemberData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/team/' . $id . '/edit');
        }

        try {
            $this->teamModel->updateWithSlug((int) $id, $data);
            $this->flash('success', 'Miembro actualizado correctamente.');
            $this->redirect('/admin/team');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar: ' . $e->getMessage());
            $this->redirect('/admin/team/' . $id . '/edit');
        }
    }

    /**
     * Delete team member
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/team');
        }

        try {
            $this->teamModel->delete((int) $id);
            $this->flash('success', 'Miembro eliminado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar: ' . $e->getMessage());
        }

        $this->redirect('/admin/team');
    }

    /**
     * Validate team member form data
     */
    private function validateMemberData(): array
    {
        $errors = [];

        $name = Sanitizer::string($this->getPost('name'));
        $slug = Sanitizer::string($this->getPost('slug'));
        $position = Sanitizer::string($this->getPost('position') ?? $this->getPost('role'));
        $description = $this->getPost('description') ?? $this->getPost('bio');
        $photo = Sanitizer::url($this->getPost('photo'));
        $photoAnimated = Sanitizer::url($this->getPost('photo_animated'));
        $email = Sanitizer::email($this->getPost('email'));
        $linkedinUrl = Sanitizer::url($this->getPost('linkedin_url') ?? $this->getPost('linkedin'));
        $twitter = Sanitizer::url($this->getPost('twitter'));
        $sortOrder = (int) $this->getPost('sort_order', 0);
        $isActive = $this->getPost('is_active') !== null ? 1 : 1; // Default to active

        if (empty($name)) {
            $errors[] = 'El nombre es obligatorio.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'name' => $name,
            'slug' => $slug ?: null,
            'position' => $position ?: null,
            'bio' => $description ?: null,
            'photo' => $photo ?: null,
            'photo_animated' => $photoAnimated ?: null,
            'email' => $email ?: null,
            'linkedin_url' => $linkedinUrl ?: null,
            'twitter_url' => $twitter ?: null,
            'sort_order' => $sortOrder,
            'active' => $isActive,
        ];
    }
}
