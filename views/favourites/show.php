<h2>Detail Film Favorit</h2>
<p><img src="<?= htmlspecialchars($favorite['poster']) ?>" style="height:100px"></p>
<p>Judul: <?= htmlspecialchars($favorite['title']) ?></p>
<p>Tahun: <?= htmlspecialchars($favorite['year']) ?></p>
<p>Genre: <?= htmlspecialchars($favorite['genre']) ?></p>
<p>Label: <?= htmlspecialchars($favorite['label'] ?? '-') ?></p>
<a href="index.php?action=favorite_index">Kembali</a>
