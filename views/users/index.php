<h2>User List</h2>
<a href="index.php?action=create">Add User</a>
<ul>
    <?php while ($row = $users->fetch_assoc()): ?>
        <li>
            <?= $row['name'] ?> (<?= $row['email'] ?>)
            <a href="index.php?action=show&id=<?= $row['id'] ?>">View</a>
            <a href="index.php?action=edit&id=<?= $row['id'] ?>">Edit</a>
            <a href="index.php?action=delete&id=<?= $row['id'] ?>">Delete</a>
        </li>
    <?php endwhile; ?>
</ul>
