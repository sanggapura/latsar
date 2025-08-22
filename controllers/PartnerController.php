<?php
require_once __DIR__ . "/../models/Partner.php";

class PartnerController {
    private $partner;

    public function __construct($db) {
        $this->partner = new Partner($db);
    }

    public function index() {
        $stmt = $this->partner->getAll();
        include __DIR__ . "/../views/partner/list.php";
    }

    public function createForm() {
        include __DIR__ . "/../views/partner/create.php";
    }

    public function store($data) {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if ($name === '' || $email === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nama dan email wajib diisi'];
            header("Location: index.php?action=create");
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Format email tidak valid'];
            header("Location: index.php?action=create");
            exit;
        }

        $this->partner->name = $name;
        $this->partner->email = $email;
        $this->partner->phone = $phone;

        if ($this->partner->create()) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Partner berhasil ditambahkan'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menambahkan partner'];
        }
        header("Location: index.php?action=partners");
        exit;
    }

    public function editForm($id) {
        $partner = $this->partner->getById($id);
        if (!$partner) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Partner tidak ditemukan'];
            header('Location: index.php?action=partners');
            exit;
        }
        include __DIR__ . "/../views/partner/edit.php";
    }

    public function update($data) {
        $id = (int)($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if ($id <= 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'ID tidak valid'];
            header('Location: index.php?action=partners');
            exit;
        }
        if ($name === '' || $email === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nama dan email wajib diisi'];
            header("Location: index.php?action=edit&id=" . $id);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Format email tidak valid'];
            header("Location: index.php?action=edit&id=" . $id);
            exit;
        }

        $this->partner->id = $id;
        $this->partner->name = $name;
        $this->partner->email = $email;
        $this->partner->phone = $phone;

        if ($this->partner->update()) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Partner berhasil diperbarui'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal memperbarui partner'];
        }
        header("Location: index.php?action=partners");
        exit;
    }

    public function delete($id) {
        $this->partner->id = (int)$id;
        if ($this->partner->delete()) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Partner berhasil dihapus'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menghapus partner'];
        }
        header("Location: index.php?action=partners");
        exit;
    }
}
