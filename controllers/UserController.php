<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User(); // tanpa parameter
    }

    public function index() {
        $users = $this->user->all();
        include '../views/users/index.php';
    }

    public function show($id) {
        $user = $this->user->find($id);
        include '../views/users/show.php';
    }

    public function create() {
        include '../views/users/create.php';
    }

    public function store($data) {
        $this->user->create($data['name'], $data['email'], $data['password']); // pastikan konsisten field-nya
        header("Location: index.php");
        exit;
    }

    public function edit($id) {
        $user = $this->user->find($id);
        include '../views/users/edit.php';
    }

    public function update($id, $data) {
        $this->user->update($id, $data['name'], $data['email']);
        header("Location: index.php");
        exit;
    }

    public function destroy($id) {
        $this->user->delete($id);
        header("Location: index.php");
        exit;
    }
}
?>
