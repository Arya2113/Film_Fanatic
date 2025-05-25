<?php
require_once dirname(__DIR__, 2) . '/models/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userModel = new User();

    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $userModel->getByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header('Location: /FilmFanatics/index.php');
        exit;
    } else {
        $error = "Email atau password salah!";
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
      <a href="FilmFanatics\index.php" class="text-2xl font-bold flex items-center">
        <i class="fas fa-film mr-2"></i>
        Film Fanatic
      </a>
    </div>
  </header>

  <main class="flex-grow container mx-auto p-4 flex justify-center items-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-sm">
      <h1 class="text-2xl font-bold text-blue-900 mb-6 text-center">Log In</h1>
      <form action="#" method="POST" class="space-y-4">
        <input type="email" name="email" placeholder="Email" 
               class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        <input type="password" name="password" placeholder="Password" 
               class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required />
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
