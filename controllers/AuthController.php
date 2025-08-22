<?php
require_once __DIR__ . "/../models/User.php";

class AuthController {
    private $user;

    public function __construct($db) {
        session_start();
        $this->user = new User($db);
    }

    public function loginForm() {
        include __DIR__ . "/../views/auth/login.php";
    }

    public function registerForm() {
        include __DIR__ . "/../views/auth/register.php";
    }

    public function login($data) {
        $user = $this->user->login($data['email']);
        if ($user && password_verify($data['password'], $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: index.php");
        } else {
            echo "<script>alert('Email atau password salah!'); window.location='auth.php?action=login';</script>";
        }
    }

    public function register($data) {
        $this->user->username = $data['username'];
        $this->user->email = $data['email'];
        $this->user->password = password_hash($data['password'], PASSWORD_BCRYPT);

        if ($this->user->register()) {
            echo "<script>alert('Registrasi berhasil, silakan login!'); window.location='auth.php?action=login';</script>";
        } else {
            echo "<script>alert('Registrasi gagal!'); window.location='auth.php?action=register';</script>";
        }
    }

    public function logout() {
        session_destroy();
        header("Location: auth.php?action=login");
    }
}
