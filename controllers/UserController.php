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

    public function login($data) {
    session_start();
    $email = $data['email'];
    $password = $data['password'];
    $remember = isset($data['remember']);

    $user = $this->user->getByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->user->updateRememberToken($user['id'], $token);
            setcookie('rememberme', '', time() - 3600, '/');
        }
        header("Location: dashboard.php");
        exit;
        } else {
        // Kirim error ke view atau redirect ke login dengan pesan error
        header("Location: login.php?error=1");
        exit;
        }
    }

    // Auto-login jika ada cookie remember me
    public function autoLoginWithRememberMe() {
    session_start();
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
        $token = $_COOKIE['rememberme'];
        $user = $this->user->getByRememberToken($token);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            // Redirect ke dashboard atau halaman yang diinginkan
            header("Location: dashboard.php");
            exit;
        }
        }
    }

    // Logout
    public function logout() {
    session_start();
    if (isset($_SESSION['user_id'])) {
        $this->user->clearRememberToken($_SESSION['user_id']); // hapus token dari database
    }
    session_destroy();
    setcookie('rememberme', '', time() - 3600, '/');
    header("Location: login.php");
    exit;
    }

}
?>
