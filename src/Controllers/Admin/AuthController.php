<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Helpers\Sanitizer;

/**
 * Auth Controller
 * Omniwallet CMS
 */
class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Show login form
     */
    public function loginForm(): void
    {
        // Redirect if already logged in
        if ($this->isAuthenticated()) {
            $this->redirect('/admin/dashboard');
        }

        $this->render('admin/auth/login', [
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Process login
     */
    public function login(): void
    {
        // Debug: Log login attempt
        error_log("Login attempt - Session ID: " . session_id());
        error_log("CSRF in session: " . ($_SESSION['csrf_token'] ?? 'NOT SET'));
        error_log("CSRF from POST: " . ($_POST['_csrf_token'] ?? 'NOT SET'));

        // Validate CSRF
        if (!$this->validateCsrf()) {
            error_log("CSRF validation FAILED");
            $this->flash('error', 'Sesión expirada. Por favor, inténtalo de nuevo.');
            $this->redirect('/admin/login');
            return;
        }

        error_log("CSRF validation PASSED");

        $email = Sanitizer::email($this->getPost('email'));
        $password = $this->getPost('password', '');
        $remember = Sanitizer::bool($this->getPost('remember'));

        // Validate inputs
        if (empty($email) || empty($password)) {
            $this->flash('error', 'Por favor, introduce tu email y contraseña.');
            $this->redirect('/admin/login');
        }

        // Find user
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->flash('error', 'Credenciales incorrectas.');
            $this->redirect('/admin/login');
        }

        // Check if user is active
        if (!$user['is_active']) {
            $this->flash('error', 'Tu cuenta está desactivada. Contacta con el administrador.');
            $this->redirect('/admin/login');
        }

        // Verify password
        error_log("Verifying password for user: " . $user['email']);
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            error_log("Password verification FAILED");
            $this->flash('error', 'Credenciales incorrectas.');
            $this->redirect('/admin/login');
            return;
        }
        error_log("Password verification PASSED");

        // Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Handle remember me (optional - would need cookie implementation)
        if ($remember) {
            // Could implement persistent login cookie here
        }

        // Regenerate session ID for security
        session_regenerate_id(true);

        $this->flash('success', 'Bienvenido, ' . $user['name']);
        $this->redirect('/admin/dashboard');
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        // Clear session
        $_SESSION = [];

        // Destroy session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        // Start new session for flash message
        session_start();
        $this->flash('success', 'Has cerrado sesión correctamente.');
        $this->redirect('/admin/login');
    }
}
