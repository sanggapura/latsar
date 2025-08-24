<?php
require_once __DIR__ . "/../models/Kontak.php";

class KontakController {
    private $kontak;

    public function __construct($db) {
        $this->kontak = new Kontak($db);
    }

    public function index() {
        $stmt = $this->kontak->getAll();
        include __DIR__ . "/../views/kontak/list.php";
    }

    public function createForm() {
        include __DIR__ . "/../views/kontak/create.php";
    }

    public function store($data) {
        $nama_perusahaan = trim($data['nama_perusahaan'] ?? '');
        $nama_pic = trim($data['nama_pic'] ?? '');
        $nomor_telp = trim($data['nomor_telp'] ?? '');
        $alamat_email = trim($data['alamat_email'] ?? '');

        if ($nama_perusahaan === '' || $nama_pic === '' || $alamat_email === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nama perusahaan, nama PIC, dan email wajib diisi'];
            header("Location: index.php?action=create_kontak");
            exit;
        }
        if (!filter_var($alamat_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Format email tidak valid'];
            header("Location: index.php?action=create_kontak");
            exit;
        }

        $this->kontak->nama_perusahaan = $nama_perusahaan;
        $this->kontak->nama_pic = $nama_pic;
        $this->kontak->nomor_telp = $nomor_telp;
        $this->kontak->alamat_email = $alamat_email;

        if ($this->kontak->create()) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kontak mitra berhasil ditambahkan'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menambahkan kontak mitra'];
        }
        header("Location: index.php?action=contacts");
        exit;
    }

    public function editForm($id) {
        $kontak = $this->kontak->getById($id);
        if (!$kontak) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Kontak tidak ditemukan'];
            header('Location: index.php?action=contacts');
            exit;
        }
        include __DIR__ . "/../views/kontak/edit.php";
    }

    public function update($data) {
        $id = (int)($data['id'] ?? 0);
        $nama_perusahaan = trim($data['nama_perusahaan'] ?? '');
        $nama_pic = trim($data['nama_pic'] ?? '');
        $nomor_telp = trim($data['nomor_telp'] ?? '');
        $alamat_email = trim($data['alamat_email'] ?? '');

        if ($id <= 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'ID tidak valid'];
            header('Location: index.php?action=contacts');
            exit;
        }
        if ($nama_perusahaan === '' || $nama_pic === '' || $alamat_email === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nama perusahaan, nama PIC, dan email wajib diisi'];
            header("Location: index.php?action=edit_kontak&id=" . $id);
            exit;
        }
        if (!filter_var($alamat_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Format email tidak valid'];
            header("Location: index.php?action=edit_kontak&id=" . $id);
            exit;
        }

        $this->kontak->id = $id;
        $this->kontak->nama_perusahaan = $nama_perusahaan;
        $this->kontak->nama_pic = $nama_pic;
        $this->kontak->nomor_telp = $nomor_telp;
        $this->kontak->alamat_email = $alamat_email;

        if ($this->kontak->update()) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kontak mitra berhasil diperbarui'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal memperbarui kontak mitra'];
        }
        header("Location: index.php?action=contacts");
        exit;
    }

    public function delete($id) {
        $this->kontak->id = (int)$id;
        if ($this->kontak->delete()) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kontak mitra berhasil dihapus'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menghapus kontak mitra'];
        }
        header("Location: index.php?action=contacts");
        exit;
    }
}