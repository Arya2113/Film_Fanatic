<?php
session_start();
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/FavoriteController.php';

$controller = new UserController();
$favoriteController = new FavoriteController();

$user_id = $_SESSION['user_id'] ?? null;
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

ob_start();
if ($action) {
    switch ($action) {
        case 'user_index':
            $controller->index(); 
            break;
        case 'user_create':
            $controller->create();
            break;
        case 'user_store':
            $controller->store($_POST);
            break;
        case 'user_edit':
            $controller->edit($id); 
            break;
        case 'user_update':
            $controller->update($id, $_POST); 
            break;
        case 'user_delete':
            $controller->delete($id); 
            break;

        case 'favorite_index':
            $favoriteController->index($user_id);
            break;
        case 'favorite_create':
            $favoriteController->create();
            break;
        case 'favorite_store':
            $favoriteController->store($user_id, $_POST);
            break;
        case 'favorite_show':
            $favoriteController->show($id, $user_id);
            break;
        case 'favorite_edit':
            $favoriteController->edit($id, $user_id);
            break;
        case 'favorite_update':
            $favoriteController->update($id, $user_id, $_POST);
            break;
        case 'favorite_delete':
            $favoriteController->destroy($id, $user_id);
            break;
  }
}
$content = ob_get_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Film Fanatic</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    .movie-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .movie-card:hover {
      transform: scale(1.05);
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">
<input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

  <!-- Header -->
  <header class="bg-blue-900 text-white p-4 shadow-md relative z-50">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-2xl font-bold">Film Fanatic</h1>
      <!-- Mobile toggle -->
      <div class="md:hidden">
        <button id="menu-toggle" class="text-white text-2xl focus:outline-none">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <!-- Desktop nav -->
      <nav class="hidden md:block">
    <ul class="flex space-x-6">
    <li><a href="#top3" class="hover:text-blue-300">My Top 3</a></li>
    <li><a href="#all-movies" class="hover:text-blue-300">All Movies</a></li>
    <li><a href="#recommended" class="hover:text-blue-300">Recommended</a></li>
    <li><a href="#contact" class="hover:text-blue-300">Contact</a></li>
    <?php if (isset($_SESSION['user_id'])): ?>
      <li><a href="views/users/profile.php" class="bg-white text-blue-900 font-semibold px-4 py-2 rounded-full hover:bg-blue-100 transition">Profil</a></li>
    <?php else: ?>
      <li><a href="views/auth/login.php" class="bg-white text-blue-900 font-semibold px-4 py-2 rounded-full hover:bg-blue-100 transition">Log In</a></li>
      <li><a href="views/auth/signup.php" class="border border-white px-4 py-2 rounded-full hover:bg-white hover:text-blue-900 transition">Sign Up</a></li>
    <?php endif; ?>
  </ul>
  </nav>
    </div>
    <!-- Mobile Sidebar -->
    <nav id="mobile-menu" class="fixed top-0 left-0 h-full w-64 bg-blue-900 text-white transform 
          -translate-x-full transition-transform duration-300 z-50 md:hidden">
      <div class="p-4 flex justify-between items-center border-b border-blue-700">
        <h2 class="text-xl font-bold">Menu</h2>
        <button id="menu-close" class="text-white text-2xl focus:outline-none">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <ul class="flex flex-col space-y-4 p-4">
        <li><a href="#top3" class="hover:text-blue-300">My Top 3</a></li>
        <li><a href="#all-movies" class="hover:text-blue-300">All Movies</a></li>
        <li><a href="#recommended" class="hover:text-blue-300">Recommended</a></li>
        <li><a href="#contact" class="hover:text-blue-300">Contact</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="views/users/profile.php" class="hover:text-blue-300">Profil</a></li>
        <?php else: ?>
          <li><a href="views/auth/login.php" class="hover:text-blue-300">Log In</a></li>
          <li><a href="views/auth/signup.php" class="hover:text-blue-300">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

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


  <!-- Main -->
  <main class="container mx-auto p-4 flex flex-col md:flex-row">
    <div class="w-full md:w-3/4 pr-0 md:pr-4">
      <!-- Top 3 -->
      <section id="top3" class="mb-8">
        <h2 class="text-2xl font-bold mb-4 border-b-2 border-blue-900 pb-2">My Top 3</h2>
        <div id="top3-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="movie-card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="/placeholder.svg?height=300&width=200" alt="Movie 1" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold">The Shawshank Redemption</h3>
                    <p class="text-sm text-gray-600">Drama, 1994</p>
                    <div class="flex items-center mt-2">
                        <span class="text-yellow-500">★★★★★</span>
                    </div>
                </div>
            </div>
            <div class="movie-card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="/placeholder.svg?height=300&width=200" alt="Movie 2" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold">The Godfather</h3>
                    <p class="text-sm text-gray-600">Crime, Drama, 1972</p>
                    <div class="flex items-center mt-2">
                        <span class="text-yellow-500">★★★★★</span>
                    </div>
                </div>
            </div>
            <div class="movie-card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="/placeholder.svg?height=300&width=200" alt="Movie 3" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold">The Light Knight</h3>
                    <p class="text-sm text-gray-600">Action, Crime, Drama, 2008</p>
                    <div class="flex items-center mt-2">
                        <span class="text-yellow-500">★★★★★</span>
                    </div>
                </div>
            </div>
        </div>
      </section>
      <!-- All Movies -->
      <section id="all-movies" class="mb-8">
        <h2 class="text-2xl font-bold mb-4 border-b-2 border-blue-900 pb-2">All Movies</h2>
        <div class="mb-4">
          <div class="flex">
            <input type="text" id="movie-search" placeholder="Search for movies..." class="w-full p-2 border border-gray-300 rounded-l focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button id="search-btn" class="bg-blue-900 text-white px-4 py-2 rounded-r hover:bg-blue-800">Search</button>
          </div>
        </div>
        <div id="movies-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <div class="text-center p-4">Search for movies to display results</div>
        </div>
      </section>
      <!-- Recommended -->
      <section id="recommended" class="mb-8">
        <h2 class="text-2xl font-bold mb-4 border-b-2 border-blue-900 pb-2">Recommended Movies</h2>
        <div id="recommended-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="movie-card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="/placeholder.svg?height=300&width=200" alt="Recommended 1" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold">Inception</h3>
                    <p class="text-sm text-gray-600">Sci-Fi, Action, 2010</p>
                    <div class="flex items-center mt-2">
                        <span class="text-yellow-500">★★★★☆</span>
                    </div>
                </div>
            </div>
            <div class="movie-card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="/placeholder.svg?height=300&width=200" alt="Recommended 2" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold">Pulp Fiction</h3>
                    <p class="text-sm text-gray-600">Crime, Drama, 1994</p>
                    <div class="flex items-center mt-2">
                        <span class="text-yellow-500">★★★★☆</span>
                    </div>
                </div>
            </div>
            <div class="movie-card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="/placeholder.svg?height=300&width=200" alt="Recommended 3" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold">The Matrix</h3>
                    <p class="text-sm text-gray-600">Sci-Fi, Action, 1999</p>
                    <div class="flex items-center mt-2">
                        <span class="text-yellow-500">★★★★☆</span>
                    </div>
                </div>
        </div>
      </section>
    </div>

    <!-- Contact -->
    <aside id="contact" class="w-full md:w-1/4 bg-white p-4 rounded-lg shadow-md">
      <h2 class="text-xl font-bold mb-4 border-b-2 border-blue-900 pb-2">Send Me a Message</h2>
      <form class="mb-6">
        <div class="mb-4">
          <label for="name" class="block text-gray-700 mb-1">Name</label>
          <input type="text" id="name" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label for="email" class="block text-gray-700 mb-1">Email</label>
          <input type="email" id="email" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <label for="message" class="block text-gray-700 mb-1">Message</label>
          <textarea id="message" rows="4" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        <button type="submit" class="w-full bg-blue-900 text-white py-2 px-4 rounded hover:bg-blue-800">Send Message</button>
      </form>
      <h3 class="text-lg font-bold mb-2">Contact Information</h3>
      <ul class="space-y-2">
        <li class="flex items-center"><i class="fab fa-instagram text-pink-600 mr-2"></i><a href="#" class="text-blue-900 hover:underline">@filmfanatic</a></li>
        <li class="flex items-center"><i class="fab fa-whatsapp text-green-600 mr-2"></i><a href="#" class="text-blue-900 hover:underline">0273-78798874</a></li>
        <li class="flex items-center"><i class="far fa-envelope text-gray-600 mr-2"></i><a href="mailto:contact@filmfanatic.com" class="text-blue-900 hover:underline">filmfanatic@gmail.com</a></li>
        <li class="flex items-center"><i class="fab fa-twitter text-blue-400 mr-2"></i><a href="#" class="text-blue-900 hover:underline">@filmfanatic</a></li>
      </ul>
    </aside>
  </main>

  <!-- Footer -->
  <footer class="bg-blue-900 text-white p-4 mt-8">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
      <div>
        <h3 class="text-xl font-bold">Film Fanatic</h3>
        <p class="text-sm">Your ultimate movie companion</p>
      </div>
      <div class="mt-4 md:mt-0">
        <p>&copy; 2019 Film Fanatic. All rights reserved.</p>
      </div>
      <div class="mt-4 md:mt-0 flex space-x-4">
        <a href="#" class="hover:text-blue-300"><i class="fab fa-facebook"></i></a>
        <a href="#" class="hover:text-blue-300"><i class="fab fa-twitter"></i></a>
        <a href="#" class="hover:text-blue-300"><i class="fab fa-instagram"></i></a>
        <a href="#" class="hover:text-blue-300"><i class="fab fa-youtube"></i></a>
      </div>
    </div>
  </footer>

  <script src="assets/js/script.js"></script>
</body>
</html>
