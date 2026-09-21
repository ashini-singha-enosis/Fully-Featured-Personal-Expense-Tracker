<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Helper;
use App\Repository\UserRepository;

class AuthController
{
    /** @param array<string, mixed> $config */
    public function __construct(
        private array $config,
        private UserRepository $userRepository,
    ) {
    }

    public function login(): void
    {
        $errors = [];
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if ($username === '' || $password === '') {
                $errors[] = 'Enter both username and password.';
            } else {
                $user = $this->userRepository->authenticate($username, $password);
                if ($user === null) {
                    $errors[] = 'Invalid username or password.';
                } else {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    header('Location: ' . Helper::url('expenses'));
                    exit;
                }
            }
        }

        $this->render('auth/login.php', [
            'pageTitle' => 'Login',
            'currentPage' => 'login',
            'hideNav' => true,
            'errors' => $errors,
            'username' => $username,
        ]);
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
        header('Location: ' . Helper::url('login'));
        exit;
    }

    /**
     * @param array<string, mixed> $vars
     */
    private function render(string $view, array $vars): void
    {
        $appName = (string) $this->config['app_name'];
        $viewContent = (string) $this->config['paths']['views'] . '/' . $view;
        extract($vars, EXTR_SKIP);
        require (string) $this->config['paths']['views'] . '/layout.php';
    }
}
