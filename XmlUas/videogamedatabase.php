<?php
$api_key = '3f16d317db484fa78a422f583abee4a0';
$base_url = 'https://api.rawg.io/api';

// Fungsi sederhana untuk mengambil data game
function getGames($page = 1, $search = '') {
    global $api_key, $base_url;
    $url = "$base_url/games?key=$api_key&page=$page&page_size=12";
    
    if (!empty($search)) {
        $url .= "&search=" . urlencode($search);
    }
    
    $json = file_get_contents($url);
    return json_decode($json, true)['results'] ?? [];
}

// Fungsi untuk mengambil detail game
function getGameDetails($gameId) {
    global $api_key, $base_url;
    $url = "$base_url/games/$gameId?key=$api_key";
    $json = file_get_contents($url);
    return json_decode($json, true);
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'search') {
        $games = getGames(1, $_GET['query'] ?? '');
        echo json_encode($games);
        exit;
    }
    
    if ($_GET['action'] === 'getDetails') {
        $details = getGameDetails($_GET['gameId']);
        echo json_encode($details);
        exit;
    }
    
    if ($_GET['action'] === 'getPage') {
        $games = getGames($_GET['page'] ?? 1);
        echo json_encode($games);
        exit;
    }
}

// Initial games data
$games = getGames();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Game Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #212529; color: white; }
        .game-card { margin-bottom: 20px; transition: transform 0.3s; }
        .game-card:hover { transform: scale(1.05); }
        .game-image { height: 200px; object-fit: cover; }
        .rating-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .pagination .page-link {
            background-color: #212529;
            border-color: #6c757d;
            color: white;
        }
        .pagination .page-link:hover {
            background-color: #343a40;
        }
        .pagination .active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark border-bottom border-secondary mb-4">
        <div class="container">
            <a class="navbar-brand">Video Game Database</a>
            <form class="d-flex" id="searchForm">
                <input class="form-control me-2" type="search" placeholder="Cari game..." id="searchInput">
                <button class="btn btn-outline-light" type="submit">Cari</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="row" id="gamesContainer">
            <?php foreach ($games as $game): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card game-card bg-dark border-secondary">
                        <img src="<?= $game['background_image'] ?>" class="card-img-top game-image" alt="<?= $game['name'] ?>">
                        <span class="rating-badge">★ <?= $game['rating'] ?></span>
                        <div class="card-body">
                            <h5 class="card-title"><?= $game['name'] ?></h5>
                            <p class="card-text">Rilis: <?= $game['released'] ?></p>
                            <button class="btn btn-primary btn-sm" onclick="showGameDetails(<?= $game['id'] ?>)">Detail</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination" id="pagination">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <li class="page-item <?= $i === 1 ? 'active' : '' ?>">
                            <button class="page-link" onclick="loadPage(<?= $i ?>)"><?= $i ?></button>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="gameModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Detail Game</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk memuat halaman
        function loadPage(page) {
            fetch(`videogamedatabase.php?action=getPage&page=${page}`)
                .then(response => response.json())
                .then(games => {
                    const container = document.getElementById('gamesContainer');
                    container.innerHTML = games.map(game => `
                        <div class="col-md-4 col-lg-3">
                            <div class="card game-card bg-dark border-secondary">
                                <img src="${game.background_image}" class="card-img-top game-image" alt="${game.name}">
                                <span class="rating-badge">★ ${game.rating}</span>
                                <div class="card-body">
                                    <h5 class="card-title">${game.name}</h5>
                                    <p class="card-text">Rilis: ${game.released}</p>
                                    <button class="btn btn-primary btn-sm" onclick="showGameDetails(${game.id})">Detail</button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                    
                    // Update pagination active state
                    document.querySelectorAll('.page-item').forEach(item => item.classList.remove('active'));
                    document.querySelector(`.pagination li:nth-child(${page})`).classList.add('active');
                });
        }

        // Fungsi untuk menampilkan detail game
        function showGameDetails(gameId) {
            fetch(`videogamedatabase.php?action=getDetails&gameId=${gameId}`)
                .then(response => response.json())
                .then(game => {
                    document.getElementById('modalContent').innerHTML = `
                        <img src="${game.background_image}" class="img-fluid mb-3" alt="${game.name}">
                        <h3>${game.name}</h3>
                        <p>${game.description_raw}</p>
                        <p><strong>Rating:</strong> ★ ${game.rating}/5</p>
                        <p><strong>Rilis:</strong> ${game.released}</p>
                    `;
                    new bootstrap.Modal(document.getElementById('gameModal')).show();
                });
        }

        // Handle pencarian
        document.getElementById('searchForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const query = document.getElementById('searchInput').value;
            
            fetch(`videogamedatabase.php?action=search&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(games => {
                    const container = document.getElementById('gamesContainer');
                    if (games.length === 0) {
                        container.innerHTML = `
                            <div class="col-12 text-center">
                                <div class="alert alert-info">Tidak ada hasil ditemukan untuk "${query}"</div>
                            </div>
                        `;
                        return;
                    }
                    
                    container.innerHTML = games.map(game => `
                        <div class="col-md-4 col-lg-3">
                            <div class="card game-card bg-dark border-secondary">
                                <img src="${game.background_image}" class="card-img-top game-image" alt="${game.name}">
                                <span class="rating-badge">★ ${game.rating}</span>
                                <div class="card-body">
                                    <h5 class="card-title">${game.name}</h5>
                                    <p class="card-text">Rilis: ${game.released}</p>
                                    <button class="btn btn-primary btn-sm" onclick="showGameDetails(${game.id})">Detail</button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                });
        });
    </script>
</body>
</html>
