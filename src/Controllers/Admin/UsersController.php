<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Helpers\Sanitizer;

/**
 * Users Controller
 * Omniwallet CMS
 */
class UsersController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * List all users
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page') ?? 1);
        $search = Sanitizer::string($this->getQuery('search') ?? '');

        $conditions = [];
        if ($search) {
            $conditions['search'] = $search;
        }

        // Get users with pagination
        $result = $this->userModel->paginate($page, 20, $conditions, ['created_at' => 'DESC']);

        $this->renderAdmin('users/index', [
            'title' => 'Usuarios',
            'users' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->renderAdmin('users/form', [
            'title' => 'Nuevo Usuario',
            'user' => null,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Store new user
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirect('/admin/users/create');
        }

        $data = [
            'name' => Sanitizer::string($this->getPost('name') ?? ''),
            'email' => Sanitizer::email($this->getPost('email') ?? ''),
            'password' => $this->getPost('password') ?? '',
            'is_active' => Sanitizer::bool($this->getPost('is_active')),
        ];

        // Validate
        $errors = $this->validate($data);
        if (!empty($errors)) {
            $this->flash('error', implode('<br>', $errors));
            $this->redirect('/admin/users/create');
        }

        // Check email uniqueness
        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing) {
            $this->flash('error', 'Ya existe un usuario con ese email.');
            $this->redirect('/admin/users/create');
        }

        try {
            $this->userModel->createUser($data);
            $this->flash('success', 'Usuario creado correctamente.');
            $this->redirect('/admin/users');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el usuario: ' . $e->getMessage());
            $this->redirect('/admin/users/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $this->requireAuth();

        $user = $this->userModel->find($id);

        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
        }

        $this->renderAdmin('users/form', [
            'title' => 'Editar Usuario',
            'user' => $user,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Update user
     */
    public function update(int $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirect('/admin/users/' . $id . '/edit');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
        }

        $data = [
            'name' => Sanitizer::string($this->getPost('name') ?? ''),
            'email' => Sanitizer::email($this->getPost('email') ?? ''),
            'is_active' => Sanitizer::bool($this->getPost('is_active')),
        ];

        $password = $this->getPost('password') ?? '';

        // Validate (password optional on update)
        $errors = $this->validate($data, false);
        if (!empty($errors)) {
            $this->flash('error', implode('<br>', $errors));
            $this->redirect('/admin/users/' . $id . '/edit');
        }

        // Check email uniqueness (excluding current user)
        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('error', 'Ya existe otro usuario con ese email.');
            $this->redirect('/admin/users/' . $id . '/edit');
        }

        try {
            $this->userModel->update($id, $data);

            // Update password if provided
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $this->flash('error', 'La contraseña debe tener al menos 6 caracteres.');
                    $this->redirect('/admin/users/' . $id . '/edit');
                }
                $this->userModel->updatePassword($id, $password);
            }

            $this->flash('success', 'Usuario actualizado correctamente.');
            $this->redirect('/admin/users');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el usuario: ' . $e->getMessage());
            $this->redirect('/admin/users/' . $id . '/edit');
        }
    }

    /**
     * Delete user
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/users');
        }

        // Prevent self-deletion
        if ($id == ($_SESSION['user_id'] ?? 0)) {
            $this->flash('error', 'No puedes eliminar tu propio usuario.');
            $this->redirect('/admin/users');
        }

        // Check if this is the last admin
        $admins = $this->userModel->getByRole('admin');
        if (count($admins) <= 1 && $user['role'] === 'admin') {
            $this->flash('error', 'No puedes eliminar el último administrador.');
            $this->redirect('/admin/users');
        }

        try {
            $this->userModel->delete($id);
            $this->flash('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el usuario.');
        }

        $this->redirect('/admin/users');
    }

    /**
     * Validate user data
     */
    private function validate(array $data, bool $requirePassword = true): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'El nombre es obligatorio.';
        }

        if (empty($data['email'])) {
            $errors[] = 'El email es obligatorio.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido.';
        }

        if ($requirePassword) {
            if (empty($data['password'])) {
                $errors[] = 'La contraseña es obligatoria.';
            } elseif (strlen($data['password']) < 6) {
                $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
            }
        }

        return $errors;
    }
}
