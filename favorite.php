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
    $user_id = $_SESSION['user_id'];
    $imdb_id = $_POST['imdb_id'];
    $title = $_POST['title'];
    $poster = $_POST['poster'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];

    $favoriteModel = new Favorite();
    $success = $favoriteModel->create($user_id, $imdb_id, $title, $poster, $year, $genre);

    echo json_encode(['status' => $success ? 'success' : 'error']);
}
?>
