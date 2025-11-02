<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\DB;
use App\Core\View;
use App\Models\User;
use DateInterval;
use DateTimeImmutable;
use PDO;

class AuthController
{
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_LOCK_MINUTES = 10;
    private const PASSWORD_MIN_LENGTH = 8;

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        View::render('auth/login', [
            'title' => 'Entrar',
            'csrfToken' => Csrf::token('auth_login'),
            'old' => $this->pullOldInput(),
        ], 'layouts/auth_base');
    }

    public function login(): void
    {
        if (!$this->validateCsrf('auth_login', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessao expirada. Tente novamente.');
            $this->redirect('/login');
        }

        $email = $this->normalizeEmail($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $ip = $this->clientIp();

        if ($this->isLockedOut($email, $ip)) {
            $this->flash('error', 'Muitas tentativas de login. Aguarde alguns minutos.');
            $this->rememberInput(['email' => $email]);
            $this->redirect('/login');
        }

        $user = $email !== '' ? User::findByEmail($email) : null;

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->recordFailedLogin($email, $ip);
            $this->flash('error', 'Credenciais invalidas.');
            $this->rememberInput(['email' => $email]);
            $this->redirect('/login');
        }

        if (!empty($user['deleted_at'])) {
            $this->flash('error', 'Conta desativada.');
            $this->rememberInput(['email' => $email]);
            $this->redirect('/login');
        }

        $this->clearLoginAttempts($email, $ip);

        Auth::login((int)$user['id'], [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ]);

        $this->flash('success', 'Login realizado com sucesso.');
        $this->clearOldInput();
        $this->redirect('/dashboard');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        View::render('auth/register', [
            'title' => 'Criar conta',
            'csrfToken' => Csrf::token('auth_register'),
            'old' => $this->pullOldInput(),
        ], 'layouts/auth_base');
    }

    public function register(): void
    {
        if (!$this->validateCsrf('auth_register', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessao expirada. Reenvie o formulario.');
            $this->redirect('/register');
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $email = $this->normalizeEmail($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $passwordConfirmation = (string)($_POST['password_confirmation'] ?? '');

        // remember minimal input to repopulate on error
        $this->rememberInput([
            'name' => $name,
            'email' => $email,
        ]);

        $errors = $this->validateRegistration($name, $email, $password, $passwordConfirmation);

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect('/register');
        }

        // enforce reasonable maximums
        if (mb_strlen($name) > 191) {
            $this->flash('error', 'Nome muito longo.');
            $this->redirect('/register');
        }

        if (mb_strlen($email) > 191) {
            $this->flash('error', 'Email muito longo.');
            $this->redirect('/register');
        }

        // normalize
        $name = trim($name);
        $email = $this->normalizeEmail($email);

        // hash password (ARGON2ID preferred, fallback to BCRYPT if not available)
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
        $passwordHash = password_hash($password, $algo);

        try {
            $userId = DB::transaction(function () use ($name, $email, $passwordHash) {
                // double-check uniqueness inside transaction to reduce race conditions
                if (User::existsWithEmail($email)) {
                    throw new \RuntimeException('duplicate_email');
                }

                return User::create([
                    'name' => $name,
                    'email' => $email,
                    'password_hash' => $passwordHash,
                ]);
            });
        } catch (\Throwable $e) {
            // handle duplicate email gracefully
            if ($e instanceof \RuntimeException && $e->getMessage() === 'duplicate_email') {
                $this->flash('error', 'Email ja registrado.');
                $this->redirect('/register');
            }

            // detect common SQL unique constraint errors across drivers
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'duplicate') || str_contains($msg, 'unique') || str_contains($msg, '1062') || str_contains($msg, 'constraint')) {
                $this->flash('error', 'Email ja registrado.');
                $this->redirect('/register');
            }

            // otherwise rethrow so the global handler can log it
            throw $e;
        }

        Auth::login($userId, [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
        ]);

        $this->flash('success', 'Conta criada com sucesso.');
        $this->clearOldInput();
        $this->redirect('/dashboard');
    }

    public function showForgot(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        View::render('auth/forgot', [
            'title' => 'Recuperar senha',
            'csrfToken' => Csrf::token('auth_forgot'),
            'old' => $this->pullOldInput(),
        ], 'layouts/auth_base');
    }

    public function forgot(): void
    {
        if (!$this->validateCsrf('auth_forgot', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessao expirada. Tente novamente.');
            $this->redirect('/forgot');
        }

        $email = $this->normalizeEmail($_POST['email'] ?? '');
        $this->rememberInput(['email' => $email]);

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Informe um email valido.');
            $this->redirect('/forgot');
        }

        $user = User::findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = (new DateTimeImmutable('now'))->add(new DateInterval('PT1H'));
            User::createPasswordResetToken(
                (int)$user['id'],
                $email,
                $token,
                $expiresAt,
                $this->clientIp(),
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );

            if ($this->isDebugEnvironment()) {
                $this->flash('info', 'Token de recuperacao (dev): ' . $token);
            }

            $this->flash('success', 'Se o email estiver cadastrado, enviaremos instrucoes em instantes.');
        } else {
            $this->flash('success', 'Se o email estiver cadastrado, enviaremos instrucoes em instantes.');
        }

        $this->redirect('/forgot');
    }

    public function showReset(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $token = (string)($_GET['token'] ?? '');
        $email = $this->normalizeEmail($_GET['email'] ?? '');

        View::render('auth/reset', [
            'title' => 'Redefinir senha',
            'csrfToken' => Csrf::token('auth_reset'),
            'token' => $token,
            'email' => $email,
            'old' => $this->pullOldInput(),
        ], 'layouts/auth_base');
    }

    public function reset(): void
    {
        if (!$this->validateCsrf('auth_reset', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessao expirada. Tente novamente.');
            $this->redirect('/reset');
        }

        $token = (string)($_POST['token'] ?? '');
        $email = $this->normalizeEmail($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $passwordConfirmation = (string)($_POST['password_confirmation'] ?? '');

        $this->rememberInput([
            'email' => $email,
        ]);

        if ($token === '' || $email === '') {
            $this->flash('error', 'Link de redefinicao invalido.');
            $this->redirect('/reset');
        }

        $errors = $this->validatePasswordReset($password, $passwordConfirmation);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect('/reset?token=' . urlencode($token) . '&email=' . urlencode($email));
        }

        $resetEntry = User::findValidPasswordReset($email, $token);
        if ($resetEntry === null) {
            $this->flash('error', 'Token invalido ou expirado.');
            $this->redirect('/reset');
        }

        $user = User::findByEmail($email);
        if ($user === null) {
            $this->flash('error', 'Usuario nao encontrado.');
            $this->redirect('/reset');
        }

        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
        User::updatePassword((int)$user['id'], $passwordHash);
        User::markPasswordResetUsed((int)$resetEntry['id']);

        Auth::login((int)$user['id'], [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ]);

        $this->flash('success', 'Senha redefinida com sucesso.');
        $this->clearOldInput();
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        if (!$this->validateCsrf('auth_logout', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessao invalida.');
            $this->redirect('/dashboard');
        }

        Auth::logout();
        $this->flash('success', 'Voce saiu da conta.');
        $this->redirect('/login');
    }

    /**
     * @param array<int, string> $errors
     */
    private function validateRegistration(string $name, string $email, string $password, string $confirmation): array
    {
        $errors = [];

        if ($name === '') {
            $errors[] = 'Informe seu nome completo.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um email valido.';
        }

        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = 'A senha deve ter pelo menos ' . self::PASSWORD_MIN_LENGTH . ' caracteres.';
        }

        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) {
            $errors[] = 'A senha deve ter letras maiusculas, minusculas e numeros.';
        }

        if (!hash_equals($password, $confirmation)) {
            $errors[] = 'A confirmacao da senha nao confere.';
        }

        return $errors;
    }

    /**
     * @return array<int, string>
     */
    private function validatePasswordReset(string $password, string $confirmation): array
    {
        return $this->validateRegistration('ignore', 'ignore@example.com', $password, $confirmation);
    }

    private function validateCsrf(string $key, ?string $token): bool
    {
        return Csrf::verify($key, $token);
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function clientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location, true, 302);
        exit;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    private function rememberInput(array $input): void
    {
        $_SESSION['_old_input'] = $input;
    }

    /**
     * @return array<string, mixed>
     */
    private function pullOldInput(): array
    {
        $old = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']);

        return is_array($old) ? $old : [];
    }

    private function clearOldInput(): void
    {
        unset($_SESSION['_old_input']);
    }

    private function isDebugEnvironment(): bool
    {
        if (isset($_ENV['APP_DEBUG']) && filter_var($_ENV['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }

        return ($_ENV['APP_ENV'] ?? 'production') !== 'production';
    }

    private function isLockedOut(string $email, string $ip): bool
    {
        if ($email === '') {
            return false;
        }

        $row = DB::run(
            'SELECT attempts, locked_until FROM login_attempts WHERE email = :email AND ip = :ip LIMIT 1',
            ['email' => $email, 'ip' => $ip]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        if (!empty($row['locked_until']) && strtotime((string)$row['locked_until']) > time()) {
            return true;
        }

        return false;
    }

    private function recordFailedLogin(string $email, string $ip): void
    {
        if ($email === '') {
            return;
        }

        $now = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        $row = DB::run(
            'SELECT id, attempts, locked_until, last_attempt_at FROM login_attempts WHERE email = :email AND ip = :ip LIMIT 1',
            ['email' => $email, 'ip' => $ip]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            DB::run(
                'INSERT INTO login_attempts (email, ip, attempts, locked_until, last_attempt_at, created_at, updated_at)
                 VALUES (:email, :ip, :attempts, NULL, :last_attempt, :created_at, :updated_at)',
                [
                    'email' => $email,
                    'ip' => $ip,
                    'attempts' => 1,
                    'last_attempt' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            return;
        }

        if (!empty($row['locked_until']) && strtotime((string)$row['locked_until']) > time()) {
            return;
        }

        $attempts = (int)$row['attempts'] + 1;
        $lockedUntil = null;

        if ($attempts >= self::LOGIN_MAX_ATTEMPTS) {
            $lockedUntil = (new DateTimeImmutable('now'))
                ->add(new DateInterval('PT' . self::LOGIN_LOCK_MINUTES . 'M'))
                ->format('Y-m-d H:i:s');
            $attempts = 0;
        }

        DB::run(
            'UPDATE login_attempts
             SET attempts = :attempts, locked_until = :locked_until, last_attempt_at = :last_attempt, updated_at = :updated_at
             WHERE email = :email AND ip = :ip',
            [
                'attempts' => $attempts,
                'locked_until' => $lockedUntil,
                'last_attempt' => $now,
                'updated_at' => $now,
                'email' => $email,
                'ip' => $ip,
            ]
        );
    }

    private function clearLoginAttempts(string $email, string $ip): void
    {
        DB::run(
            'DELETE FROM login_attempts WHERE email = :email AND ip = :ip',
            ['email' => $email, 'ip' => $ip]
        );
    }
}
