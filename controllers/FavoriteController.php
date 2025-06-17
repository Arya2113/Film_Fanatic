<?php
require_once __DIR__ . '/../models/Favorite.php';

class FavoriteController {
    private $favoriteModel;

    public function __construct() {
        $this->favoriteModel = new Favorite();
    }

    public function store($user_id, $data) {
        session_start();

        if (
            !isset($data['csrf_token']) ||
            $data['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')
        ) {
            $_SESSION['fav_errors'] = ["CSRF token tidak valid!"];
            header("Location: ../views/favorite/create.php");
            exit;
        }
        $errors = [];

        if (empty($data['imdb_id']))  $errors[] = "ID film tidak boleh kosong.";
        if (empty($data['title']))    $errors[] = "Judul film wajib diisi.";
        if (empty($data['poster']))   $errors[] = "Poster film wajib diisi.";
        if (empty($data['year']))     $errors[] = "Tahun film wajib diisi.";
        if (empty($data['genre']))    $errors[] = "Genre wajib diisi.";

        if ($this->favoriteModel->isFavorite($user_id, $data['imdb_id'])) {
            $errors[] = "Film ini sudah ada di daftar favorit Anda.";
        }

        if (!empty($errors)) {
            $_SESSION['fav_errors'] = $errors;
            header("Location: ../views/favorite/create.php");
            exit;
        }

        $this->favoriteModel->create(
            $user_id,
            $data['imdb_id'],
            $data['title'],
            $data['poster'],
            $data['year'],
            $data['genre'],
            $data['label'] ?? null
        );

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: index.php?action=favorite_index");
    }

    public function update($id, $user_id, $data) {
        session_start();

        if (
            !isset($data['csrf_token']) ||
            $data['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')
        ) {
            $_SESSION['fav_errors'] = ["CSRF token tidak valid!"];
            header("Location: ../views/favorite/edit.php?id=$id");
            exit;
        }

        $errors = [];

        if (isset($data['label']) && strlen($data['label']) > 255) {
            $errors[] = "Label maksimal 255 karakter.";
        }
        if (!empty($errors)) {
            $_SESSION['fav_errors'] = $errors;
            header("Location: ../views/favorite/edit.php?id=$id");
            exit;
        }

        $this->favoriteModel->update($id, $user_id, $data['label']);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: index.php?action=favorite_index");
    }
}
?>
