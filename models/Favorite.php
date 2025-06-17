<?php
require_once __DIR__ . '/../database/config.php';

class Favorite {
    private $conn;

    public function __construct() {
        $this->conn = getConnection(); 
    }

    public function all($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM favorite_movies WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id, $user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM favorite_movies WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($user_id, $imdb_id, $title, $poster, $year, $genre, $label = null) {
        $stmt = $this->conn->prepare("INSERT IGNORE INTO favorite_movies (user_id, imdb_id, title, poster, year, genre, label) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, $imdb_id, $title, $poster, $year, $genre, $label]);
    }

    public function update($id, $user_id, $label) {
        $stmt = $this->conn->prepare("UPDATE favorite_movies SET label = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$label, $id, $user_id]);
    }

    public function delete($id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM favorite_movies WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }

    public function isFavorite($user_id, $imdb_id) {
        $stmt = $this->conn->prepare("SELECT 1 FROM favorite_movies WHERE user_id = ? AND imdb_id = ?");
        $stmt->execute([$user_id, $imdb_id]);
        return $stmt->fetch() ? true : false;
    }
    
    public function userFavorites($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM favorite_movies WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteByImdb($user_id, $imdb_id) {
        $stmt = $this->conn->prepare("DELETE FROM favorite_movies WHERE user_id = ? AND imdb_id = ?");
        return $stmt->execute([$user_id, $imdb_id]);
    }

}
?>
