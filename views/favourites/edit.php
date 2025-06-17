<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<h2>Edit Label Film Favorit</h2>
<form action="index.php?action=favorite_update&id=<?= $favorite['id'] ?>" method="post">
    <strong><?= htmlspecialchars($favorite['title']) ?></strong><br>
    Label: <input type="text" name="label" value="<?= htmlspecialchars($favorite['label'] ?? '') ?>"><br>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <button type="submit">Update</button>
</form>
