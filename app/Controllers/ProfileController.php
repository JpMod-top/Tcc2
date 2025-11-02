<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\User;

class ProfileController
{
    public function index(): void
    {
        Auth::requireAuth();
        $user = Auth::user();
        if ($user === null) {
            $this->redirect('/login');
        }

        View::render('profile/index', [
            'title' => 'Perfil',
            'user' => $user,
            'csrfUpdate' => Csrf::token('profile_update'),
            'csrfPassword' => Csrf::token('profile_password'),
            'csrfDelete' => Csrf::token('profile_delete'),
        ]);
    }

    public function update(): void
    {
        Auth::requireAuth();
        if (!$this->verifyCsrf('profile_update', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessão expirada.');
            $this->redirect('/profile');
        }

        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Informe seu nome.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um e-mail válido.';
        }

        $current = User::findById($userId);
        if ($current === null) {
            $this->redirect('/login');
        }

        if ($email !== $current['email']) {
            $existing = User::findByEmail($email);
            if ($existing !== null && (int)$existing['id'] !== $userId) {
                $errors[] = 'E-mail já está em uso.';
            }
        }

        if ($errors !== []) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect('/profile');
        }

        User::updateProfile($userId, $name, $email);

        AuditLog::record(
            $userId,
            'users',
            $userId,
            'update_profile',
            [
                'name' => ['old' => $current['name'], 'new' => $name],
                'email' => ['old' => $current['email'], 'new' => $email],
            ],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        Auth::login($userId, [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
        ]);

        $this->flash('success', 'Perfil atualizado.');
        $this->redirect('/profile');
    }

    public function updatePassword(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('profile_password', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessão expirada.');
            $this->redirect('/profile');
        }

        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['password_confirmation'] ?? '');

        $user = User::findById($userId);
        if ($user === null) {
            $this->redirect('/login');
        }

        if (!password_verify($currentPassword, $user['password_hash'])) {
            $this->flash('error', 'Senha atual incorreta.');
            $this->redirect('/profile');
        }

        if ($newPassword !== $confirmPassword) {
            $this->flash('error', 'Confirmação da senha não confere.');
            $this->redirect('/profile');
        }

        if (!$this->validatePasswordStrength($newPassword)) {
            $this->flash('error', 'Senha deve conter letras maiúsculas, minúsculas e números, com no mínimo 8 caracteres.');
            $this->redirect('/profile');
        }

        $hash = password_hash($newPassword, PASSWORD_ARGON2ID);
        User::updatePassword($userId, $hash);

        AuditLog::record(
            $userId,
            'users',
            $userId,
            'update_password',
            ['updated' => true],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $this->flash('success', 'Senha atualizada com sucesso.');
        $this->redirect('/profile');
    }

    public function delete(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('profile_delete', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessão expirada.');
            $this->redirect('/profile');
        }

        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        User::softDelete($userId);

        AuditLog::record(
            $userId,
            'users',
            $userId,
            'soft_delete',
            ['user_id' => $userId],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        Auth::logout();
        $this->flash('success', 'Conta removida. Sentiremos sua falta!');
        $this->redirect('/login');
    }

    private function verifyCsrf(string $key, ?string $token): bool
    {
        return Csrf::verify($key, $token);
    }

    private function validatePasswordStrength(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        return preg_match('/[A-Z]/', $password) === 1
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/\d/', $password) === 1;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location, true, 302);
        exit;
    }
}
