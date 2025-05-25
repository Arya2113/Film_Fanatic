<h2>Edit Label Film Favorit</h2>
<form action="index.php?action=favorite_update&id=<?= $favorite['id'] ?>" method="post">
    <strong><?= htmlspecialchars($favorite['title']) ?></strong><br>
    Label: <input type="text" name="label" value="<?= htmlspecialchars($favorite['label'] ?? '') ?>"><br>
    <button type="submit">Update</button>
</form>
