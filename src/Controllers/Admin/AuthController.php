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
        // DEBUG: Handle POST here directly to test
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<h2>POST recibido en loginForm!</h2>";
            echo "<pre>" . print_r($_POST, true) . "</pre>";
            $this->login();
            return;
        }

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
        // DEBUG - Show what's happening
        echo "<h2>DEBUG LOGIN</h2>";
        echo "<pre>";
        echo "Email recibido: " . htmlspecialchars($_POST['email'] ?? 'NO EMAIL') . "\n";
        echo "Password recibido: " . (empty($_POST['password']) ? 'VACÍO' : 'TIENE VALOR') . "\n";

        $email = Sanitizer::email($this->getPost('email'));
        $password = $this->getPost('password', '');

        echo "Email sanitizado: " . htmlspecialchars($email) . "\n";

        // Find user
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            echo "Usuario NO encontrado en base de datos\n";
            echo "</pre>";
            exit;
        }

        echo "Usuario encontrado: " . htmlspecialchars($user['name']) . "\n";
        echo "is_active: " . ($user['is_active'] ?? 'NULL') . "\n";
        echo "Hash en DB: " . htmlspecialchars(substr($user['password'], 0, 20)) . "...\n";

        // Verify password
        $passwordOk = $this->userModel->verifyPassword($password, $user['password']);
        echo "Password verificado: " . ($passwordOk ? 'SÍ' : 'NO') . "\n";
        echo "</pre>";

        if (!$passwordOk) {
            echo "<p style='color:red'>La contraseña no coincide</p>";
            exit;
        }

        echo "<p style='color:green'>TODO OK - Redirigiendo al dashboard...</p>";

        // Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

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
