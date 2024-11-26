<?php
session_start();
include_once 'functions.php';
include_once 'db.php';


if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    
$welcomeMessage = isset($_SESSION['username']) ? "Welcome " . htmlspecialchars($_SESSION['username']) : "Welcome Guest";

    // Fetch all songs
    $db = getDbConnection();
    $songQuery = "SELECT s.song_id, s.title, s.file_path, s.cover_image, a.title AS album_title, ar.name AS artist_name 
                  FROM songs s 
                  JOIN albums a ON s.album_id = a.album_id 
                  JOIN artists ar ON s.artist_id = ar.artist_id";

    $songStmt = $db->prepare($songQuery);
    $songStmt->execute();
    $songs = $songStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch liked songs for the user
    $likedSongs = getUserLikedSongs($userId);

    // Fetch all albums
    $albumQuery = "SELECT album_id, title, cover_image, release_date FROM albums";
    $albumStmt = $db->prepare($albumQuery);
    $albumStmt->execute();
    $albums = $albumStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Please log in to view songs and albums.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Database System</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <div class="nav-toggle" onclick="toggleNav()">☰</div>
    <div class="sidenav">
        <div class="profile-section">
            <a href="profile.php" class="profile-link">
                <span class="hello-user"><?php echo "Welcome " . htmlspecialchars($_SESSION['username']); ?></span>
            </a>
        </div>

        <!-- Search form -->
        <form action="search_results.php" method="get">
            <input type="text" name="search_query" placeholder="Search for songs..." required>
            <button type="submit">Search</button>
        </form>

        <h2>Your Library</h2>
        <div class="playlist-section">
            <a href="liked-songs.php">Liked Songs</a>
            <a href="create-playlist.php">Create Playlist</a>
            <a href="playlist_management.php">My Playlists</a>
        </div>

        <div class="logout-section">
            <a href="login.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1><?php echo "Welcome " . htmlspecialchars($_SESSION['username']); ?></h1>
        <p>Explore and enjoy your favorite music!</p>

        <!-- Display Albums -->
        <div class="albums-container">
            <h2>Albums</h2>
            <?php if (!empty($albums)): ?>
                <div class="albums-grid">
                    <?php foreach ($albums as $album): ?>
                        <div class="album-box">
                            <a href="album.php?album_id=<?php echo $album['album_id']; ?>">
                                <img src="images/<?php echo htmlspecialchars($album['cover_image']); ?>" alt="Album Cover" class="album-cover">
                                <p class="album-title"><?php echo htmlspecialchars($album['title']); ?></p>
                            </a>
                            <p class="album-release-date">Released: <?php echo htmlspecialchars($album['release_date']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No albums available.</p>
            <?php endif; ?>
        </div>

       
          <div class="songs-container-h2">
            <h2>Songs</h2>
           </div>
        <div class="songs-container">
            <?php foreach ($songs as $song): ?>
                <div class="song-box">
                    <img src="images/<?php echo htmlspecialchars($song['cover_image']); ?>" alt="Cover Image" class="song-cover">
                    <p class="song-info">
                        <?php echo htmlspecialchars($song['title']) . " - " . htmlspecialchars($song['artist_name']) . " (" . htmlspecialchars($song['album_title']) . ")"; ?>
                    </p>
                    <button class="play-button" onclick="redirectToTrackPage(<?php echo $song['song_id']; ?>)">Play Song</button>
                    
                    <?php
                    // Check if the song is liked by the user
                    $liked = in_array($song['song_id'], $likedSongs) ? 'Unlike' : 'Like';
                    ?>
                    <form action="like_unlike_song.php" method="POST" style="display:inline;">
                        <input type="hidden" name="song_id" value="<?php echo $song['song_id']; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                        <button type="submit" class="like-button"><?php echo $liked; ?></button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function toggleNav() {
            var sidenav = document.querySelector(".sidenav");
            sidenav.classList.toggle("active");
        }

        function redirectToTrackPage(songId) {
            window.location.href = "track.php?song_id=" + songId + "&autoplay=1";
        }
    </script>
</body>
</html>