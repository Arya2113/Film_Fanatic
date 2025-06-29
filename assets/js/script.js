document.addEventListener('DOMContentLoaded', function () {
    const apiKey = '776abd6a';

    const searchInput = document.getElementById('movie-search');
    const searchBtn = document.getElementById('search-btn');
    const moviesContainer = document.getElementById('movies-container');
    const top3Container = document.getElementById('top3-container');
    const recommendedContainer = document.getElementById('recommended-container');

    const top3IDs = ['tt0111161', 'tt0068646', 'tt0468569'];
    const recommendedIDs = ['tt1375666', 'tt0110912', 'tt0133093'];

    // Event: Search button click & Enter key
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', searchMovies);
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') searchMovies();
        });
    }

    // Movie search function
    function searchMovies() {
        const searchTerm = searchInput.value.trim();
        if (searchTerm === '') {
            moviesContainer.innerHTML = '<div class="text-center p-4">Please enter a movie title to search</div>';
            return;
        }
        moviesContainer.innerHTML = '<div class="text-center p-4">Loading movies...</div>';

        fetch(`https://www.omdbapi.com/?s=${encodeURIComponent(searchTerm)}&apikey=${apiKey}`)
            .then(response => response.json())
            .then(data => {
                if (data.Response === 'True') {
                    displayMovies(data.Search);
                } else {
                    moviesContainer.innerHTML = `<div class="text-center p-4">${data.Error || 'No movies found'}</div>`;
                }
            })
            .catch(error => {
                console.error('Error fetching movies:', error);
                moviesContainer.innerHTML = '<div class="text-center p-4">Error fetching movies. Please try again.</div>';
            });
    }

    // Render searched movies
    function displayMovies(movies) {
        if (!movies || movies.length === 0) {
            moviesContainer.innerHTML = '<div class="text-center p-4">No movies found</div>';
            return;
        }
        moviesContainer.innerHTML = movies.map(movie => {
            const posterUrl = movie.Poster !== 'N/A' ? movie.Poster : '/placeholder.svg?height=300&width=200';
            return `
                <div class="movie-card bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="${posterUrl}" alt="${movie.Title}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold mb-1">${movie.Title}</h3>
                        <p class="text-sm text-gray-600 mb-4">${movie.Type}, ${movie.Year}</p>
                        <div class="flex items-center justify-between">
                            <button class="bg-blue-900 text-white px-3 py-1 rounded text-sm hover:bg-blue-800"
                                onclick="getMovieDetails('${movie.imdbID}')">
                                View Details
                            </button>
                            <button class="add-fav-btn bg-pink-600 text-white px-3 py-1 rounded text-sm hover:bg-pink-800 transition"
                                title="Add to Favourite"
                                data-imdbid="${movie.imdbID}"
                                data-title="${movie.Title.replace(/"/g, '&quot;')}"
                                data-poster="${posterUrl}"
                                data-year="${movie.Year}"
                                data-genre="${movie.Type}">
                                Add to Favourite
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Event: Add to favourite
        document.querySelectorAll('.add-fav-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const csrf_token = document.getElementById('csrf_token').value;
                const imdb_id = btn.getAttribute('data-imdbid');
                const title = btn.getAttribute('data-title');
                const poster = btn.getAttribute('data-poster');
                const year = btn.getAttribute('data-year');
                const genre = btn.getAttribute('data-genre');
                fetch('favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `csrf_token=${encodeURIComponent(csrf_token)}&imdb_id=${encodeURIComponent(imdb_id)}&title=${encodeURIComponent(title)}&poster=${encodeURIComponent(poster)}&year=${encodeURIComponent(year)}&genre=${encodeURIComponent(genre)}`                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        btn.classList.add('bg-pink-800');
                        btn.textContent = 'Favorited!';
                        btn.disabled = true;
                        btn.title = 'Sudah jadi favorit!';
                    } else if (data.status === 'unauthorized') {
                        alert('Silakan login dulu untuk menambah ke favorit!');
                    }
                });
            });
        });
    }

    // Global: movie details modal
    window.getMovieDetails = function (imdbID) {
        fetch(`https://www.omdbapi.com/?i=${imdbID}&apikey=${apiKey}`)
            .then(response => response.json())
            .then(movie => {
                if (movie.Response === 'True') {
                    const modal = document.createElement('div');
                    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50';
                    const posterUrl = movie.Poster !== 'N/A' ? movie.Poster : '/placeholder.svg?height=300&width=200';
                    modal.innerHTML = `
                        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-screen overflow-y-auto">
                            <div class="p-4 flex justify-between items-start border-b">
                                <h2 class="text-xl font-bold">${movie.Title} (${movie.Year})</h2>
                                <button class="text-gray-500 hover:text-gray-700" id="close-modal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="p-4 flex flex-col md:flex-row">
                                <div class="md:w-1/3 mb-4 md:mb-0">
                                    <img src="${posterUrl}" alt="${movie.Title}" class="w-full rounded">
                                </div>
                                <div class="md:w-2/3 md:pl-4">
                                    <p class="mb-2"><span class="font-bold">Genre:</span> ${movie.Genre}</p>
                                    <p class="mb-2"><span class="font-bold">Director:</span> ${movie.Director}</p>
                                    <p class="mb-2"><span class="font-bold">Actors:</span> ${movie.Actors}</p>
                                    <p class="mb-2"><span class="font-bold">Plot:</span> ${movie.Plot}</p>
                                    <p class="mb-2"><span class="font-bold">Rating:</span> ${movie.imdbRating} / 10</p>
                                    <div class="mt-4">
                                        <a href="https://www.imdb.com/title/${movie.imdbID}" target="_blank"
                                            class="bg-yellow-500 text-black px-4 py-2 rounded inline-block hover:bg-yellow-400">
                                            View on IMDb
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    document.getElementById('close-modal').addEventListener('click', () => document.body.removeChild(modal));
                    modal.addEventListener('click', (e) => { if (e.target === modal) document.body.removeChild(modal); });
                }
            })
            .catch(error => {
                console.error('Error fetching movie details:', error);
            });
    };

    // Fetch and render Top 3 and Recommended movies
    function fetchAndRenderMovies(ids, container) {
        container.innerHTML = '<div class="text-center p-4">Loading...</div>';
        Promise.all(ids.map(id =>
            fetch(`https://www.omdbapi.com/?i=${id}&apikey=${apiKey}`).then(res => res.json())
        ))
        .then(movies => {
            container.innerHTML = movies.map(movie => {
                const posterUrl = movie.Poster !== 'N/A' ? movie.Poster : '/placeholder.svg?height=300&width=200';
                const ratingStars = movie.imdbRating >= 8.5 ? '★★★★★' : '★★★★☆';
                return `
                    <div class="movie-card bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="${posterUrl}" alt="${movie.Title}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold">${movie.Title}</h3>
                            <p class="text-sm text-gray-600">${movie.Genre}, ${movie.Year}</p>
                            <div class="flex items-center mt-2 text-yellow-500">${ratingStars}</div>
                        </div>
                    </div>
                `;
            }).join('');
        })
        .catch(error => {
            console.error('Error loading movies:', error);
            container.innerHTML = '<div class="text-center p-4">Error loading movies</div>';
        });
    }

    fetchAndRenderMovies(top3IDs, top3Container);
    fetchAndRenderMovies(recommendedIDs, recommendedContainer);

    // Mobile nav sidebar toggle
    const menuToggle = document.getElementById('menu-toggle');
    const menuClose = document.getElementById('menu-close');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && menuClose && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('-translate-x-full');
        });
        menuClose.addEventListener('click', () => {
            mobileMenu.classList.add('-translate-x-full');
        });
    }

    // AUTO-DISMISS FLASH MESSAGE (success/error) - FADE OUT
    setTimeout(function () {
        var flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.opacity = 0;
            setTimeout(function () {
                flash.style.display = 'none';
            }, 500); 
        }
        var err = document.getElementById('error-message');
        if (err) {
            err.style.opacity = 0;
            setTimeout(function () {
                err.style.display = 'none';
            }, 500);
        }
    }, 3000);
});
