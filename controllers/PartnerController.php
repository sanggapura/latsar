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
        $this->partner->name = $data['name'];
        $this->partner->email = $data['email'];
        $this->partner->phone = $data['phone'];
        $this->partner->create();
        header("Location: index.php");
    }

    public function editForm($id) {
        $partner = $this->partner->getById($id);
        include __DIR__ . "/../views/partner/edit.php";
    }

    public function update($data) {
        $this->partner->id = $data['id'];
        $this->partner->name = $data['name'];
        $this->partner->email = $data['email'];
        $this->partner->phone = $data['phone'];
        $this->partner->update();
        header("Location: index.php");
    }

    public function delete($id) {
        $this->partner->id = $id;
        $this->partner->delete();
        header("Location: index.php");
    }
}
