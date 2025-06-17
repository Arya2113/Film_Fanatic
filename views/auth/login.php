<?php
require_once dirname(__DIR__, 2) . '/models/User.php';
session_start();

if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    $userModel = new User();
    $token = $_COOKIE['rememberme'];
    $user = $userModel->getByRememberToken($token);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['flash_message'] = "Auto-login berhasil, selamat datang kembali, " . htmlspecialchars($user['name']) . "!";
        header('Location: /FilmFanatics/index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userModel = new User();

    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $user = $userModel->getByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['flash_message'] = "Login berhasil! Selamat datang, " . htmlspecialchars($user['name']) . ".";

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $userModel->updateRememberToken($user['id'], $token);
            setcookie('rememberme', '', time() - 3600, '/');
        }

        header('Location: /FilmFanatics/index.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Email atau password salah!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Film Fanatic</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
  <header class="bg-blue-900 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <a href="/FilmFanatics/index.php" class="text-2xl font-bold flex items-center">
        <i class="fas fa-film mr-2"></i>
        Film Fanatic
      </a>
    </div>
  </header>

  <main class="flex-grow container mx-auto p-4 flex justify-center items-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-sm">
      <h1 class="text-2xl font-bold text-blue-900 mb-6 text-center">Log In</h1>
      
      <?php if (isset($_SESSION['flash_message'])): ?>
        <div id="flash-message" class="mb-4 px-4 py-3 rounded bg-green-100 border border-green-300 text-green-800 max-w-lg mx-auto mt-6 text-center transition-opacity duration-500">
          <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['error_message'])): ?>
        <div id="error-message" class="mb-4 px-4 py-3 rounded bg-red-100 border border-red-300 text-red-800 max-w-lg mx-auto mt-6 text-center transition-opacity duration-500">
          <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
      <?php endif; ?>

      <form action="#" method="POST" class="space-y-4">
        <input type="email" name="email" placeholder="Email" 
               class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        <input type="password" name="password" placeholder="Password" 
               class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        <div class="flex items-center">
          <input type="checkbox" name="remember" id="remember" class="mr-2">
          <label for="remember" class="text-sm text-gray-700">Remember Me</label>
        </div>
        <button type="submit" 
                class="w-full bg-blue-900 text-white py-2 rounded hover:bg-blue-800 transition">
          Login
        </button>
      </form>
      <p class="text-center text-sm mt-4">
        Don't have an account? 
        <a href="signup.php" class="text-blue-700 hover:underline">Register</a>
      </p>
    </div>
  </main>

  <footer class="bg-blue-900 text-white p-4">
    <div class="container mx-auto text-center">
      <p>&copy; 2019 Film Fanatic. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
