<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'unauthorized']);
    exit;
}
require_once __DIR__ . '/models/Favorite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $imdb_id = trim($_POST['imdb_id'] ?? '');
    $title   = trim($_POST['title'] ?? '');
    $poster  = trim($_POST['poster'] ?? '');
    $year    = trim($_POST['year'] ?? '');
    $genre   = trim($_POST['genre'] ?? '');

    $required_fields = [
        'imdb_id' => $imdb_id,
        'title'   => $title,
        'year'    => $year,
        'genre'   => $genre
    ];

    foreach ($required_fields as $key => $value) {
        if ($value === '') {
            echo json_encode(['status' => 'error', 'message' => ucfirst($key) . ' wajib diisi!']);
            exit;
        }
    }

    $favoriteModel = new Favorite();
    if ($favoriteModel->isFavorite($user_id, $imdb_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Film sudah ada di daftar favorit.']);
        exit;
    }

    $success = $favoriteModel->create($user_id, $imdb_id, $title, $poster, $year, $genre);

    echo json_encode(['status' => $success ? 'success' : 'error']);
}
?>
