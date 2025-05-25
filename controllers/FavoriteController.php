<?php
require_once __DIR__ . '/../models/Favorite.php';

class FavoriteController {
    private $favorite;

    public function __construct() {
        $this->favorite = new Favorite();
    }

    // Read (lihat semua favorit user)
    public function index($user_id) {
        $favorites = $this->favorite->all($user_id);
        include __DIR__ . '/../views/favorites/index.php';
    }

    // Show form create
    public function create() {
        include __DIR__ . '/../views/favorites/create.php';
    }

    // Store data baru
    public function store($user_id, $data) {
        $this->favorite->create(
            $user_id,
            $data['imdb_id'],
            $data['title'],
            $data['poster'],
            $data['year'],
            $data['genre'],
            $data['label'] ?? null
        );
        header("Location: index.php?action=favorite_index");
        exit;
    }

    // Show detail favorit
    public function show($id, $user_id) {
        $favorite = $this->favorite->find($id, $user_id);
        include __DIR__ . '/../views/favorites/show.php';
    }

    // Show form edit
    public function edit($id, $user_id) {
        $favorite = $this->favorite->find($id, $user_id);
        include __DIR__ . '/../views/favorites/edit.php';
    }

    // Update data
    public function update($id, $user_id, $data) {
        $this->favorite->update($id, $user_id, $data['label'] ?? '');
        header("Location: index.php?action=favorite_index");
        exit;
    }

    // Delete
    public function destroy($id, $user_id) {
        $this->favorite->delete($id, $user_id);
        header("Location: index.php?action=favorite_index");
        exit;
    }
}
?>
