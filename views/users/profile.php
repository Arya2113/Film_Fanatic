<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once dirname(__DIR__, 2) . '/models/User.php';
require_once dirname(__DIR__, 2) . '/models/Favorite.php';

$userModel = new User();
$user = $userModel->find($_SESSION['user_id']);

$favoriteModel = new Favorite();

// Feedback
$edit_success = null;

// Handle edit profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    if ($userModel->update($_SESSION['user_id'], $new_name, $new_email)) {
        $edit_success = "Profil berhasil diubah!";
        $user = $userModel->find($_SESSION['user_id']); // refresh
    } else {
        $edit_success = "Gagal mengubah profil.";
    }
}

// Handle delete favorite
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_fav_id'])) {
    $fav_id = (int)$_POST['delete_fav_id'];
    $favoriteModel->delete($fav_id, $_SESSION['user_id']);
}

$favorites = $favoriteModel->userFavorites($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Profil User - Film Fanatic</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
  <!-- Header -->
  <header class="bg-blue-900 text-white p-4 shadow">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-wide">Film Fanatic</h1>
      <nav>
        <a href="../../index.php" class="mr-6 hover:text-blue-200 transition">Home</a>
        <a href="../users/profile.php" class="font-semibold underline">Profil</a>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex flex-col items-center py-12 px-2 min-h-[80vh] w-full">
    <!-- Profile Box -->
    <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-md mb-10 border-t-4 border-blue-900 mx-auto">
      <h2 class="text-2xl font-bold mb-6 text-blue-900 text-center">Profil User</h2>
      <?php if ($edit_success): ?>
        <div class="mb-3 text-center text-green-600"><?= $edit_success ?></div>
      <?php endif; ?>

      <!-- Info User Static (non-edit) -->
      <div id="profile-static">
        <div class="mb-5">
          <div class="flex items-center mb-2">
            <span class="font-semibold w-20">Nama</span>
            <span class="ml-2">: <?= htmlspecialchars($user['name']) ?></span>
          </div>
          <div class="flex items-center">
            <span class="font-semibold w-20">Email</span>
            <span class="ml-2">: <?= htmlspecialchars($user['email']) ?></span>
          </div>
        </div>
        <button type="button" onclick="toggleEditProfile(true)"
          class="w-full bg-blue-900 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-800 transition">Edit Profile</button>
        <a href="../../logout.php" class="mt-4 inline-block w-full text-center bg-red-600 text-white px-4 py-2 rounded-lg font-semibold shadow hover:bg-red-800 transition">Logout</a>
      </div>

      <!-- Form Edit Profile (hidden by default) -->
      <form method="POST" class="space-y-4 hidden" id="profile-edit-form">
        <div>
          <label class="block font-semibold mb-1">Nama</label>
          <input type="text" name="name" required value="<?= htmlspecialchars($user['name']) ?>"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block font-semibold mb-1">Email</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
          <button type="submit" name="edit_profile"
            class="w-1/2 bg-blue-900 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-800 transition">Simpan</button>
          <button type="button" onclick="toggleEditProfile(false)"
            class="w-1/2 bg-gray-300 text-gray-900 px-4 py-2 rounded-lg font-semibold hover:bg-gray-400 transition">Batal</button>
        </div>
      </form>
    </div>

    <!-- Favorite Movies List -->
    <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-2xl border-t-4 border-blue-900 mx-auto">
      <h3 class="text-xl font-bold mb-6 text-blue-900">Daftar Film Favorit</h3>
      <?php if (count($favorites) == 0): ?>
        <div class="text-gray-500 mb-2 italic">Belum ada film favorit.</div>
      <?php else: ?>
        <ul>
        <?php foreach ($favorites as $fav): ?>
          <li class="flex items-center py-3 border-b last:border-b-0 hover:bg-blue-50 transition group">
            <img src="<?= htmlspecialchars($fav['poster']) ?>"
                 class="w-14 h-20 object-cover rounded shadow mr-4 border border-gray-200"
                 alt="Poster">
            <div class="flex flex-col flex-1">
              <span class="font-semibold text-base text-blue-900"><?= htmlspecialchars($fav['title']) ?></span>
              <span class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($fav['year']) ?> &mdash; <?= htmlspecialchars($fav['genre']) ?></span>
            </div>
            <form method="POST" class="ml-3">
              <input type="hidden" name="delete_fav_id" value="<?= $fav['id'] ?>">
              <button type="submit"
                onclick="return confirm('Hapus film ini dari favorit?')"
                class="bg-red-500 hover:bg-red-700 text-white rounded px-3 py-1 text-xs font-semibold ml-2 transition">
                Hapus
              </button>
            </form>
          </li>
        <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </main>

  <script>
  function toggleEditProfile(editMode) {
    document.getElementById('profile-static').style.display = editMode ? 'none' : 'block';
    document.getElementById('profile-edit-form').style.display = editMode ? 'block' : 'none';
  }
  </script>
</body>
</html>
