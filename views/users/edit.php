<h2>Edit User</h2>
<form method="POST" action="index.php?action=update&id=<?= $user['id'] ?>">
    <input name="name" value="<?= $user['name'] ?>" required>
    <input name="email" value="<?= $user['email'] ?>" required>
    <button type="submit">Update</button>
</form>
