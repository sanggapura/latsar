<?php
require_once __DIR__ . "/../models/User.php";

class AuthController {
    private $user;

    public function __construct($db) {
        // Hapus session_start() karena sudah dipanggil di file utama
        $this->user = new User($db);
    }

    public function loginForm() {
        include __DIR__ . "/../views/auth/login.php";
    }

    public function registerForm() {
        include __DIR__ . "/../views/auth/register.php";
    }

    public function login($data) {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        if ($email === '' || $password === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email dan password wajib diisi'];
            header("Location: auth.php?action=login_form");
            exit;
        }

        $user = $this->user->login($email);
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Berhasil login'];
            header("Location: index.php");
            exit;
        }

        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email atau password salah'];
        header("Location: auth.php?action=login_form");
        exit;
    }

    public function register($data) {
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Semua field wajib diisi'];
            header("Location: auth.php?action=register_form");
            exit;
        }

        $this->user->username = $username;
        $this->user->email = $email;
        $this->user->password = password_hash($password, PASSWORD_BCRYPT);

        if ($this->user->register()) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Registrasi berhasil, silakan login'];
            header("Location: auth.php?action=login_form");
            exit;
        }

        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Registrasi gagal'];
        header("Location: auth.php?action=register_form");
        exit;
    }

    public function logout() {
        session_destroy();
        header("Location: auth.php?action=login_form");
        exit;
    }
}