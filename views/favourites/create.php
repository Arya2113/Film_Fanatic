<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<h2>Tambah Film Favorit Manual</h2>
<form action="index.php?action=favorite_store" method="post">
    IMDB ID: <input type="text" name="imdb_id" required><br>
    Judul: <input type="text" name="title" required><br>
    Poster URL: <input type="text" name="poster"><br>
    Tahun: <input type="text" name="year"><br>
    Genre: <input type="text" name="genre"><br>
    Label (opsional): <input type="text" name="label"><br>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <button type="submit">Simpan</button>
</form>
