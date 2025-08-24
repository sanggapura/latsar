<?php
class Kontak {
    private $conn;
    private $table = "kontak_mitra";

    public $id;
    public $nama_perusahaan;
    public $nama_pic;
    public $nomor_telp;
    public $alamat_email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (nama_perusahaan, nama_pic, nomor_telp, alamat_email) VALUES (:nama_perusahaan, :nama_pic, :nomor_telp, :alamat_email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nama_perusahaan", $this->nama_perusahaan);
        $stmt->bindParam(":nama_pic", $this->nama_pic);
        $stmt->bindParam(":nomor_telp", $this->nomor_telp);
        $stmt->bindParam(":alamat_email", $this->alamat_email);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " SET nama_perusahaan=:nama_perusahaan, nama_pic=:nama_pic, nomor_telp=:nomor_telp, alamat_email=:alamat_email WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nama_perusahaan", $this->nama_perusahaan);
        $stmt->bindParam(":nama_pic", $this->nama_pic);
        $stmt->bindParam(":nomor_telp", $this->nomor_telp);
        $stmt->bindParam(":alamat_email", $this->alamat_email);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}