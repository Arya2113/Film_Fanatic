<h2 class="text-xl font-bold mb-2">Film Favorit Saya</h2>
<a href="index.php?action=favorite_create" class="bg-blue-900 text-white px-4 py-2 rounded">Tambah Manual</a>
<table class="table-auto w-full mt-4">
    <tr>
        <th>Poster</th><th>Judul</th><th>Tahun</th><th>Genre</th><th>Label</th><th>Aksi</th>
    </tr>
    <?php foreach ($favorites as $fav): ?>
    <tr>
        <td><img src="<?= htmlspecialchars($fav['poster']) ?>" style="height:60px"></td>
        <td><?= htmlspecialchars($fav['title']) ?></td>
        <td><?= htmlspecialchars($fav['year']) ?></td>
        <td><?= htmlspecialchars($fav['genre']) ?></td>
        <td><?= htmlspecialchars($fav['label'] ?? '-') ?></td>
        <td>
            <a href="index.php?action=favorite_show&id=<?= $fav['id'] ?>">Show</a> |
            <a href="index.php?action=favorite_edit&id=<?= $fav['id'] ?>">Edit</a> |
            <a href="index.php?action=favorite_delete&id=<?= $fav['id'] ?>" onclick="return confirm('Yakin?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
